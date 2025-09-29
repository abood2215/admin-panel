<?php

namespace App\Jobs;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Smalot\PdfParser\Parser; // composer require smalot/pdfparser

class ExtractQuestionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Document $document, public string $lang = 'ar') {}

    public function handle(): void
    {
        $this->document->update(['extract_status' => 'processing']);

        try {
            $path = storage_path('app/'.$this->document->file_path);
            $parser = new Parser();
            $pdf    = $parser->parseFile($path);
            $text   = $pdf->getText();

            // معالجة أوّلية: توحيد الهمزات/المسافات والعلامات
            $norm = preg_replace('/[^\S\r\n]+/u', ' ', $text);
            $norm = preg_replace('/\h+/u', ' ', $norm);
            $norm = str_replace(["\xC2\xA0"], ' ', $norm); // NBSP

            // اختَر نمط التقطيع بحسب اللغة
            $questions = $this->lang === 'ar'
                ? $this->extractAr($norm)
                : $this->extractEn($norm);

            if (empty($questions)) {
                throw new \RuntimeException('no-questions-detected');
            }

            // خزن الأسئلة
            foreach ($questions as $q) {
                $this->document->questions()->create([
                    'question'        => $q['q'],
                    'options'         => $q['opts'],
                    'correct_answer'  => $q['correct'] ?? null,
                    'language'        => $this->lang,
                ]);
            }

            $this->document->update(['extract_status' => 'done']);
        } catch (\Throwable $e) {
            // فشل: نوثّق السبب و نترك الإدارة تُعيد الاستخراج أو تضيف يدويًا
            $this->document->update([
                'extract_status' => 'failed',
                'extract_error'  => $e->getMessage(),
            ]);
        }
    }

    /** أنماط عربية تستوعب أ/ب/ج/د، شرطة أو قوس، وأرقام عربية أو هندية */
    private function extractAr(string $t): array
    {
        // قَطِّع الأسئلة على أساس بداية سطر فيها رقم ثم نقطة
        $blocks = preg_split('/\R\s*(?:\d+|[٠-٩]+)\s*[)\.]\s*/u', $t, flags: PREG_SPLIT_NO_EMPTY);

        $out = [];
        foreach ($blocks as $block) {
            // التقط السؤال حتى أول خيار
            // أمثلة خيارات: "أ -", "أ)", "A)"; نركّز العربي هنا
            if (!preg_match('/^(.*?)(?:\R| )((?:[أإا]\s*[)\-]|أ\s?:|أ\)))/u', $block, $m)) {
                // إن لم يلتقط، نحاول أخذ سطر أول كسؤال
                $parts = preg_split('/\R/u', trim($block));
                $qText = trim($parts[0] ?? '');
                $rest  = trim(implode("\n", array_slice($parts, 1)));
            } else {
                $qText = trim($m[1]);
                $rest  = trim($block);
            }

            // إلتقاط الخيارات الأربعة العربية (أ/ب/ج/د) بأي فاصل
            preg_match_all('/[أإا]\s*[)\-]?\s*(.+?)\R?ب\s*[)\-]?\s*(.+?)\R?ج\s*[)\-]?\s*(.+?)\R?د\s*[)\-]?\s*(.+?)(?:\R|$)/u', $rest, $mm);
            if (isset($mm[1][0])) {
                $opts = [trim($mm[1][0]), trim($mm[2][0]), trim($mm[3][0]), trim($mm[4][0])];
            } else {
                // fallback: التقط أسطر تبدأ بـ (أ|ب|ج|د)
                preg_match_all('/^(?:[أإابجده]\s*[)\-])\s*(.+)$/um', $rest, $mm2);
                $opts = array_map('trim', $mm2[1] ?? []);
            }

            if (!$qText || count($opts) < 2) continue;

            // محاولة تخمين الصحيحة إن ظهرت “الإجابة الصحيحة” في النص
            $correct = null;
            if (preg_match('/الإجابة\s+الصحيحة\s*[:：]?\s*(.+)/u', $block, $mc)) {
                $correct = trim($mc[1]);
            }

            $out[] = ['q' => $qText, 'opts' => $opts, 'correct' => $correct];
        }

        return $out;
    }

    /** نمط إنجليزي بسيط A/B/C/D مع ) أو . أو - */
    private function extractEn(string $t): array
    {
        $blocks = preg_split('/\R\s*\d+\s*[)\.]\s*/', $t, -1, PREG_SPLIT_NO_EMPTY);
        $out = [];
        foreach ($blocks as $b) {
            $parts = preg_split('/\R/u', trim($b));
            $qText = trim($parts[0] ?? '');
            preg_match_all('/^[A-D]\s*[)\.\-]\s*(.+)$/mi', $b, $m);
            $opts = array_map('trim', $m[1] ?? []);
            if ($qText && count($opts) >= 2) {
                $out[] = ['q'=>$qText, 'opts'=>$opts];
            }
        }
        return $out;
    }
}

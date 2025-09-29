<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class ChatbotController extends Controller
{
    public function send(Request $request)
    {
        $data = $request->validate([
            'message' => 'required|string|max:60000',
            'history' => 'array',
        ]);

        $userMessage = trim($data['message']);
        $history     = $data['history'] ?? [];

        try {
            $apiKey = config('openai.api_key') ?: config('services.openai.key') ?: env('OPENAI_API_KEY');
            if (!$apiKey) {
                throw new \RuntimeException('Missing OPENAI_API_KEY');
            }

            $model = env('OPENAI_MODEL', 'gpt-4o-mini');

            // نحدد العدد المطلوب تلقائيًا: أرقام أو كلمات عربية؛ ولو ما وجد رقم ولكن في نية "أسئلة" نستخدم الافتراضي (100)
            $targetN = $this->desiredCount($userMessage, (int)env('OPENAI_DEFAULT_TOTAL', 100));

            // مهلة أطول لأننا قد نولّد كميات كبيرة
            @set_time_limit(300);

            $system = "You are a helpful assistant. Write naturally like a human. For Arabic, use clear, friendly language and proper RTL-friendly punctuation. When the user asks for many items (e.g., 100 questions), produce them cleanly with consistent formatting. Avoid overuse of markdown if the output is very long.";

            // لو المطلوب >= 30 عنصر (مثل 100 سؤال) نولّد بدُفعات حتى نكمل كل العدد
            if ($targetN >= 30) {
                $full = $this->generateBatchedText($model, $system, $history, $userMessage, $targetN);
                return response()->json(['ok' => true, 'reply' => $full]);
            }

            // غير ذلك: استدعاء واحد
            $messages = [['role' => 'system', 'content' => $system]];
            foreach ($history as $h) {
                $role = ($h['role'] ?? '') === 'assistant' ? 'assistant' : 'user';
                $messages[] = ['role' => $role, 'content' => (string)($h['content'] ?? '')];
            }
            $messages[] = ['role' => 'user', 'content' => $userMessage];

            $resp = OpenAI::chat()->create([
                'model'       => $model,
                'messages'    => $messages,
                'temperature' => 0.4,
                'max_tokens'  => 1800,
            ]);

            $reply = trim($resp['choices'][0]['message']['content'] ?? '');
            if ($reply === '') $reply = 'لم أتمكن من توليد رد الآن.';

            return response()->json(['ok' => true, 'reply' => $reply]);

        } catch (\Throwable $e) {
            Log::error('Chatbot error: '.$e->getMessage());
            // نرجّع 200 برسالة واضحة بدل 500 عشان الواجهة ما تعتبره فشل شبكة
            return response()->json([
                'ok'    => false,
                'reply' => config('app.debug')
                    ? ('خطأ من OpenAI: '.$e->getMessage())
                    : 'عذراً، حدث خطأ غير متوقع. تحقّق من إعدادات OpenAI ثم أعد المحاولة.',
            ], 200);
        }
    }

    /**
     * توليد نص طويل على دفعات (مثلاً 100 سؤال) مع ترقيم مستمر وعدم تكرار.
     * حجم الدفعة يُقرأ من OPENAI_BATCH_SIZE (افتراضي 20).
     */
    private function generateBatchedText(string $model, string $system, array $history, string $userMessage, int $targetN): string
    {
        $batchSize = (int) env('OPENAI_BATCH_SIZE', 20);
        if ($batchSize < 5)  $batchSize = 5;
        if ($batchSize > 40) $batchSize = 40;

        $batches = (int) ceil($targetN / $batchSize);
        $output  = [];
        $soFar   = 0;

        for ($b = 1; $b <= $batches; $b++) {
            $start = $soFar + 1;
            $end   = min($start + $batchSize - 1, $targetN);

            $batchInstruction = <<<TXT
الرجاء توليد العناصر {$start} إلى {$end} فقط من المطلوب التالي، بدون تكرار ما سبق، واستمرّ في الترقيم من {$start}:

{$userMessage}

تعليمات التنسيق:
- اكتب بصياغة بشرية طبيعية.
- إن كانت أسئلة اختيار من متعدد: اجعل كل سؤال مرقماً، والخيارات تحت كل سؤال بصيغة:
  - أ)
  - ب)
  - ج)
  - د)
- تجنّب الزخرفة المبالغ فيها، لكن لا تختصر الإجابة بشكل قسري.
TXT;

            $messages = [['role' => 'system', 'content' => $system]];
            foreach ($history as $h) {
                $role = ($h['role'] ?? '') === 'assistant' ? 'assistant' : 'user';
                $messages[] = ['role' => $role, 'content' => (string)($h['content'] ?? '')];
            }
            if (!empty($output)) {
                $prev = mb_substr(implode("\n\n", $output), -8000); // لتجنّب التكرار
                $messages[] = ['role' => 'assistant', 'content' => $prev];
            }
            $messages[] = ['role' => 'user', 'content' => $batchInstruction];

            $resp = OpenAI::chat()->create([
                'model'       => $model,
                'messages'    => $messages,
                'temperature' => 0.4,
                'max_tokens'  => 1800,
            ]);

            $chunk = trim($resp['choices'][0]['message']['content'] ?? '');
            if ($chunk === '') break;

            $output[] = $chunk;
            $soFar    = $end;
        }

        return implode("\n\n", $output);
    }

    /**
     * يستخرج العدد المطلوب من النص:
     * - يدعم أرقام (20/50/100…)
     * - يدعم كلمات عربية شائعة (خمسين/مئة/عشرين…)
     * - لو لم يوجد رقم ولكن في نية "أسئلة/اختبار" يرجع fallback (افتراضياً 100)
     */
    private function desiredCount(string $text, int $fallback = 100): int
    {
        $t = mb_strtolower($text, 'UTF-8');

        // نية أسئلة
        $hasQuestionsIntent = preg_match('/(أسئلة|اسئله|اختبار|اختيار\s*متعدد|سؤال|سوال)/u', $t);

        // أرقام
        if (preg_match('/(\d{1,3})\s*(?:سؤال|سوال|سؤ|questions?)/u', $t, $m)) {
            return (int) $m[1];
        }

        // كلمات أعداد شائعة
        $map = [
            'عشرين' => 20, 'عشرون' => 20,
            'خمس وعشرين' => 25, 'خمسة وعشرين' => 25, 'خمسة وعشرون' => 25,
            'ثلاثين' => 30, 'اربعين' => 40, 'أربعين' => 40, 'خمسين' => 50,
            'ستين' => 60, 'سبعين' => 70, 'ثمانين' => 80, 'تسعين' => 90,
            'مائة' => 100, 'مائَة' => 100, 'مئة' => 100, 'مية' => 100,
            'مائتين' => 200, 'مائتان' => 200, 'مئتين' => 200, 'مئتان' => 200,
        ];
        foreach ($map as $k => $v) {
            if (mb_strpos($t, $k) !== false) return $v;
        }

        return $hasQuestionsIntent ? $fallback : 0;
    }
}

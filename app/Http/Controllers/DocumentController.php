<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentQuestion;
use App\Models\DocumentAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use OpenAI\Laravel\Facades\OpenAI;
use Smalot\PdfParser\Parser;

class DocumentController extends Controller
{
    /* ====================== صفحات ====================== */

    public function create()
    {
        return view('documents.upload');
    }

    /**
     * قائمة المستندات (لجميع المستخدمين)
     * - افتراضيًا نعرض processed فقط
     * - فلترة اللغة: ?lang=arabic|english
     * - فلترة الحالة: ?status=processed,pending,processing
     * - عرض ملفّات المستخدم الحالي فقط: ?mine=1
     * - صفحة: paginate(12)
     */
    public function index(Request $request)
    {
        $query = Document::query()
            ->select('id', 'title', 'language', 'status', 'created_at')
            ->withCount('questions');

        // فلترة اللغة (اختياري)
        if ($request->filled('lang')) {
            $query->where('language', $request->string('lang')->toString());
        }

        // فلترة الحالة (اختياري) - افتراضي: processed فقط
        if ($request->filled('status')) {
            $statuses = collect(explode(',', $request->string('status')->toString()))
                ->map(fn ($s) => trim(strtolower($s)))
                ->filter()
                ->values()
                ->all();

            if (!empty($statuses)) {
                $query->whereIn('status', $statuses);
            }
        } else {
            $query->where('status', 'processed');
        }

        // عرض ملفّات المستخدم الحالي فقط لو mine=1
        if ($request->boolean('mine')) {
            $query->where('user_id', Auth::id());
        }

        $documents = $query->orderByDesc('created_at')->paginate(12);

        return view('documents.index', compact('documents'));
    }

    public function view(Document $document)
    {
        $document->load('questions');

        $answers = [];
        if (Schema::hasTable('document_answers')) {
            $answers = DocumentAnswer::where('document_id', $document->id)
                ->where('user_id', Auth::id())
                ->pluck('answer', 'question_index')
                ->toArray();
        }

        return view('documents.view', compact('document', 'answers'));
    }

    /* ====================== رفع واستخراج ====================== */

    public function store(Request $request)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'document' => 'required|mimes:pdf|max:204800', // 200MB
            'language' => 'required|in:arabic,english'
        ]);

        // رفع الملف
        $file = $request->file('document');
        $fileName = time().'_'.$file->getClientOriginalName();
        $filePath = $file->storeAs('documents', $fileName, 'public');

        // قراءة نص PDF
        $text = '';
        try {
            @set_time_limit(240);
            $parser = new Parser();
            $pdf    = $parser->parseFile(storage_path('app/public/'.$filePath));
            $rawText = trim((string) $pdf->getText());
            $text    = $this->normalizeText($rawText);
        } catch (\Throwable $e) {
            Log::warning('PDF parse failed: '.$e->getMessage());
            $text = '';
        }

        // حفظ الوثيقة
        $document = Document::create([
            'title'         => $request->title,
            'file_name'     => $file->getClientOriginalName(),
            'file_path'     => $filePath,
            'file_size'     => $file->getSize(),
            'language'      => $request->language,
            'status'        => 'processing',
            'extracted_text'=> $text,
            'content'       => $text, // إذا لا يوجد عمود content احذفه من fillable ومن هنا
            'user_id'       => Auth::id()
        ]);

        // 1) نحاول OpenAI بشكل مجزّأ (أفضل جودة) — بصيغة JSON صارمة
        $madeQuestions = $this->extractWithOpenAIChunked($document, $text);

        // 2) إن فشل/لم يرجّع شيء، نعمل Fallback Regex احترافي
        if (!$madeQuestions) {
            $madeQuestions = $this->extractWithRegexPro($document, $text);
        }

        $document->update(['status' => $madeQuestions ? 'processed' : 'pending']);

        return redirect()
            ->route('documents.view', $document->id)
            ->with($madeQuestions ? 'success' : 'warning', $madeQuestions
                ? 'تم رفع الملف واستخراج الأسئلة.'
                : 'تم رفع الملف لكن تعذّر استخراج الأسئلة تلقائيًا. يمكنك إعادة الاستخراج لاحقًا.');
    }

    public function reextract(Document $document)
    {
        $text = $document->extracted_text ?: $document->content ?: '';
        if (!$text) {
            return back()->with('warning', 'لا يوجد نص مُستخرج من الملف.');
        }

        // حذف الأسئلة السابقة
        $document->questions()->delete();
        $text = $this->normalizeText($text);

        $madeQuestions = $this->extractWithOpenAIChunked($document, $text);
        if (!$madeQuestions) {
            $madeQuestions = $this->extractWithRegexPro($document, $text);
        }

        $document->update(['status' => $madeQuestions ? 'processed' : 'pending']);

        return back()->with($madeQuestions ? 'success' : 'warning',
            $madeQuestions ? 'تمت إعادة الاستخراج.' : 'تعذّر الاستخراج. تحقق من اتصال OpenAI/SSL أو عدّل يدويًا.');
    }

    /* ====================== إرسال وتصحيح ====================== */

    public function submit(Request $request, Document $document)
    {
        $answers = $request->input('answers', []);
        $document->load('questions');

        $results = [];
        $needAI  = [];   // أسئلة بلا إجابة صحيحة، نرسلها لـ OpenAI لاحقاً

        // 1) تصحيح محلي فوري حيثما أمكن
        foreach ($document->questions as $index => $q) {
            $userAns = $answers[$index] ?? null;

            if ($q->correct_answer) {
                $isCorrect = $userAns !== null && $this->normalizeChoice($userAns) === $this->normalizeChoice($q->correct_answer);
                $results[] = [
                    'index'           => $index,
                    'question'        => $q->question,
                    'user_answer'     => $userAns,
                    'correct_answer'  => $q->correct_answer,
                    'is_correct'      => $isCorrect,
                    'feedback'        => $isCorrect ? '✔️ إجابة صحيحة' : '❌ الإجابة غير صحيحة',
                    'source'          => 'local',
                ];
            } else {
                // لا نملك correct_answer لهذا السؤال → نضيفه لقائمة التصحيح عبر AI
                $needAI[] = [
                    'index'       => $index,
                    'question'    => $q->question,
                    'options'     => $q->options ?: [],
                    'user_answer' => $userAns,
                ];
            }
        }

        // 2) حاول تصحيح ما تبقى عبر OpenAI (بمهلات صريحة)
        $aiFailed = false;
        if (!empty($needAI)) {
            try {
                $sys = "أنت مصحح اختبارات. لكل عنصر أعد JSON فقط:
[{\"index\":0,\"question\":\"...\",\"user_answer\":\"...\",\"correct_answer\":\"...\",\"is_correct\":true,\"feedback\":\"...\"}]
- إذا كانت الخيارات موجودة استند إليها في التصحيح.
- أعِد فقط JSON دون أي نص إضافي.";

                $response = $this->ai()->chat()->create([
                    'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
                    'messages' => [
                        ['role' => 'system', 'content' => $sys],
                        ['role' => 'user', 'content' => json_encode($needAI, JSON_UNESCAPED_UNICODE)],
                    ],
                    'temperature' => 0.0,
                    'max_tokens'  => 1500,
                ]);

                $aiPart = $this->safeDecodeJson($response['choices'][0]['message']['content'] ?? '[]', true);

                foreach ($aiPart as $row) {
                    $results[] = [
                        'index'           => $row['index'] ?? null,
                        'question'        => $row['question'] ?? '',
                        'user_answer'     => $row['user_answer'] ?? null,
                        'correct_answer'  => $row['correct_answer'] ?? null,
                        'is_correct'      => (bool)($row['is_correct'] ?? false),
                        'feedback'        => $row['feedback'] ?? '',
                        'source'          => 'ai',
                    ];
                }
            } catch (\Throwable $e) {
                Log::error('OpenAI grading failed: '.$e->getMessage());
                $aiFailed = true;

                foreach ($needAI as $row) {
                    $results[] = [
                        'index'           => $row['index'],
                        'question'        => $row['question'],
                        'user_answer'     => $row['user_answer'],
                        'correct_answer'  => null,
                        'is_correct'      => null,
                        'feedback'        => '— لم يتم تصحيح هذا السؤال لعدم توفر اتصال بالتصحيح الآلي.',
                        'source'          => 'pending',
                    ];
                }
            }
        }

        // 3) احفظ إجابات المستخدم
        foreach ($answers as $index => $answer) {
            DocumentAnswer::updateOrCreate(
                ['user_id' => Auth::id(), 'document_id' => $document->id, 'question_index' => $index],
                ['answer' => $answer]
            );
        }

        // 4) احسب الدرجة النهائية من النتائج المتاحة
        $answered   = 0;
        $correct    = 0;
        foreach ($results as $r) {
            if ($r['is_correct'] === true) {
                $answered++;
                $correct++;
            } elseif ($r['is_correct'] === false) {
                $answered++;
            }
        }
        $total = count($document->questions);
        $score = $answered > 0 ? round(($correct / $answered) * 100) : 0;

        // 5) رجّع للواجهة
        $flashType = $aiFailed ? 'warning' : 'success';
        $flashMsg  = $aiFailed
            ? 'تم حفظ إجاباتك وتصحيح ما أمكن محليًا، وبعض الأسئلة مؤجلة لغياب اتصال التصحيح الآلي.'
            : 'تم تصحيح إجاباتك.';

        return redirect()->back()
            ->with($flashType, $flashMsg)
            ->with('results', [
                'score'      => $score,
                'answered'   => $answered,
                'correct'    => $correct,
                'total'      => $total,
                'details'    => $results,
            ]);
    }

    /**
     * توحيد تمثيل الخيار للمقارنة (A) نص… → A) نص…)
     */
    private function normalizeChoice(?string $s): ?string
    {
        if ($s === null) return null;
        $s = trim($s);
        $map = ['أ'=>'A','ا'=>'A','ب'=>'B','ج'=>'C','د'=>'D','هـ'=>'E','ه'=>'E'];
        if (preg_match('/^([A-Za-zأ-ه])\)?\s*/u', $s, $m)) {
            $label = $m[1];
            if (isset($map[$label])) $label = $map[$label];
            $body  = trim(mb_substr($s, mb_strlen($m[0])));
            $s = $label.') '.($body ?: '');
        }
        return preg_replace('/\s{2,}/', ' ', $s);
    }

    /* ====================== OpenAI: استخراج مجزّأ (JSON صارم) ====================== */

    private function extractWithOpenAIChunked(Document $document, string $text): bool
    {
        // إعداد القطع والميزانية الزمنية لتجنب التعليق
        $chunks      = $this->splitByLength($text, 2800, (int) env('OPENAI_MAX_CHUNKS', 6));
        $timeBudget  = (int) env('OPENAI_TIME_BUDGET', 100); // ثواني لكل عملية استخراج
        $startedAt   = microtime(true);

        $allQs  = [];

        $sys = <<<SYS
أنت مساعد تعليمي. ارجع *حصراً* JSON مطابقًا للمخطط التالي:

{
  "questions": [
    {
      "question": "string (<= 220 chars)",
      "options": ["string","string","string","string"], // 2..6 عناصر، بدون حروف A/B/C/D وبدون أرقام
      "correct_index": 0 // رقم صحيح يشير إلى عنصر في options
    }
  ]
}

القواعد:
- لا تُرجِع أي Markdown أو نص خارج JSON.
- لا تُكرّر الأسئلة.
- اختصر نص السؤال والخيارات.
- إن لم تجد أسئلة صالحة في هذا المقتطف، أرجِع {"questions": []}.
SYS;

        foreach ($chunks as $i => $chunk) {
            if ((microtime(true) - $startedAt) > $timeBudget) {
                Log::warning('OpenAI extract: time budget reached, stopping early.');
                break;
            }

            try {
                $resp = $this->ai()->chat()->create([
                    'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
                    'messages' => [
                        ['role' => 'system', 'content' => $sys],
                        ['role' => 'user',   'content' => mb_substr($chunk, 0, 12000)],
                    ],
                    'temperature'      => 0.0,
                    'response_format'  => ['type' => 'json_object'],
                    'max_tokens'       => 1200,
                ]);

                $raw  = $resp['choices'][0]['message']['content'] ?? '{}';
                $json = json_decode($raw, true);

                if (!is_array($json) || !isset($json['questions']) || !is_array($json['questions'])) {
                    continue;
                }

                foreach ($json['questions'] as $q) {
                    if (!isset($q['question'], $q['options'], $q['correct_index'])) continue;
                    if (!is_array($q['options']) || count($q['options']) < 2) continue;
                    $ci = (int)$q['correct_index'];
                    if ($ci < 0 || $ci >= count($q['options'])) continue;

                    $allQs[] = [
                        'question'       => (string)$q['question'],
                        'options'        => array_values(array_map('trim', $q['options'])),
                        'correct_index'  => $ci,
                    ];
                }

                // تهدئة بسيطة بين الطلبات
                usleep(150 * 1000);
            } catch (\Throwable $e) {
                Log::warning("OpenAI chunk #$i failed: ".$e->getMessage());
            }
        }

        $clean = $this->postProcessJsonQuestions($allQs);

        if (count($clean) === 0) {
            return false;
        }

        foreach ($clean as $q) {
            DocumentQuestion::create([
                'document_id'    => $document->id,
                'question'       => $q['question'],
                'options'        => $q['options'],
                'correct_answer' => $q['correct_answer'], // مثل: "C) Jupiter"
            ]);
        }
        return true;
    }

    /* ====================== Fallback Regex (احترافي) ====================== */

    private function extractWithRegexPro(Document $document, string $text): bool
    {
        $qs = $this->regexExtract($text);
        $qs = $this->postProcessQuestions($qs);

        if (count($qs) === 0) return false;

        foreach ($qs as $q) {
            DocumentQuestion::create([
                'document_id'    => $document->id,
                'question'       => $q['question'],
                'options'        => $q['options'],
                'correct_answer' => $q['correct_answer'] ?? null,
            ]);
        }
        return true;
    }

    /* ====================== Utilities: تنظيف/تطبيع ====================== */

    private function normalizeText(string $text): string
    {
        $text = preg_replace("/\r\n|\r/", "\n", $text);
        $text = preg_replace('/[ \t]+/', ' ', $text);
        $text = preg_replace('/\.{3,}/', '…', $text);
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        $arabicDigits = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];
        $western      = ['0','1','2','3','4','5','6','7','8','9'];
        $text = str_replace($arabicDigits, $western, $text);
        return trim($text);
    }

    private function splitByLength(string $text, int $chunkLen = 3000, int $maxChunks = 6): array
    {
        $text = trim($text);
        if (mb_strlen($text) <= $chunkLen) return [$text];

        $parts = [];
        $pos   = 0;
        for ($i = 0; $i < $maxChunks && $pos < mb_strlen($text); $i++) {
            $slice = mb_substr($text, $pos, $chunkLen);
            $lastBreak = mb_strrpos($slice, "\n");
            if ($lastBreak !== false && $lastBreak > $chunkLen * 0.6) {
                $slice = mb_substr($slice, 0, $lastBreak);
                $pos  += $lastBreak + 1;
            } else {
                $pos  += $chunkLen;
            }
            $parts[] = trim($slice);
        }
        return $parts;
    }

    private function safeDecodeJson(string $raw, bool $arrayOnly = false)
    {
        $candidate = $raw;
        if (preg_match('/\{.*\}|\[.*\]/sU', $raw, $m)) {
            $candidate = $m[0];
        }
        return json_decode($candidate, $arrayOnly, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * تحويل مخرجات JSON (question/options/correct_index) إلى
     * البنية الحالية للمشروع (options مع حروف A/B/C… و correct_answer نصيًا).
     */
    private function postProcessJsonQuestions(array $items): array
    {
        $out = [];
        foreach ($items as $q) {
            $question = trim((string)$q['question']);
            if ($question === '' || mb_strlen($question) > 350) continue;

            $opts = array_slice(array_filter(array_map(function($s){
                $s = trim((string)$s);
                $s = preg_replace('/^[A-D]\)\s*/u', '', $s);
                return $s;
            }, $q['options'] ?? [])), 0, 6);

            if (count($opts) < 2) continue;

            $labels = ['A','B','C','D','E','F'];
            $labeled = [];
            foreach ($opts as $i => $opt) {
                $label = $labels[$i] ?? chr(65+$i);
                $labeled[] = $label.') '.$opt;
            }

            $ci = (int)($q['correct_index'] ?? -1);
            $correct = ($ci >= 0 && $ci < count($labeled)) ? $labeled[$ci] : null;

            $out[] = [
                'question'       => $question,
                'options'        => $labeled,
                'correct_answer' => $correct,
            ];
        }

        $seen = [];
        $unique = [];
        foreach ($out as $row) {
            $sig = md5($row['question'].'|'.implode('|',$row['options']));
            if (isset($seen[$sig])) continue;
            $seen[$sig] = true;
            $unique[] = $row;
        }

        return array_slice($unique, 0, 50);
    }

    /**
     * التنظيف/تطبيع لأسلوب الاستخراج القديم (عند استخدام Regex أو نماذج قديمة)
     */
    private function postProcessQuestions(array $qs): array
    {
        $out = [];
        $seen = [];

        foreach ($qs as $q) {
            $question = trim((string)($q['question'] ?? ''));
            $options  = $q['options'] ?? [];

            if ($question === '') continue;
            if (mb_strlen($question) > 350) continue;

            $question = preg_replace('/^(?:\d{1,3}[\)\.\-]\s*)/u', '', $question);
            $question = trim($question, " \t\n\r\0\x0B-–—:،؛.");

            $normalizedOptions = [];
            $map = ['أ'=>'A','ا'=>'A','ب'=>'B','ج'=>'C','د'=>'D','هـ'=>'E','ه'=>'E'];
            foreach ((array)$options as $i => $opt) {
                $opt = trim((string)$opt);
                if ($opt === '') continue;

                if (preg_match('/^([A-Dأ-ه])\)\s*/u', $opt, $m)) {
                    $label = $m[1];
                    $body  = trim(mb_substr($opt, mb_strlen($m[0])));
                    if (isset($map[$label])) $label = $map[$label];
                    $opt = $label.') '.$body;
                }

                $opt = preg_replace('/\s{2,}/', ' ', $opt);
                if (mb_strlen($opt) > 160) continue;

                $normalizedOptions[] = $opt;
            }

            if (count($normalizedOptions) < 2) continue;
            $normalizedOptions = array_slice($normalizedOptions, 0, 4);

            $sig = md5($question.'|'.implode('|', $normalizedOptions));
            if (isset($seen[$sig])) continue;
            $seen[$sig] = true;

            $correct = $q['correct_answer'] ?? null;
            if (!$correct) {
                foreach ($normalizedOptions as $opt) {
                    if (preg_match('/(\*|✓|✔)/u', $opt)) {
                        $correct = preg_replace('/\s*(\*|✓|✔)\s*/u', '', $opt);
                        break;
                    }
                }
            }

            $out[] = [
                'question'        => $question,
                'options'         => $normalizedOptions,
                'correct_answer'  => $correct,
            ];
        }

        return array_slice($out, 0, 50);
    }

    /* ====================== Regex Extract (مع فلاتر) ====================== */

    private function regexExtract(string $text): array
    {
        $text = preg_replace('/\d{6,}/', '', $text);
        $text = preg_replace('/https?:\/\/\S+/', '', $text);
        $text = preg_replace('/[|]{2,}/', ' ', $text);

        $blocks = preg_split(
            "/(?=\n?(?:\d{1,3})[\)\.\-]\s)/u",
            $text,
            -1,
            PREG_SPLIT_NO_EMPTY
        );

        $questions = [];
        $optPattern = "/(?:^|\n)\s*([A-Dأبجده])\)\s*(.+?)(?=(?:\n\s*[A-Dأبجده]\)|$))/u";

        foreach ($blocks as $b) {
            $b = trim($b);
            if ($b === '') continue;
            if (mb_strlen($b) > 600) continue;

            $opts = [];
            if (preg_match_all($optPattern, $b, $m, PREG_SET_ORDER)) {
                $firstPos = mb_strpos($b, $m[0][0]);
                $qText    = $firstPos !== false ? trim(mb_substr($b, 0, $firstPos)) : $b;

                foreach ($m as $hit) {
                    $label = $hit[1];
                    $body  = trim($hit[2]);
                    if ($label && $body) {
                        $opts[] = $label.') '.$body;
                    }
                }

                $qText = preg_replace('/^(?:\d{1,3}[\)\.\-]\s*)/u', '', $qText);
                $qText = trim($qText, " \t\n\r\0\x0B-–—:،؛.");

                if ($qText && count($opts) >= 2) {
                    $questions[] = ['question' => $qText, 'options' => $opts];
                }
            }
        }

        return $questions;
    }

    /* ====================== عميل OpenAI مع مهلات افتراضية ====================== */

    /**
     * يبني Facade مع خيارات Guzzle (timeouts/verify) من config('openai.http') أو قيم افتراضية.
     */
    private function ai()
    {
        $defaults = [
            'timeout'         => 240,
            'connect_timeout' => 30,
            'read_timeout'    => 25,
        ];

        // ملاحظة: على ويندوز للتجارب فقط يمكنك ضبط HTTP_VERIFY=false في .env
        $cfg = array_merge($defaults, (array) config('openai.http', []));
        return OpenAI::withOptions($cfg);
    }
}

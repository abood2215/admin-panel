<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Document, DocumentQuestion, Stream, Year, Subject, Specialty};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use OpenAI\Laravel\Facades\OpenAI;
use Smalot\PdfParser\Parser;

class DocumentAdminController extends Controller
{
    /* ====================== NEW: Step 1 — Specialties grid ====================== */
    public function specialties()
    {
        $specialties = Specialty::with('stream:id,slug,name_ar,name_en')
            ->select('id','name_ar','name_en','stream_id')
            ->orderBy('id')->get();

        return view('admin.specialties.index', compact('specialties'));
    }

    /* ====================== NEW: Step 2 — Pick Year & Semester ====================== */
    public function specialtyChoose(Specialty $specialty, Request $request)
    {
        $years = Year::orderBy('year','desc')->get(['id','year']);
        return view('admin.specialties.choose', [
            'specialty' => $specialty,
            'years'     => $years,
            'picked'    => [
                'year'     => $request->integer('year'),
                'semester' => $request->get('semester'),
            ]
        ]);
    }

    /* ====================== NEW: Step 3 — Browse Cards ====================== */
    public function specialtyBrowse(Specialty $specialty, Request $request)
    {
        $yearNumber = (int)$request->query('year');
        $semester   = $request->query('semester'); // first | second | null

        $yearId = $yearNumber ? Year::where('year',$yearNumber)->value('id') : null;

        $query = Document::query()
            ->with(['year','subject','stream'])
            ->where('specialty_id', $specialty->id)
            ->when($yearId,   fn($q)=>$q->where('year_id',$yearId))
            ->when($semester, fn($q)=>$q->where('semester',$semester))
            ->orderByDesc('created_at');

        $documents = $query->get();

        // لعرض الكروت نقسّم حسب السنة أو الأكثر فاعلية:
        $grouped = $documents->groupBy(fn($d)=> optional($d->year)->year ?: '—');

        return view('admin.documents.cards', [
            'specialty' => $specialty,
            'documents' => $documents,
            'grouped'   => $grouped,
            'yearNumber'=> $yearNumber,
            'semester'  => $semester,
        ]);
    }




    

    /* ====================== Dashboard القديمة تبقى كما هي (للجدول) ====================== */
    public function index(Request $request)
    {
        $totalUsers          = DB::table('users')->count();
        $totalAdmins         = DB::table('users')->where('is_admin', 1)->count();
        $totalUsersNonAdmin  = DB::table('users')->where('is_admin', 0)->count();
        $totalDocuments      = DB::table('documents')->count();

        $streamSlug = $request->query('stream');
        $yearNumber = $request->query('year');
        $subjectId  = $request->query('subject');

        $streams  = Stream::select('id','slug','name_ar','name_en')->orderBy('id')->get();
        $yearsMap = Year::pluck('id','year');
        $yearsRev = Year::pluck('year','id');

        $streamId = $streamSlug ? Stream::where('slug',$streamSlug)->value('id') : null;
        $yearId   = $yearNumber ? ($yearsMap[$yearNumber] ?? null) : null;

        $query = Document::query()->with(['stream','year','subject'])->orderByDesc('created_at');
        if ($streamId)  $query->where('stream_id', $streamId);
        if ($yearId)    $query->where('year_id',   $yearId);
        if ($subjectId) $query->where('subject_id',$subjectId);
        $documents = $query->get();

        $availableYearIds = Document::when($streamId, fn($q)=>$q->where('stream_id',$streamId))
            ->pluck('year_id')->filter()->unique()->values();
        $availableYears = Year::whereIn('id',$availableYearIds)->orderBy('year','desc')->pluck('year');

        $availableSubjectIds = Document::when($streamId, fn($q)=>$q->where('stream_id',$streamId))
            ->when($yearId,   fn($q)=>$q->where('year_id',$yearId))
            ->pluck('subject_id')->filter()->unique()->values();
        $availableSubjects = \App\Models\Subject::whereIn('id',$availableSubjectIds)
            ->select('id','name_ar','name_en')->orderBy('id')->get();

        return view('livewire.dashboardAdmin', compact(
            'totalUsers','totalAdmins','totalUsersNonAdmin','totalDocuments',
            'documents','streams','availableYears','availableSubjects',
            'streamSlug','yearNumber','subjectId','yearsRev'
        ));
    }

    /* ====================== Upload/Create ====================== */
   public function create(\Illuminate\Http\Request $request)
{
    // لوائح الاختيار
    $streams     = \App\Models\Stream::select('id','slug','name_ar','name_en')->orderBy('id')->get();
    $years       = \App\Models\Year::orderBy('year','desc')->get(['id','year']);
    $subjects    = \App\Models\Subject::orderBy('id')->get(['id','name_ar','name_en']);
    $specialties = \App\Models\Specialty::orderBy('id')->get(['id','name_ar','name_en','stream_id']);

    // Prefill من الـ query string
    $prefillStreamSlug = $request->query('stream'); // scientific | literary
    $prefillStreamId   = $request->query('stream_id');
    if (!$prefillStreamSlug && $prefillStreamId) {
        $prefillStreamSlug = \App\Models\Stream::where('id', $prefillStreamId)->value('slug');
    }

    $prefillYearNumber = $request->query('year');
    $prefillYearId     = $request->query('year_id');
    if (!$prefillYearNumber && $prefillYearId) {
        $prefillYearNumber = \App\Models\Year::where('id', $prefillYearId)->value('year');
    }

    $defaults = [
        'stream_slug'  => $prefillStreamSlug,
        'year'         => $prefillYearNumber ? (int)$prefillYearNumber : null,
        'semester'     => $request->query('semester'),        // first | second
        'specialty_id' => $request->query('specialty_id'),
        'subject_id'   => $request->query('subject_id'),
        'language'     => $request->query('language'),        // arabic | english
    ];

    return view('admin.documents.upload', compact(
        'streams','years','subjects','specialties','defaults'
    ));
}


    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => ['required','string','max:255'],
            'language'     => ['required','in:arabic,english'],
            'stream_slug'  => ['required','string'],
            'year'         => ['required','integer'],
            'semester'     => ['required','in:first,second'], // <— جديد
          'specialty_id' => ['nullable','integer','exists:specialties,id'],
            'subject_id'   => ['required','integer','exists:subjects,id'],
            'document'     => ['required','file','mimes:pdf,txt','mimetypes:application/pdf,text/plain,application/octet-stream','max:10240'],
        ]);

        $stream   = \App\Models\Stream::where('slug', $data['stream_slug'])->firstOrFail();
        $year     = \App\Models\Year::where('year', (int)$data['year'])->firstOrFail();
        $specialty= \App\Models\Specialty::findOrFail($data['specialty_id']);
        $subject  = \App\Models\Subject::findOrFail($data['subject_id']);

        $file       = $request->file('document');
        $baseName   = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $ext        = $file->getClientOriginalExtension();
        $storedName = $baseName.'-'.time().'.'.$ext;
        $filePath   = $file->storeAs('documents', $storedName, 'public');
        $fullPath   = storage_path('app/public/'.$filePath);

        $extLower = strtolower($ext);
        $raw = '';
        try {
            if (in_array($extLower, ['txt','text'], true)) {
                $rawContent = @file_get_contents($fullPath);
                if ($rawContent !== false) {
                    if ($data['language'] === 'arabic') {
                        $encodings = ['UTF-8','CP1256','ISO-8859-6'];
                        foreach ($encodings as $enc) {
                            $t = @iconv($enc, 'UTF-8//IGNORE', $rawContent);
                            if ($t && $this->containsArabic($t)) { $rawContent = $t; break; }
                        }
                    }
                    $raw = $this->normalizePdfText($rawContent);
                }
            } else {
                $raw = $this->extractTextFromPdf($fullPath, $data['language']);
            }
        } catch (\Throwable $e) {
            Log::warning('File parse failed: '.$e->getMessage());
            try {
                $binary = @file_get_contents($fullPath);
                if ($binary) $raw = $this->normalizePdfText($binary);
            } catch (\Throwable $e2) {
                Log::warning('Binary file read failed: '.$e2->getMessage());
            }
        }

        $document = Document::create([
            'user_id'        => Auth::id(),
            'title'          => $this->toUtf8($data['title']),
            'language'       => $data['language'],
            'file_name'      => $storedName,
            'file_path'      => $filePath,
            'file_size'      => $file->getSize(),
            'status'         => 'processing',
            'extracted_text' => $raw,
            'content'        => $raw,
            'stream_id'      => $stream->id,
            'year_id'        => $year->id,
            'semester'       => $data['semester'],
            'specialty_id'   => $specialty->id,
            'subject_id'     => $subject->id,
        ]);

        $ok = $this->extractWithOpenAIChunked($document, $raw, $data['language']);
        if (!$ok) $ok = $this->extractWithRegex($document, $raw);

        $document->update(['status' => $ok ? 'processed' : 'pending']);

        return redirect()
            ->route('admin.documents.edit', $document->id)
            ->with($ok ? 'success' : 'warning', $ok
                ? 'تم رفع الملف وتصنيفه واستخراج الأسئلة بنجاح.'
                : 'تم رفع الملف وتصنيفه، لكن فشل الاستخراج. يمكنك إعادة الاستخراج أو التعديل يدوياً.');
    }

    /* ====================== Enhanced PDF Text Extraction ====================== */

    private function extractTextFromPdf(string $filePath, string $language = 'arabic'): string
    {
        $text = '';

        try {
            // (A) Smalot أولاً
            $parser = new Parser();
            $pdf = $parser->parseFile($filePath);
            $text = $pdf->getText() ?? '';

            // (B) لو النص ضعيف/مكسور، جرّب pdftotext بأوضاع متعددة
            if (mb_strlen($text) < 100 || $this->hasEncodingIssues($text)) {
                if (trim(shell_exec('which pdftotext 2>/dev/null')) !== '') {
                    $cands = $this->runPdfToTextCandidates($filePath);
                    foreach ($cands as $cand) {
                        if (mb_strlen($cand) > mb_strlen($text)) $text = $cand;
                    }
                }
            }

            // (C) إن ما زال شبه فارغ، جرّب OCR
            if (mb_strlen(trim($text)) < 30) {
                $ocr = $this->tryOcrPdf($filePath, $language);
                if ($ocr !== '') $text = $ocr;
            }
        } catch (\Throwable $e) {
            Log::warning('PDF text extraction failed: '.$e->getMessage());
            try {
                $binary = @file_get_contents($filePath);
                if ($binary) $text = $this->extractTextFromBinary($binary);
            } catch (\Throwable $e2) {
                Log::warning('Binary file read failed: '.$e2->getMessage());
            }
        }

        // (D) تطبيع
        $text = $this->normalizePdfText($text);

        // (E) إصلاح اتجاه العربية
        if ($language === 'arabic' && $this->looksArabicAndReversed($text)) {
            if (trim(shell_exec('which fribidi 2>/dev/null')) !== '') {
                $fixed = $this->bidiFixWithFribidi($text);
                if ($fixed !== '') $text = $fixed;
            } else {
                $text = $this->heuristicRtlWordOrderFix($text);
            }
        }

        return $this->normalizePdfText($text);
    }

    // … بقية الدوال كما هي (بدون تغيير) …

    private function runPdfToTextCandidates(string $filePath): array
    {
        $tmp = storage_path('app/tmp_'.uniqid());
        $results = [];
        $cmds = [
            'pdftotext -layout -enc UTF-8 -eol unix %s %s',
            'pdftotext -raw -enc UTF-8 %s %s',
            'pdftotext -simple -enc UTF-8 %s %s',
        ];
        foreach ($cmds as $fmt) {
            $out = $tmp.'.txt';
            $cmd = sprintf($fmt, escapeshellarg($filePath), escapeshellarg($out));
            @shell_exec($cmd.' 2>/dev/null');
            if (is_file($out)) {
                $txt = @file_get_contents($out) ?: '';
                @unlink($out);
                $results[] = $txt;
            }
        }
        return $results;
    }

    private function tryOcrPdf(string $filePath, string $language = 'arabic'): string
    {
        $hasPdftoppm = trim(shell_exec('which pdftoppm 2>/dev/null')) !== '';
        $hasTess     = trim(shell_exec('which tesseract 2>/dev/null')) !== '';
        if (!$hasPdftoppm || !$hasTess) return '';

        $lang = ($language === 'arabic') ? 'ara+eng' : 'eng';
        $dir  = storage_path('app/ocr_'.uniqid());
        @mkdir($dir, 0775, true);

        $base = $dir.'/page';
        @shell_exec(sprintf('pdftoppm -png -r 300 %s %s 2>/dev/null',
            escapeshellarg($filePath), escapeshellarg($base)));

        $buf = [];
        $files = glob($base.'-*.png'); sort($files);
        foreach ($files as $png) {
            $txt = @shell_exec(sprintf('tesseract %s stdout -l %s --psm 6 2>/dev/null',
                escapeshellarg($png), escapeshellarg($lang)));
            if ($txt) $buf[] = $txt;
            @unlink($png);
        }
        @rmdir($dir);

        return $buf ? implode("\n\n", $buf) : '';
    }

    private function extractTextFromBinary(string $binaryData): string
    {
        $text = '';
        if (preg_match_all('/[\x20-\x7E\x{0600}-\x{06FF}]{3,}/u', $binaryData, $m)) {
            $text = implode(' ', $m[0]);
        }
        return $text;
    }

    private function hasEncodingIssues(string $text): bool
    {
        $issues = [
            preg_match('/[^\x{0020}-\x{007E}\x{0600}-\x{06FF}\s\n\r\t]/u', $text) > (mb_strlen($text) * 0.1),
            (substr_count($text, '?') / max(1, mb_strlen($text))) > 0.1,
        ];
        return in_array(true, $issues, true);
    }

    private function looksArabicAndReversed(string $text): bool
    {
        $hasAr = preg_match('/[\x{0600}-\x{06FF}]/u', $text) === 1;
        if (!$hasAr) return false;
        $patterns = [
            '/\d+\s+[\x{0600}-\x{06FF}]+\s*\)/u',
            '/\)\s*[\x{0600}-\x{06FF}]+/u',
            '/[\x{0600}-\x{06FF}]{2,}\s+\d+/u',
        ];
        foreach ($patterns as $p) if (preg_match($p, $text)) return true;
        return false;
    }

    private function bidiFixWithFribidi(string $text): string
    {
        $cmd = 'fribidi -w 200 -c';
        $desc = [
            0 => ['pipe','r'],
            1 => ['pipe','w'],
            2 => ['pipe','w'],
        ];
        $proc = proc_open($cmd, $desc, $pipes);
        if (!is_resource($proc)) return '';
        fwrite($pipes[0], $text); fclose($pipes[0]);
        $out = stream_get_contents($pipes[1]); fclose($pipes[1]);
        stream_get_contents($pipes[2]); fclose($pipes[2]);
        proc_close($proc);
        return $out ?: '';
    }

    private function heuristicRtlWordOrderFix(string $text): string
    {
        $lines = preg_split('/\R/u', $text);
        $fixed = [];
        foreach ($lines as $ln) {
            $trim = trim($ln);
            if ($trim === '') { $fixed[] = $ln; continue; }
            $arCount = preg_match_all('/[\x{0600}-\x{06FF}]/u', $trim);
            $ratio = $arCount ? ($arCount / max(1, mb_strlen($trim))) : 0;
            if ($ratio > 0.3) {
                $tokens = preg_split('/(\s+)/u', $trim, -1, PREG_SPLIT_DELIM_CAPTURE);
                $words = []; $spaces = [];
                for ($i=0; $i<count($tokens); $i++) {
                    if ($i % 2 === 0) $words[] = $tokens[$i]; else $spaces[] = $tokens[$i];
                }
                $words = array_reverse($words);
                $re = '';
                for ($i=0; $i<count($words); $i++) {
                    $re .= $words[$i];
                    if (isset($spaces[$i])) $re .= $spaces[$i];
                }
                $fixed[] = $re;
            } else {
                $fixed[] = $ln;
            }
        }
        return implode("\n", $fixed);
    }

    /* ====================== Edit/Re-extract ====================== */

    public function edit(Document $document)
    {
        $document->load(['questions' => function ($q) {
            $q->orderBy('sort')->orderBy('id');
        }, 'categories']);
        return view('admin.documents.edit', compact('document'));
    }

    public function reextract(Document $document)
    {
        $text = $document->extracted_text ?: $document->content ?: '';
        if (!$text) return back()->with('warning', 'لا يوجد نص لإعادة الاستخراج منه.');

        $text = $this->normalizePdfText($text);
        $document->questions()->delete();

        $lang = $document->language ?: 'arabic';
        $ok   = $this->extractWithOpenAIChunked($document, $text, $lang) ?: $this->extractWithRegex($document, $text);

        $document->update(['status' => $ok ? 'processed' : 'pending']);

        return back()->with($ok ? 'success' : 'warning',
            $ok ? 'تمت إعادة الاستخراج بنجاح.' : 'فشل في إعادة الاستخراج. تحقق من اتصال الـ AI أو قم بالتعديل يدوياً.');
    }

    /* ====================== CRUD / Reorder ====================== */

    public function reorder(Request $request, Document $document)
    {
        $request->validate(['order' => 'required|array']);
        foreach ($request->order as $qid => $sort) {
            DocumentQuestion::where('document_id', $document->id)
                ->where('id', $qid)
                ->update(['sort' => (int)$sort]);
        }
        return response()->json(['ok' => true]);
    }

    public function storeQuestion(Request $request, Document $document)
    {
        $data = $request->validate([
            'question'       => 'required|string',
            'options'        => 'nullable|array',
            'correct_answer' => 'nullable|string',
        ]);

        $this->saveQuestion($document->id, [
            'question'       => $data['question'] ?? '',
            'options'        => $data['options'] ?? [],
            'correct_answer' => $data['correct_answer'] ?? null,
        ], (int) ($document->questions()->max('sort') + 1));

        return back()->with('success', 'تمت إضافة السؤال.');
    }

    public function updateQuestion(Request $request, Document $document, DocumentQuestion $question)
    {
        abort_unless($question->document_id === $document->id, 404);

        $data = $request->validate([
            'question'       => 'required|string',
            'options'        => 'nullable|array',
            'correct_answer' => 'nullable|string',
        ]);

        $clean = $this->prepareQuestionArray([
            'question'       => $data['question'] ?? '',
            'options'        => $data['options'] ?? [],
            'correct_answer' => $data['correct_answer'] ?? null,
        ]);

        $question->update($clean);
        return back()->with('success', 'تم تحديث السؤال.');
    }

    public function destroyQuestion(Document $document, DocumentQuestion $question)
    {
        abort_unless($question->document_id === $document->id, 404);
        $question->delete();
        return back()->with('success', 'تم حذف السؤال.');
    }

    public function destroy($id)
    {
        $doc = Document::findOrFail($id);
        // تأمين فصل التصنيفات (لو ما كان pivot cascadeOnDelete)
        $doc->categories()->detach();
        $doc->delete();
        return redirect()->route('admin.dashboard')->with('success', __('تم حذف المستند'));
    }

    /* ====================== UTF-8 & Normalization ====================== */

    private function toUtf8(string $s): string
    {
        if ($s === '') return '';
        $s = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $s);
        if (mb_check_encoding($s, 'UTF-8')) return trim($s);

        $encodings = ['UTF-8','CP1256','ISO-8859-6','UTF-16','UTF-32','ISO-8859-1','ASCII'];
        $supported = array_map('strtoupper', mb_list_encodings());
        $encodings = array_filter($encodings, fn($e)=>in_array(strtoupper($e), $supported));

        $detected = mb_detect_encoding($s, $encodings, true) ?: 'CP1256';
        $converted = @iconv($detected, 'UTF-8//IGNORE', $s);
        if ($converted === false) {
            $converted = @iconv('ISO-8859-6', 'UTF-8//IGNORE', $s);
            if ($converted === false) {
                $converted = @mb_convert_encoding($s, 'UTF-8', $detected);
                if ($converted === false) {
                    return trim(preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $s));
                }
            }
        }
        return trim($converted);
    }

    private function toUtf8Array(array $arr): array
    {
        return array_values(array_filter(array_map(function ($v) {
            if (is_string($v)) {
                $v = $this->toUtf8($v);
                return $v !== '' ? $v : null;
            }
            return $v;
        }, $arr), fn($v) => !is_string($v) || trim($v) !== ''));
    }

    private function normalizePdfText(string $text): string
    {
        $text = $this->toUtf8($text);
        $text = preg_replace("/\r\n|\r/", "\n", $text);
        $text = preg_replace('/[ \t]+/', ' ', $text);
        $text = preg_replace('/\.{3,}/', '…', $text);
        $text = preg_replace('/\n{3,}/', "\n\n", $text);

        $an = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];
        $wn = ['0','1','2','3','4','5','6','7','8','9'];
        $text = str_replace($an, $wn, $text);

        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $text);
        return trim($text);
    }

    private function containsArabic(string $text): bool
    {
        return preg_match('/[\x{0600}-\x{06FF}]/u', $text) === 1;
    }

    /* ====================== AI Helpers ====================== */

    private function splitByLength(string $text, int $chunkLen = 3500, int $maxChunks = 60, int $overlap = 400): array
    {
        $text = trim($text);
        $n = mb_strlen($text);
        if ($n <= $chunkLen) return [$text];

        $parts = [];
        $pos   = 0;
        for ($i = 0; $i < $maxChunks && $pos < $n; $i++) {
            $slice = mb_substr($text, $pos, $chunkLen);
            $lastBreak = mb_strrpos($slice, "\n");
            if ($lastBreak !== false && $lastBreak > $chunkLen * 0.5) {
                $slice = mb_substr($slice, 0, $lastBreak);
                $nextPos = $pos + $lastBreak + 1;
            } else {
                $nextPos = $pos + $chunkLen;
            }
            $parts[] = trim($slice);
            $pos = max(0, $nextPos - $overlap);
            if ($pos >= $n) break;
        }
        return $parts;
    }

    /** تقدير ذكي لعدد الأسئلة بناءً على الترقيم وأنماط الخيارات */
    private function estimateQuestionCountFromText(string $text): int
    {
        $t = $this->normalizePdfText($text);

        // 1) أسئلة مرقّمة: 1) 2. 3- ...
        preg_match_all('/(^|\R)\s*\d{1,3}\s*[\)\.\-]\s+/u', $t, $mNums);
        $numCount = count($mNums[0]);

        // 2) عدّ A) / أ) (غالباً يساوي عدد الأسئلة)
        preg_match_all('/(^|\R)\s*A\)\s+/u', $t, $mA1);
        preg_match_all('/(^|\R)\s*أ\)\s+/u', $t, $mA2);
        $aCount = count($mA1[0]) + count($mA2[0]);

        // 3) صيغ مرنة: A . أو أ .
        preg_match_all('/(^|\R)\s*A\s*[\.\-]\s+/u', $t, $mA3);
        preg_match_all('/(^|\R)\s*أ\s*[\.\-]\s+/u', $t, $mA4);
        $aLoose = count($mA3[0]) + count($mA4[0]);

        $estimate = max($numCount, $aCount, $aLoose);

        // 4) احتياط: B)/ب) في حال اختفى A)
        if ($estimate === 0) {
            preg_match_all('/(^|\R)\s*B\)\s+/u', $t, $mB1);
            preg_match_all('/(^|\R)\s*ب\)\s+/u', $t, $mB2);
            $estimate = max($estimate, count($mB1[0]), count($mB2[0]));
        }

        return $estimate;
    }

    /**
     * استخراج بالـAI مع حدّ أدنى ديناميكي:
     * - desired = max(50, estimate-by-regex, estimate-by-text) وبحد أقصى 500
     * - perChunk = 30
     * - توليد إضافي + دمج Regex عند الحاجة، مع إزالة التكرار وترتيب sort
     * - دعم override يدوي عبر request('target_count')
     */
    private function extractWithOpenAIChunked(Document $document, string $text, string $language = 'arabic'): bool
    {
        $text = $this->normalizePdfText($text);
        if ($text === '') return false;

        // حماية لو مفتاح OpenAI غير موجود
        $apiKey = config('openai.api_key') ?: env('OPENAI_API_KEY');
        if (!$apiKey) {
            Log::warning('OpenAI key missing; skipping AI extraction.');
            return false;
        }

        // تقدير العدد المرغوب
        $regexPreview = $this->basicExtract($text);    // بدون حفظ
        $estByRegex   = count($regexPreview);
        $estSmart     = $this->estimateQuestionCountFromText($text);
        $desired      = max(50, $estByRegex, $estSmart);
        $desired      = min($desired, 500);

        // Override يدوي إن وُجد
        if (request()->has('target_count')) {
            $manual = (int) request()->input('target_count');
            if ($manual > 0) {
                $desired = min(max($manual, 1), 500);
            }
        }

        $perChunk   = 30; // كان 20
        $chunks     = $this->splitByLength($text, 3500, 60, 400);
        $all        = [];
        $isArabic   = ($language === 'arabic');

        $sys = $isArabic
            ? <<<SYS
أنت مساعد تعليمي. من هذا المقتطف فقط استخرج أسئلة اختيار من متعدد وأعِد JSON **فقط**:

{"questions":[
  {"question":"<= 220 حرف","options":["خيار 1","خيار 2","خيار 3","خيار 4"],"correct_index":0}
]}

القواعد:
- أعِد حتى {$perChunk} سؤالًا كحد أقصى لهذا المقتطف، بدون تكرار.
- لا تُرجع أي نص خارج JSON.
- 2 إلى 6 خيارات لكل سؤال، ولا تضع A/B/C داخل نص الخيار.
- correct_index هو رقم الخيار الصحيح.
- إن لم يوجد أسئلة مناسبة أعِد {"questions": []}.
SYS
            : <<<SYS
You are an educational assistant. From THIS chunk only, extract multiple-choice questions and return **JSON only**:

{"questions":[
  {"question":"<= 220 chars","options":["opt1","opt2","opt3","opt4"],"correct_index":0}
]}

Rules:
- Return up to {$perChunk} questions for this chunk, no duplicates.
- No text outside JSON.
- 2..6 options; do not include A/B/C letters inside option text.
- correct_index is the correct option index.
- If none, return {"questions": []}.
SYS;

        foreach ($chunks as $i => $chunk) {
            try {
                $resp = OpenAI::chat()->create([
                    'model' => env('OPENAI_MODEL','gpt-4o-mini'),
                    'messages' => [
                        ['role' => 'system', 'content' => $sys],
                        ['role' => 'user',   'content' => mb_substr($chunk, 0, 12000)],
                    ],
                    'temperature'     => 0.0,
                    'response_format' => ['type' => 'json_object'],
                    'max_tokens'      => 2200,
                ]);

                $raw  = $resp['choices'][0]['message']['content'] ?? '{}';
                $json = json_decode($raw, true);

                if (is_array($json) && isset($json['questions']) && is_array($json['questions'])) {
                    foreach ($json['questions'] as $q) {
                        if (!isset($q['question'], $q['options'], $q['correct_index'])) continue;
                        $opts = array_values(array_filter(array_map('trim', (array)$q['options'])));
                        if (count($opts) < 2) continue;
                        $ci = (int)$q['correct_index'];
                        if ($ci < 0 || $ci >= count($opts)) continue;

                        $all[] = [
                            'question'      => trim((string)$q['question']),
                            'options'       => $opts,
                            'correct_index' => $ci,
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("OpenAI chunk #$i failed: ".$e->getMessage());
            }
        }

        // تنظيف وتوسيم (بدون A/B/C/D داخل النص)
        $clean = $this->postProcessJsonQuestions($all);

        // إن كان أقل من المطلوب، جرب توليد إضافي بالـAI لتغطية الفرق
        if (count($clean) < $desired) {
            $neededAi  = min($desired - count($clean), 120);
            if ($neededAi > 0) {
                $extra  = $this->generateAdditionalQuestions($text, $clean, $language, $neededAi);
                $merged = [];

                foreach ($clean as $r) {
                    $merged[] = [
                        'question'      => $r['question'],
                        'options'       => array_map(fn($o)=>$this->removeOptionLabelPrefix($o), $r['options']),
                        'correct_index' => 0
                    ];
                }
                foreach ($extra as $e) {
                    $merged[] = [
                        'question'      => $e['question'],
                        'options'       => $e['options'],
                        'correct_index' => $e['correct_index'] ?? 0
                    ];
                }
                $clean = $this->postProcessJsonQuestions($merged);
            }
        }

        // إذا بقي أقل من المطلوب، نكمّل من المرشّح Regex
        if (count($clean) < $desired && !empty($regexPreview)) {
            $have = [];
            foreach ($clean as $row) {
                $sig = md5($row['question'].'|'.implode('|',$row['options']));
                $have[$sig] = true;
            }

            foreach ($regexPreview as $r) {
                if (count($clean) >= $desired) break;
                $qArr = [
                    'question'       => $r['question'] ?? '',
                    'options'        => $r['options'] ?? [],
                    'correct_answer' => null,
                ];
                $pp = $this->postProcessJsonQuestions([[
                    'question'      => $qArr['question'],
                    'options'       => array_map(fn($o)=>$this->removeOptionLabelPrefix($o), $qArr['options']),
                    'correct_index' => 0,
                ]]);
                if ($pp) {
                    $sig = md5($pp[0]['question'].'|'.implode('|',$pp[0]['options']));
                    if (!isset($have[$sig])) {
                        $clean[] = $pp[0];
                        $have[$sig] = true;
                    }
                }
            }
        }

        if (!count($clean)) return false;

        // حفظ مع sort تصاعدي
        $sort = (int)($document->questions()->max('sort') ?? -1) + 1;
        foreach ($clean as $row) {
            DocumentQuestion::create([
                'document_id'    => $document->id,
                'question'       => $row['question'],
                'options'        => $row['options'],        // بدون A/B/C/D
                'correct_answer' => $row['correct_answer'], // نص الخيار الصحيح كما هو
                'sort'           => $sort++,
            ]);
        }
        return true;
    }

    private function generateAdditionalQuestions(string $fullText, array $existing, string $language, int $needed = 20): array
    {
        $existingQs = array_map(fn($r)=>$r['question'], $existing);
        $existingJoined = implode("\n- ", array_slice($existingQs, 0, 120));

        $isArabic = ($language === 'arabic');
        $sys = $isArabic
            ? <<<SYS
أنت مساعد تعليمي. من النص الكامل التالي، وبتجنب تكرار الأفكار/الأسئلة التالية، ولّد أسئلة اختيار من متعدد جديدة وارجع JSON فقط:

{"questions":[
  {"question":"<= 220 حرف","options":["خيار 1","خيار 2","خيار 3","خيار 4"],"correct_index":0}
]}

القواعد:
- أخرج {$needed} سؤالًا جديدًا قدر الإمكان (بدون تكرار معنى).
- لا تُرجع أي نص خارج JSON.
- الأسئلة والخيارات قصيرة وواضحة (2 إلى 6 خيارات).
- لا تضع A/B/C داخل نص الخيار.
- correct_index هو رقم الخيار الصحيح.
SYS
            : <<<SYS
You are an educational assistant. From the FULL text, and avoiding duplicating the following existing questions/ideas, generate NEW multiple-choice questions and return **JSON only**:

{"questions":[
  {"question":"<= 220 chars","options":["opt1","opt2","opt3","opt4"],"correct_index":0}
]}

Rules:
- Generate {$needed} new questions if possible (no semantic duplicates).
- No text outside JSON.
- Keep questions/options concise (2..6 options).
- Do not include A/B/C letters inside option text.
- correct_index is the correct option index.
SYS;

        try {
            $userPrompt = ($isArabic
                ? "النص الكامل:\n".$fullText."\n\nتجنّب تكرار هذه الأسئلة/الأفكار:\n- ".$existingJoined
                : "FULL TEXT:\n".$fullText."\n\nAvoid duplicating these questions/ideas:\n- ".$existingJoined
            );

            $resp = OpenAI::chat()->create([
                'model' => env('OPENAI_MODEL','gpt-4o-mini'),
                'messages' => [
                    ['role' => 'system', 'content' => $sys],
                    ['role' => 'user',   'content' => mb_substr($userPrompt, 0, 12000)],
                ],
                'temperature'     => 0.2,
                'response_format' => ['type' => 'json_object'],
                'max_tokens'      => 2500,
            ]);

            $raw  = $resp['choices'][0]['message']['content'] ?? '{}';
            $json = json_decode($raw, true);

            $out = [];
            if (is_array($json) && isset($json['questions']) && is_array($json['questions'])) {
                foreach ($json['questions'] as $q) {
                    if (!isset($q['question'], $q['options'], $q['correct_index'])) continue;
                    $opts = array_values(array_filter(array_map('trim', (array)$q['options'])));
                    if (count($opts) < 2) continue;
                    $ci = (int)$q['correct_index'];
                    if ($ci < 0 || $ci >= count($opts)) continue;

                    $out[] = [
                        'question'      => trim((string)$q['question']),
                        'options'       => $opts,
                        'correct_index' => $ci,
                    ];
                }
            }
            return $out;
        } catch (\Throwable $e) {
            Log::warning('generateAdditionalQuestions failed: '.$e->getMessage());
            return [];
        }
    }

    /* ====================== إزالة ترميز الحروف من الخيارات ====================== */

    private function removeOptionLabelPrefix(string $s): string
    {
        $s = $this->toUtf8($s);
        $s = trim($s);

        // احذف البادئات الشائعة: A)  B)  C)  D)  أو أ) ب) ج) د) وكذلك "A ." / "أ ."
        $s = preg_replace('/^\s*(?:[A-D]|[A-F]|[أبجده])\s*[\)\.\-]\s*/u', '', $s);

        // في بعض حالات RTL قد تظهر "(A" أو "A)" في نهاية النص — احذفها
        $s = preg_replace('/\s*[()\s]*[A-D]\)?\s*$/u', '', $s);   // لاتيني
        $s = preg_replace('/\s*[()\s]*[أبجده]\)?\s*$/u', '', $s); // عربي

        return trim($s);
    }

    private function postProcessJsonQuestions(array $items): array
    {
        $out = [];
        foreach ($items as $q) {
            $question = trim((string)($q['question'] ?? ''));
            if ($question === '' || mb_strlen($question) > 350) continue;

            // نظّف الخيارات من أي ترميز (A) / أ)
            $optsRaw = (array)($q['options'] ?? []);
            $opts = array_values(array_filter(array_map(function ($s) {
                return $this->removeOptionLabelPrefix((string)$s);
            }, $optsRaw)));

            if (count($opts) < 2) continue;

            $ci = (int)($q['correct_index'] ?? -1);
            $correct = ($ci >= 0 && $ci < count($opts)) ? $opts[$ci] : null;

            $out[] = [
                'question'       => $question,
                'options'        => $opts,     // بدون A/B/C/D
                'correct_answer' => $correct,  // نص الخيار الصحيح
            ];
        }

        // إزالة التكرار
        $seen = [];
        $unique = [];
        foreach ($out as $row) {
            $sig = md5($row['question'].'|'.implode('|',$row['options']));
            if (isset($seen[$sig])) continue;
            $seen[$sig] = true;
            $unique[] = $row;
        }

        return array_slice($unique, 0, 500);
    }

    /* ====================== Regex Fallback ====================== */

    private function extractWithRegex(Document $document, string $text): bool
    {
        $qs = $this->basicExtract($text);
        if (!count($qs)) return false;

        $sort = (int)($document->questions()->max('sort') ?? -1) + 1;
        foreach ($qs as $q) {
            $this->saveQuestion($document->id, $q, $sort++);
        }
        return true;
    }

    private function basicExtract(string $text): array
    {
        $text = $this->normalizePdfText($text);

        $blocks = preg_split("/(?=\n?(?:\d{1,3})[\)\.\-]\s)/u", $text, -1, PREG_SPLIT_NO_EMPTY);

        $out = [];
        $optPattern = "/(?:^|\n)\s*([A-Dأبجده])\)\s*(.+?)(?=(?:\n\s*[A-Dأبجده]\)|$))/u";
        foreach ($blocks as $b) {
            $b = trim($b);
            if ($b === '') continue;

            if (preg_match_all($optPattern, $b, $m, PREG_SET_ORDER)) {
                $firstPos  = mb_strpos($b, $m[0][0]);
                $qText     = $firstPos !== false ? trim(mb_substr($b, 0, $firstPos)) : $b;
                $qText     = preg_replace('/^(?:\d{1,3}[\)\.\-]\s*)/u', '', $qText);
                $qText     = trim($qText, " \t\n\r\0\x0B-–—:،؛.");

                $opts = [];
                foreach ($m as $hit) {
                    $label = $hit[1];
                    $body  = $this->removeOptionLabelPrefix((string)$hit[2]);
                    $opts[] = $body;
                }

                if ($qText && count($opts) >= 2) {
                    $out[] = ['question' => $qText, 'options' => array_slice($opts, 0, 4)];
                }
            }
        }
        return $out;
    }

    /* ====================== Save helpers ====================== */

    private function prepareQuestionArray(array $q): array
    {
        $question = $this->toUtf8((string)($q['question'] ?? ''));

        // نظّف الخيارات من أي ترميز حرفي
        $options  = $this->toUtf8Array($q['options'] ?? []);
        $options  = array_values(array_filter(array_map(function ($s) {
            return $this->removeOptionLabelPrefix((string)$s);
        }, $options)));

        // صحّح الإجابة لو أُرسلت وبداخلها حروف
        $correct  = isset($q['correct_answer']) && $q['correct_answer'] !== ''
            ? $this->removeOptionLabelPrefix((string)$q['correct_answer'])
            : null;

        return [
            'question'       => $question,
            'options'        => $options,   // بدون A/B/C/D
            'correct_answer' => ($correct === '?' || $correct === '') ? null : $correct,
        ];
    }

    private function saveQuestion(int $documentId, array $q, int $sort = 0): void
    {
        $clean = $this->prepareQuestionArray($q);

        // التأكد من صحة الترميز قبل الحفظ
        $question = $this->toUtf8($clean['question']);
        $options = array_map([$this, 'toUtf8'], $clean['options']);
        $correctAnswer = $clean['correct_answer'] ? $this->toUtf8($clean['correct_answer']) : null;

        DocumentQuestion::create([
            'document_id'    => $documentId,
            'question'       => $question,
            'options'        => $options,
            'correct_answer' => $correctAnswer,
            'sort'           => $sort,
        ]);
    }
}

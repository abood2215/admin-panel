<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Document, Stream, Year};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class BrowseController extends Controller
{
    /* 1) شاشة اختيار الفرع */
    public function streams(Request $request)
    {
        $streams = \App\Models\Stream::select('id','slug','name_ar','name_en')->orderBy('id')->get();

        // عدد الملفات داخل كل فرع (يعرض على الكرت)
        $counts = Document::selectRaw('stream_id, COUNT(*) as c')
            ->groupBy('stream_id')->pluck('c','stream_id');

        return view('admin.browse.streams', compact('streams','counts'));
    }

    /* 2) شاشة السنوات + الفصول المتوفّرة فعلاً */
    public function yearSemesters(Stream $stream)
    {
        // نجلب السنوات من جدول years (عن طريق year_id) لنتفادى عمود year غير الموجود
        $years = Document::query()
            ->where('stream_id', $stream->id)
            ->join('years', 'years.id', '=', 'documents.year_id')
            ->select('years.year')
            ->distinct()
            ->orderBy('years.year', 'asc')
            ->pluck('years.year');

        $hasSemester = Schema::hasColumn('documents','semester');

        // دالة لتطبيع قيمة الفصل إلى first/second
        $normalize = function (?string $raw): ?string {
            if ($raw === null) return null;
            $r = trim(mb_strtolower($raw));
            $firsts  = ['first','1','الأول','اول','الفصل الاول','الفصل الأول'];
            $seconds = ['second','2','الثاني','ثاني','الفصل الثاني'];
            if (in_array($r, $firsts, true))  return 'first';
            if (in_array($r, $seconds, true)) return 'second';
            return null; // أي قيم غريبة نتجاهلها
        };

        $blocks = [];
        foreach ($years as $yr) {
            $semesters = collect();

            if ($hasSemester) {
                // نجلب القيم المميّزة للفصول لهذه السنة والفرع ثم نطبّعها
                $rawSems = Document::query()
                    ->where('documents.stream_id', $stream->id)
                    ->join('years', 'years.id', '=', 'documents.year_id')
                    ->where('years.year', $yr)
                    ->select('documents.semester')
                    ->distinct()
                    ->pluck('documents.semester');

                $semesters = $rawSems
                    ->map($normalize)
                    ->filter()          // نحذف null
                    ->unique()
                    ->sort()
                    ->values();         // مثل ['first'] أو ['first','second']
            } else {
                // لو ما في عمود semester نعتبر أن كل الملفات بدون فصل محدد
                $semesters = collect(['first','second']); // أو حط واحد فقط إذا حاب
            }

            if ($semesters->isNotEmpty()) {
                $blocks[] = [
                    'year'      => (int)$yr,
                    'semesters' => $semesters,
                ];
            }
        }

        return view('admin.browse.year_semesters', [
            'stream' => $stream,
            'blocks' => $blocks,
        ]);
    }

    /* 3) شاشة عرض الملفات حسب (الفرع/السنة/الفصل) */
    public function documents(Request $request, Stream $stream, $year, $semester)
    {
        $hasSemester = Schema::hasColumn('documents','semester');

        // نحصل على year_id من قيمة السنة
        $yearId = Year::where('year', (int)$year)->value('id');
        abort_unless($yearId, 404);

        $query = Document::with(['subject'])
            ->where('stream_id', $stream->id)
            ->where('year_id', $yearId);

        if ($hasSemester && in_array($semester, ['first','second'], true)) {
            // مجموعة القيم المحتملة داخل قاعدة البيانات لكلا الفصلين
            $rawMap = [
                'first'  => ['first','1','الأول','اول','الفصل الاول','الفصل الأول'],
                'second' => ['second','2','الثاني','ثاني','الفصل الثاني'],
            ];
            $query->whereIn('semester', $rawMap[$semester]);
        }

        $documents = $query->orderByDesc('created_at')->get();

        return view('admin.browse.documents', [
            'stream'     => $stream,
            'year'       => (int)$year,
            'semester'   => $semester,
            'documents'  => $documents,
            'hasSemester'=> $hasSemester,
        ]);
    }
}

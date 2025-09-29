<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Specialty, Year, Document, Subject};

class SpecialtyAdminController extends Controller
{
    /**
     * الشاشة 1: قائمة جميع التخصصات (مع الفرع).
     */
    public function index()
    {
        // نجلب التخصصات مع الفرع التابع لها
        $specialties = Specialty::with('stream')
            ->orderBy('stream_id')
            ->orderBy('name_ar')
            ->get();

        return view('admin.specialties.index', compact('specialties'));
    }

    /**
     * الشاشة 2: اختيار السنة والفصل لتخصص معيّن.
     */
    public function choose(Specialty $specialty)
    {
        // سنوات النظام كلها (أو حدّد المتاح فقط)
        $years = Year::orderBy('year', 'desc')->get();

        // الفصول المتاحة (لو عندك جدول خاص للفصول استخدمه هنا)
        $semesters = [
            1 => app()->getLocale()==='ar' ? 'الفصل الأول' : 'Semester 1',
            2 => app()->getLocale()==='ar' ? 'الفصل الثاني' : 'Semester 2',
        ];

        return view('admin.specialties.choose', compact('specialty', 'years', 'semesters'));
    }

    /**
     * الشاشة 3: لوحة المستندات حسب (التخصص + السنة + الفصل).
     * ملاحظة: هذا الأكشن يفترض وجود عمود semester في جدول documents.
     * لو ما أضفته بعد، علّق سطر where('semester', ...) مؤقتًا.
     */
    public function board(Specialty $specialty, $year, $semester)
    {
        $yearNumber = (int) $year;

        $documents = Document::query()
            ->with(['subject','year','stream'])
            ->where('specialty_id', $specialty->id)
            ->whereHas('year', fn($q)=>$q->where('year', $yearNumber))
            ->when(schema_has_column('documents', 'semester') /* helper صغير بالأسفل */, function ($q) use ($semester) {
                $q->where('semester', (int) $semester);
            })
            ->orderByDesc('created_at')
            ->get();

        // لعرض المواد الموجودة ضمن هذه الفئة
        $subjectIds = $documents->pluck('subject_id')->filter()->unique()->values();
        $subjects = Subject::whereIn('id', $subjectIds)->get();

        return view('admin.specialties.board', compact('specialty', 'yearNumber', 'semester', 'documents', 'subjects'));
    }
}

/**
 * Helper بسيط للتأكد من وجود عمود قبل استخدامه (بدون رمي استثناء).
 * بإمكانك نقلها لـ helpers عام إن رغبت.
 */
if (!function_exists('schema_has_column')) {
    function schema_has_column(string $table, string $column): bool
    {
        try {
            return \Illuminate\Support\Facades\Schema::hasColumn($table, $column);
        } catch (\Throwable $e) {
            return false;
        }
    }
}

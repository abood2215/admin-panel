<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Document, Year, Stream};

class MobileBrowseController extends Controller
{
    public function years(Request $request)
    {
        $rtl = app()->getLocale() === 'ar';

        // فلاتر اختيارية
        $streamSlug = $request->query('stream');   // scientific | literary
        $subjectId  = $request->query('subject');  // id من subjects (اختياري)

        $streamId = null;
        if ($streamSlug) {
            $streamId = Stream::where('slug', $streamSlug)->value('id');
        }

        // السنوات التي لديها ملفات وفق الفلاتر
        $years = Year::select('years.id','years.year')
            ->join('documents','documents.year_id','=','years.id')
            ->when($streamId, fn($q)=>$q->where('documents.stream_id',$streamId))
            ->when($subjectId, fn($q)=>$q->where('documents.subject_id',$subjectId))
            ->groupBy('years.id','years.year')
            ->orderBy('years.year','desc')
            ->get();

        // يمكنك تحديد النص الظاهر تحت السنة (الفصل)
        $termLabel = $rtl ? 'الفصل الأول' : 'Term 1'; // غيّرها لاحقاً لو عندك حقل فصل فعلي

        return view('mobile.years', compact('years','termLabel','rtl','streamSlug','subjectId'));
    }
}

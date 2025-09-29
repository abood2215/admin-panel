<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Stream,Year,Specialty,Subject,Document};
use Illuminate\Http\Request;

class LibraryController extends Controller
{
  public function streams() {
    $streams = Stream::select('id','slug','name_ar','name_en')->get();
    return view('admin.library.streams', compact('streams'));
  }

  public function years(Stream $stream) {
    // سنين موجود لها ملفات أو كل السنوات؟ هنا نعرض الكل (2007..)
    $years = Year::orderBy('year','desc')->get();
    return view('admin.library.years', compact('stream','years'));
  }

  public function specialties(Stream $stream, $year) {
    $yearModel = Year::where('year', (int)$year)->firstOrFail();

    // تخصّصات تحت الفرع مع عدد المواد المتوفرة لهذه السنة
    $specialties = Specialty::where('stream_id',$stream->id)
      ->withCount(['subjects as subjects_count' => function($q) use ($yearModel){
        $q->whereHas('documents', fn($qd)=>$qd->where('year_id',$yearModel->id));
      }])->get();

    return view('admin.library.specialties', compact('stream','yearModel','specialties'));
  }

  public function subjects(Stream $stream, $year, Specialty $specialty) {
    $yearModel = Year::where('year',(int)$year)->firstOrFail();
    $subjects = Subject::where('specialty_id',$specialty->id)
      ->withCount(['documents' => fn($q)=>$q->where('year_id',$yearModel->id)])->get();

    return view('admin.library.subjects', compact('stream','yearModel','specialty','subjects'));
  }

  public function documents(Stream $stream, $year, Specialty $specialty, Subject $subject) {
    $yearModel = Year::where('year',(int)$year)->firstOrFail();

    $documents = Document::where([
      'stream_id'    => $stream->id,
      'year_id'      => $yearModel->id,
      'specialty_id' => $specialty->id,
      'subject_id'   => $subject->id,
    ])->latest()->paginate(20);

    return view('admin.library.documents', compact('stream','yearModel','specialty','subject','documents'));
  }

  // APIs للرفع (سلاسل معتمدة)
  public function apiSpecialties(Stream $stream) {
    return Specialty::where('stream_id',$stream->id)
      ->get(['id','name_ar','name_en']);
  }
  public function apiSubjects(Specialty $specialty) {
    return Subject::where('specialty_id',$specialty->id)
      ->get(['id','name_ar','name_en']);
  }
}

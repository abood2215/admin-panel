{{-- resources/views/admin/documents/show.blade.php --}}
@extends('layouts.admin')
@section('title', $document->title)

@section('content')
<div class="flex items-center justify-between mb-4">
  <h1 class="text-2xl font-bold">{{ $document->title }}</h1>
  <form action="{{ route('admin.documents.reextract',$document) }}" method="post">
    @csrf
    <button class="btn btn-outline">Re-extract</button>
  </form>
</div>

{{-- إضافة سؤال جديد --}}
<div class="card bg-base-200 p-4 mb-6">
  <form action="{{ route('admin.documents.questions.store',$document) }}" method="post" class="space-y-3">
    @csrf
    <input name="question" class="input input-bordered w-full" placeholder="Question text" required>

    <div class="grid md:grid-cols-2 gap-2">
      <input name="options[]" class="input input-bordered" placeholder="Option A">
      <input name="options[]" class="input input-bordered" placeholder="Option B">
      <input name="options[]" class="input input-bordered" placeholder="Option C">
      <input name="options[]" class="input input-bordered" placeholder="Option D">
    </div>

    <input name="correct_answer" class="input input-bordered w-full" placeholder="Correct answer (must equal one option)">
    <div class="text-xs opacity-70">* اكتُب الإجابة الصحيحة مطابقة لأحد الخيارات كما هو.</div>

    <button class="btn btn-primary">Add Question</button>
  </form>
</div>

{{-- قائمة الأسئلة --}}
@foreach($document->questions as $q)
  @php
    // خيارات نظيفة (بدون A) ...)
    $opts = $q->options_plain;
  @endphp
  <div class="card bg-base-100 p-4 mb-3">
    <form action="{{ route('admin.documents.questions.update', [$document, $q]) }}" method="post" class="space-y-3">
      @csrf
      <div>
        <label class="block text-sm font-semibold mb-1">Question Text</label>
        <textarea name="question" rows="3" class="textarea textarea-bordered w-full">{{ $q->question }}</textarea>
      </div>

      <div>
        <label class="block text-sm font-semibold mb-1">Options</label>
        <div class="grid md:grid-cols-2 gap-2">
          @for($i=0;$i<4;$i++)
            <input name="options[]" value="{{ $opts[$i] ?? '' }}" class="input input-bordered" placeholder="Option {{ chr(65+$i) }}">
          @endfor
        </div>
      </div>

      <div>
        <label class="block text-sm font-semibold mb-1">Correct Answer</label>
        <input name="correct_answer" value="{{ $q->correct_answer_plain }}" class="input input-bordered w-full" placeholder="Must equal one of the options">
        <div class="text-xs opacity-70 mt-1">* تُخزَّن كنصّ صافي. عند العرض أمام المستخدم يضاف بادج الحرف فقط.</div>
      </div>

      <div class="flex items-center gap-2">
        <input name="sort" type="number" value="{{ $q->sort ?? '' }}" class="input input-bordered w-24" placeholder="#"/>
        <button class="btn btn-success btn-sm">Save</button>
        <form action="{{ route('admin.documents.questions.destroy', [$document, $q]) }}" method="post" onsubmit="return confirm('Delete?')">
          @csrf @method('DELETE')
          <button class="btn btn-error btn-sm">Delete</button>
        </form>
      </div>
    </form>
  </div>
@endforeach
@endsection

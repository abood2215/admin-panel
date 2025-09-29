@extends('layouts.app')

@section('content')
<h2>إدارة أسئلة: {{ $document->title }}</h2>

@foreach($document->questions as $q)
    <form method="POST" action="{{ route('admin.documents.updateQuestion', [$document->id, $q->id]) }}">
        @csrf
        <input type="text" name="question" value="{{ $q->question }}">
        <textarea name="options[]">{{ implode("\n", $q->options ?? []) }}</textarea>
        <input type="text" name="correct_answer" value="{{ $q->correct_answer }}">
        <button type="submit">تحديث</button>
    </form>
    <form method="POST" action="{{ route('admin.documents.destroyQuestion', [$document->id, $q->id]) }}">
        @csrf @method('DELETE')
        <button type="submit">حذف</button>
    </form>
@endforeach
@endsection

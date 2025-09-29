{{-- resources/views/admin/documents/create.blade.php --}}
@extends('layouts.admin')

@section('title','Upload Document')
@section('content')
<div class="max-w-2xl mx-auto space-y-4">
  <h1 class="text-2xl font-bold">Upload PDF</h1>
  <form action="{{ route('admin.documents.store') }}" method="post" enctype="multipart/form-data" class="space-y-3">
    @csrf
    <input type="text" name="title" class="input input-bordered w-full" placeholder="Title" required>
    <select name="language" class="select select-bordered w-full" required>
      <option value="english">English</option>
      <option value="arabic">Arabic</option>
    </select>
    <input type="file" name="document" accept="application/pdf" class="file-input file-input-bordered w-full" required>
    <button class="btn btn-primary">Upload</button>
  </form>
</div>
@endsection

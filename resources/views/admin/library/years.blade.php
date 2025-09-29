@extends('layouts.app')
@section('content')
<div class="container py-4" dir="{{ app()->getLocale()==='ar' ? 'rtl' : 'ltr' }}">
  <a class="btn btn-outline-secondary mb-3" href="{{ route('admin.library.streams') }}">
    <i class="fa fa-arrow-{{ app()->getLocale()==='ar'?'right':'left' }}"></i> {{ __('رجوع') }}
  </a>
  <h5 class="fw-bold mb-3">
    {{ __('اختر السنة') }} — {{ app()->getLocale()==='ar' ? $stream->name_ar : $stream->name_en }}
  </h5>
  <div class="d-flex flex-wrap gap-2">
    @foreach($years as $y)
      <a class="btn btn-primary"
         href="{{ route('admin.library.specialties', [$stream->slug, $y->year]) }}">
        {{ $y->year }}
      </a>
    @endforeach
  </div>
</div>
@endsection

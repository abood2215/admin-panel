@extends('layouts.app')
@section('content')
<div class="container py-4" dir="{{ app()->getLocale()==='ar' ? 'rtl' : 'ltr' }}">
  <a class="btn btn-outline-secondary mb-3" href="{{ route('admin.library.specialties', [$stream->slug, $yearModel->year]) }}">
    <i class="fa fa-arrow-{{ app()->getLocale()==='ar'?'right':'left' }}"></i> {{ __('رجوع') }}
  </a>

  <h5 class="fw-bold mb-3">
    {{ __('اختر المادة') }} — {{ $yearModel->year }} /
    {{ app()->getLocale()==='ar' ? $stream->name_ar : $stream->name_en }} /
    {{ app()->getLocale()==='ar' ? $specialty->name_ar : $specialty->name_en }}
  </h5>

  <div class="row g-3">
    @foreach($subjects as $sj)
      <div class="col-12 col-md-6 col-lg-4">
        <a class="card card-body h-100"
           href="{{ route('admin.library.documents', [$stream->slug, $yearModel->year, $specialty->id, $sj->id]) }}">
          <div class="fw-800">{{ app()->getLocale()==='ar' ? $sj->name_ar : $sj->name_en }}</div>
          <div class="text-muted small">{{ __('عدد المستندات') }}: {{ $sj->documents_count }}</div>
        </a>
      </div>
    @endforeach
  </div>
</div>
@endsection

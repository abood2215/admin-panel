@extends('layouts.app')
@section('content')
<div class="container py-4" dir="{{ app()->getLocale()==='ar' ? 'rtl' : 'ltr' }}">
  <a class="btn btn-outline-secondary mb-3" href="{{ route('admin.library.years',$stream->slug) }}">
    <i class="fa fa-arrow-{{ app()->getLocale()==='ar'?'right':'left' }}"></i> {{ __('رجوع') }}
  </a>
  <h5 class="fw-bold mb-3">{{ __('اختر التخصص') }} — {{ $yearModel->year }} / {{ app()->getLocale()==='ar' ? $stream->name_ar : $stream->name_en }}</h5>

  <div class="row g-3">
    @forelse($specialties as $sp)
      <div class="col-12 col-md-6">
        <a class="card card-body h-100" href="{{ route('admin.library.subjects', [$stream->slug, $yearModel->year, $sp->id]) }}">
          <div class="fw-800">{{ app()->getLocale()==='ar' ? $sp->name_ar : $sp->name_en }}</div>
          <div class="text-muted small">{{ __('عدد المواد المرتبطة هذه السنة') }}: {{ $sp->subjects_count }}</div>
        </a>
      </div>
    @empty
      <div class="text-muted">{{ __('لا توجد تخصصات') }}</div>
    @endforelse
  </div>
</div>
@endsection

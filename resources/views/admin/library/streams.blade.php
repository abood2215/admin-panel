@extends('layouts.app')
@section('content')
<div class="container py-4" dir="{{ app()->getLocale()==='ar' ? 'rtl' : 'ltr' }}">
  <h4 class="fw-bold mb-3">{{ __('اختر الفرع') }}</h4>
  <div class="row g-3">
    @foreach($streams as $s)
      <div class="col-12 col-md-6">
        <a class="card card-body h-100" href="{{ route('admin.library.years',$s->slug) }}" style="text-decoration:none">
          <div class="d-flex align-items-center gap-3">
            <i class="fas fa-layer-group fa-2x text-primary"></i>
            <div>
              <div class="fw-800">{{ app()->getLocale()==='ar' ? $s->name_ar : $s->name_en }}</div>
              <div class="text-muted small">{{ $s->slug }}</div>
            </div>
          </div>
        </a>
      </div>
    @endforeach
  </div>
</div>
@endsection

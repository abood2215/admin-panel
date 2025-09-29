@extends('layouts.app')

@section('content')
<style>
.grid{display:grid;gap:12px;grid-template-columns:repeat(auto-fill,minmax(230px,1fr))}
.card{background:var(--panel,#0f172a);border:1px solid var(--border,#1f2937);border-radius:14px;padding:14px}
.badge{display:inline-block;background:color-mix(in srgb, #6366f1 12%, transparent);border:1px solid color-mix(in srgb,#6366f1 30%, #1f2937);padding:.2rem .5rem;border-radius:999px;color:#e5e7eb;font-weight:800;font-size:.75rem}
.btn{display:inline-flex;align-items:center;gap:.5rem;padding:.55rem .75rem;border-radius:.6rem;border:1px solid var(--border,#1f2937);background:#1f2435;color:#e5e7eb;font-weight:800}
.btn:hover{filter:brightness(1.05)}
.title{font-weight:900;color:#e5e7eb;margin:.4rem 0}
</style>

<div class="container py-4" dir="{{ app()->getLocale()==='ar'?'rtl':'ltr' }}">
  <h3 class="fw-bold mb-3" style="color:#e5e7eb">
    {{ app()->getLocale()==='ar' ? 'اختر التخصص' : 'Choose a Specialty' }}
  </h3>

  <div class="grid">
    @forelse($specialties as $sp)
      <div class="card">
        <div class="badge">
          <i class="fa-solid fa-layer-group"></i>
          {{ app()->getLocale()==='ar' ? ($sp->stream->name_ar ?? '—') : ($sp->stream->name_en ?? '—') }}
        </div>

        <div class="title">
          {{ app()->getLocale()==='ar' ? $sp->name_ar : $sp->name_en }}
        </div>

        <a class="btn" href="{{ route('admin.specialties.choose', $sp->id) }}">
          <i class="fa-solid fa-arrow-{{ app()->getLocale()==='ar' ? 'left' : 'right' }}"></i>
          {{ app()->getLocale()==='ar' ? 'متابعة' : 'Continue' }}
        </a>
      </div>
    @empty
      <div class="text-muted">{{ app()->getLocale()==='ar' ? 'لا توجد تخصصات' : 'No specialties found' }}</div>
    @endforelse
  </div>
</div>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
@endsection

{{-- resources/views/admin/browse/streams.blade.php --}}
@extends('layouts.app')

@section('content')
@php
  $rtl = app()->getLocale()==='ar';
@endphp
<style>
:root{
  --bg:#0b1220;--panel:#0f172a;--panel-2:#111827;--border:#1f2937;
  --text:#e5e7eb;--muted:#94a3b8;--primary:#6366f1;--primary-2:#4f46e5;
}
html[data-theme="light"]{
  --bg:#f6f7fb;--panel:#fff;--panel-2:#f8fafc;--border:#e5e7eb;
  --text:#0f172a;--muted:#475569;--primary:#4f46e5;--primary-2:#4338ca;
}

.page{direction:{{ $rtl ? 'rtl' : 'ltr' }}}
.page::before{content:"";position:fixed;inset:0;background:var(--bg);z-index:-1}
.wrap{max-width:980px;margin-inline:auto;background:var(--panel);border:1px solid var(--border);border-radius:1rem;padding:1rem 1.25rem}
.header{display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem}
.title{color:var(--text);font-weight:900;margin:0}
.sub{color:var(--muted);font-weight:700;margin-top:.25rem}
.btn{padding:.55rem .9rem;border-radius:.75rem;border:1px solid var(--border);background:var(--panel-2);color:var(--text);font-weight:700}
.btn:hover{filter:brightness(1.05)}
.btn-primary{background:var(--primary);border:0;color:#fff}
.btn-primary:hover{background:var(--primary-2)}

.grid{display:grid;gap:1rem;grid-template-columns:repeat(12,1fr);margin-top:.75rem}
.col-12{grid-column:span 12}
@media(min-width:780px){ .col-6{grid-column:span 6} }
@media(min-width:1024px){ .col-4{grid-column:span 4} }

.card{background:var(--panel-2);border:1px solid var(--border);border-radius:1rem;padding:1rem}
.card h5{margin:0 0 .5rem 0;color:var(--text);font-weight:900}
.meta{display:flex;align-items:center;gap:.5rem;color:var(--muted);font-size:.9rem;margin-bottom:.75rem}
.badge{background:transparent;border:1px solid var(--border);border-radius:999px;padding:.25rem .55rem}
.actions{display:flex;gap:.5rem;flex-wrap:wrap}
.chip{border:1px solid var(--border);background:transparent;color:var(--text);padding:.45rem .8rem;border-radius:.7rem;font-weight:700}
.chip:hover{border-color:var(--primary);color:#fff;background:var(--primary)}
</style>

<div class="container py-4 page">
  <div class="wrap">
    <div class="header">
      <div>
        <h4 class="title">{{ $rtl ? 'اختر الفرع' : 'Choose a stream' }}</h4>
        <div class="sub">{{ $rtl ? 'انتقل لاختيار السنة والفصل' : 'Go on to pick year & semester' }}</div>
      </div>

      {{-- رفع عام (النموذج الكامل) من لوحة الأدمن --}}
      <a href="{{ route('admin.documents.upload') }}" class="btn btn-primary">
        <i class="fa-solid fa-cloud-arrow-up"></i>
        {{ $rtl ? 'رفع جديد' : 'New upload' }}
      </a>
    </div>

    <div class="grid">
      @forelse($streams as $s)
        <div class="col-12 col-6 col-4">
          <div class="card">
            <h5>{{ $rtl ? ($s->name_ar ?? $s->name_en) : ($s->name_en ?? $s->name_ar) }}</h5>

            <div class="meta">
              <span class="badge">
                {{ $rtl ? 'ملف' : 'files' }} : {{ (int)($counts[$s->id] ?? 0) }}
              </span>
            </div>

            <div class="actions">
              {{-- متابعة -> صفحة السنة/الفصل --}}
              <a class="chip" href="{{ route('admin.browse.year_semesters', $s->slug) }}">
                {{ $rtl ? 'متابعة' : 'Browse' }}
                <i class="fa-solid fa-arrow-{{ $rtl ? 'left':'right' }}"></i>
              </a>

              {{-- رفع للفرع فقط (النموذج المبسّط سيقرأ stream من الـ query) --}}
              <a class="chip" href="{{ route('admin.documents.upload', ['stream' => $s->slug]) }}">
                <i class="fa-solid fa-cloud-arrow-up"></i>
                {{ $rtl ? 'رفع لهذا الفرع' : 'Upload for this stream' }}
              </a>
            </div>
          </div>
        </div>
      @empty
        <div class="col-12">
          <div class="card" style="text-align:center">
            {{ $rtl ? 'لا يوجد بيانات لعرضها' : 'No streams yet.' }}
          </div>
        </div>
      @endforelse
    </div>
  </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
@endpush
@endsection

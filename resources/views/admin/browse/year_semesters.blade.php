{{-- resources/views/admin/browse/year_semesters.blade.php --}}
@extends('layouts.app')

@section('content')
<style>
:root{
  --bg:#0b1220;--panel:#0f172a;--panel-2:#111827;--border:#1f2937;
  --text:#e5e7eb;--muted:#94a3b8;--primary:#6366f1;--primary-2:#4f46e5;--ok:#16a34a;
}
html[data-theme="light"]{
  --bg:#f6f7fb;--panel:#fff;--panel-2:#f8fafc;--border:#e5e7eb;
  --text:#0f172a;--muted:#475569;--primary:#4f46e5;--primary-2:#4338ca;--ok:#16a34a;
}

.page{direction:{{ app()->getLocale()==='ar' ? 'rtl':'ltr' }}}
.page::before{content:"";position:fixed;inset:0;background:var(--bg);z-index:-1}
.wrap{max-width:980px;margin-inline:auto;background:var(--panel);border:1px solid var(--border);border-radius:1rem;padding:1.25rem}
.header{display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem}
.title{color:var(--text);font-weight:800;margin:0}
.sub{color:var(--muted);font-weight:700;margin-top:.25rem}
.btn{padding:.55rem .9rem;border-radius:.75rem;border:1px solid var(--border);background:var(--panel-2);color:var(--text);font-weight:700}
.btn:hover{filter:brightness(1.05)}
.btn-primary{background:var(--primary);border:0;color:#fff}
.btn-primary:hover{background:var(--primary-2)}

.grid{display:grid;gap:1rem;grid-template-columns:repeat(12,1fr);margin-top:.75rem}
.col-12{grid-column:span 12}
.col-6{grid-column:span 12}
@media(min-width:780px){ .col-6{grid-column:span 6} }
@media(min-width:1024px){ .col-4{grid-column:span 4} }

.card{background:var(--panel-2);border:1px solid var(--border);border-radius:1rem;padding:1rem}
.year{display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem}
.year h5{margin:0;color:var(--text);font-weight:900}
.chips{display:flex;gap:.5rem;flex-wrap:wrap}
.chip{border:1px solid var(--border);background:transparent;color:var(--text);padding:.45rem .8rem;border-radius:.7rem;font-weight:700}
.chip:hover{border-color:var(--primary);color:#fff;background:var(--primary)}
.small{color:var(--muted);font-size:.9rem}
</style>

<div class="container py-4 page">
  <div class="wrap">
    <div class="header">
      <div>
        <h4 class="title">
          {{ app()->getLocale()==='ar' ? 'السنة والفصل —' : 'Year & Semester —' }}
          {{ app()->getLocale()==='ar' ? ($stream->name_ar ?? $stream->name_en) : ($stream->name_en ?? $stream->name_ar) }}
        </h4>
        <div class="sub">
          <a class="btn" href="{{ route('admin.browse.streams') }}">
            <i class="fa-solid fa-arrow-{{ app()->getLocale()==='ar' ? 'right':'left' }}"></i>
            {{ app()->getLocale()==='ar' ? 'رجوع' : 'Back' }}
          </a>
        </div>
      </div>

      <a class="btn btn-primary" href="{{ route('admin.documents.upload', ['stream'=>$stream->slug]) }}">
        <i class="fa-solid fa-cloud-arrow-up"></i>
        {{ app()->getLocale()==='ar' ? 'رفع لهذا الفرع' : 'Upload for this stream' }}
      </a>
    </div>

    @if(empty($blocks))
      <div class="card">
        <div class="small">{{ app()->getLocale()==='ar' ? 'لا يوجد بيانات لعرضها' : 'No data to display yet.' }}</div>
      </div>
    @else
      <div class="grid">
        @foreach($blocks as $b)
          <div class="col-12 col-6 col-4">
            <div class="card">
              <div class="year">
                <h5>{{ $b['year'] }}</h5>
                <span class="small">{{ app()->getLocale()==='ar' ? 'المتاح' : 'available' }}</span>
              </div>

              <div class="chips">
                @foreach($b['semesters'] as $sem)
                  <a class="chip"
                     href="{{ route('admin.browse.documents', [$stream->slug, $b['year'], $sem]) }}">
                    {{ $sem==='first'
                        ? (app()->getLocale()==='ar' ? 'الفصل الأول' : 'First')
                        : (app()->getLocale()==='ar' ? 'الفصل الثاني' : 'Second') }}
                  </a>
                @endforeach
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
@endpush
@endsection

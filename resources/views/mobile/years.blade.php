@extends('layouts.app')

@section('content')
<style>
  :root{
    --bg:#0B5C8E;
    --bg-grad: linear-gradient(180deg, #0B5C8E 0%, #4FA3D1 100%);
    --card:#0f3b5e;
    --card-hover:#10446c;
    --text:#fff;
    --muted:#dbe7f3;
  }
  body{ background: var(--bg-grad); }
  .mobile-shell{ max-width: 920px; margin: 0 auto; padding: 24px 16px; }
  .topbar{
    display:flex; align-items:center; justify-content:space-between; color:var(--text);
    font-weight:900; font-size:1.8rem; letter-spacing:.5px; margin: 8px 0 18px;
  }
  .topbar .arrow{ font-size:1.4rem; opacity:.9 }
  .grid{ display:grid; gap:16px; grid-template-columns: repeat(2, minmax(0, 1fr)); }
  @media (max-width:560px){ .grid{ grid-template-columns: 1fr; } }

  .card{
    min-height: 180px; border-radius:22px; background:var(--card);
    box-shadow: 0 10px 26px rgba(0,0,0,.25), inset 0 0 0 1px rgba(255,255,255,.06);
    color:var(--text); padding:24px; display:flex; align-items:flex-end; position:relative;
    transition:.22s transform, .22s filter, .22s background;
  }
  .card:hover{ transform: translateY(-4px); background:var(--card-hover); filter:brightness(1.02) }
  .card::before{
    content:''; position:absolute; inset:0; border-radius:22px;
    background: radial-gradient(120px 120px at 85% 18%, rgba(255,255,255,.12), transparent 70%);
    pointer-events:none;
  }
  .year{ font-size:1.6rem; font-weight:900; line-height:1.2 }
  .term{ margin-top:6px; font-size:1.2rem; font-weight:800; color:var(--muted) }
  .chip{
    display:inline-flex; gap:.45rem; align-items:center; padding:.35rem .6rem;
    border-radius:999px; font-weight:800; font-size:.8rem; color:#0b2a42; background:#e6f3fb;
  }
  .top-actions{ display:flex; gap:8px; align-items:center }
  .btn-back{
    display:inline-flex; align-items:center; gap:.45rem; color:#0b2a42; background:#e6f3fb;
    border:0; border-radius:12px; padding:.55rem .8rem; font-weight:800;
  }
</style>

<div class="mobile-shell" dir="{{ $rtl ? 'rtl' : 'ltr' }}">

  {{-- Header --}}
  <div class="topbar">
    <span>{{ $rtl ? 'الفصل والسنة' : 'Term & Year' }}</span>
    <div class="top-actions">
      <span class="chip">{{ $rtl ? 'PDF / TXT' : 'PDF / TXT' }}</span>
      <button class="btn-back" onclick="history.back()">
        <i class="fa-solid fa-arrow-{{ $rtl ? 'right' : 'left' }}"></i>
        {{ $rtl ? 'رجوع' : 'Back' }}
      </button>
    </div>
  </div>

  {{-- Grid of years --}}
  <div class="grid">
    @forelse($years as $y)
      @php
        // رابط للخطوة التالية (مثلاً شاشة المواد أو قائمة الملفات لتلك السنة)
        $nextUrl = route('admin.dashboard', [
          'year'   => $y->year,
          'stream' => $streamSlug,
          'subject'=> $subjectId,
        ]);
      @endphp
      <a class="card" href="{{ $nextUrl }}" aria-label="open year {{ $y->year }}">
        <div>
          <div class="year">{{ $rtl ? "توجيهي {$y->year}" : "Tawjihi {$y->year}" }}</div>
          <div class="term">{{ $termLabel }}</div>
        </div>
      </a>
    @empty
      <div style="grid-column:1/-1;color:#fff;opacity:.9; font-weight:800">
        {{ $rtl ? 'لا توجد سنوات متاحة تحت الفلاتر الحالية.' : 'No years found for current filters.' }}
      </div>
    @endforelse
  </div>
</div>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
@endsection

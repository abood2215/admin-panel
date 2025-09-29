{{-- resources/views/documents/index.blade.php --}}
@extends('layouts.app')

@php
  $rtl  = app()->getLocale() === 'ar';
  $dir  = $rtl ? 'rtl' : 'ltr';
  $tStart = $rtl ? 'text-end' : 'text-start';
@endphp

@section('content')
<style>
  :root{
    --bg:#0b1220;
    --panel:#0f172a;
    --panel-2:#0b1426;
    --border:#1f2937;
    --text:#e5e7eb;
    --muted:#9aa4b2;
    --chip:#0e1628;
    --accent:#5b5ef7;      /* Primary قريب من ألوان مشروعك */
    --accent-2:#7b5bff;    /* Gradient stop */
    --success:#16a34a;
    --warning:#f59e0b;
    --danger:#ef4444;
    --radius:16px;
    --shadow:0 14px 40px rgba(2,6,23,.45);
  }
  html[data-theme="light"]{
    --bg:#f6f7fb; --panel:#fff; --panel-2:#f9fafb; --border:#e5e7eb;
    --text:#0f172a; --muted:#475569; --chip:#eef2ff; --shadow:0 10px 24px rgba(2,6,23,.08);
  }
  body{background:var(--bg);}
  .wrap{max-width:1280px;margin:0 auto;padding:18px}

  /* ===== Filters ===== */
  .filters{
    background:var(--panel); border:1px solid var(--border); border-radius:var(--radius);
    padding:14px; box-shadow:var(--shadow)
  }
  .title{font-weight:900; color:var(--text)}
  .muted{color:var(--muted); font-weight:600}
  .ctrl{display:flex; gap:.75rem; align-items:center; flex-wrap:wrap}
  .select,.checkbox,.btn{
    border-radius:12px; border:1px solid var(--border); background:var(--panel-2);
    color:var(--text); padding:.6rem .85rem; font-weight:800
  }
  .select:focus{outline:0; border-color:var(--accent); box-shadow:0 0 0 3px color-mix(in srgb,var(--accent) 24%, transparent)}
  .btn{
    border:0; background:linear-gradient(135deg,var(--accent),var(--accent-2)); cursor:pointer;
    box-shadow:0 6px 18px color-mix(in srgb,var(--accent) 35%, transparent); transition:.2s
  }
  .btn:hover{transform:translateY(-1px)}

  /* ===== Grid (1 / 2 / 3) ===== */
  .grid{display:grid; gap:14px; margin-top:16px; grid-template-columns:1fr}
  @media (min-width: 820px){ .grid{grid-template-columns:repeat(2,1fr)} }
  @media (min-width: 1150px){ .grid{grid-template-columns:repeat(3,1fr)} }

  /* ===== Card ===== */
  .card{
    position:relative; background:var(--panel); border:1px solid var(--border); border-radius:var(--radius);
    overflow:hidden; box-shadow:var(--shadow); transition:transform .2s
  }
  .card:hover{ transform: translateY(-3px) }
  .card::before{content:''; position:absolute; inset:0 0 auto 0; height:4px;
    background:linear-gradient(90deg,var(--accent),var(--accent-2))}
  .card-body{padding:16px}
  .row{display:flex; align-items:center; justify-content:space-between; gap:.8rem; flex-wrap:wrap}
  .title-line{display:flex; align-items:center; gap:.6rem; flex-wrap:wrap; font-weight:900; color:var(--text)}

  /* Chips */
  .chip{display:inline-flex; align-items:center; gap:.4rem; padding:.28rem .6rem; border-radius:999px;
        font-weight:800; font-size:.78rem; background:var(--chip); border:1px solid var(--border); color:var(--muted)}
  .chip.qs{background:color-mix(in srgb,var(--accent) 12%, var(--chip)); color:#dbeafe; border-color:color-mix(in srgb,var(--accent) 28%, var(--border))}
  .chip.ok{background:color-mix(in srgb,var(--success) 18%, var(--chip)); color:#bbf7d0; border-color:color-mix(in srgb,var(--success) 40%, var(--border))}
  .chip.pend{background:#231b0b; color:#fde68a; border-color:#6b4b0a}
  .chip.proc{background:#0b1b23; color:#8ff3d7; border-color:#114e43}
  .chip.fail{background:#2a1010; color:#fecaca; border-color:#7f1d1d}

  .btn-soft{
    padding:.55rem .85rem; border-radius:12px; font-weight:800; border:1px solid var(--border);
    background:var(--panel-2); color:var(--text)
  }


  html[data-theme="light"]{
  /* نصوص أوضح */
  --text: #0b1220;       /* أغمق */
  --muted:#334155;       /* أغمق شوي */
  --panel:#ffffff;       /* خلفية الكرت */
  --panel-2:#f8fafc;     /* خلفية الحقول */
  --chip:#eef2ff;        /* خلفية الشارة الافتراضية */
  --border:#e5e7eb;
}

/* عناوين وروابط داخل الكرت تكون أوضح */
.card .title-line,
.card .title-line a{
  color: var(--text) !important;
}

/* أزرار وفلاتر نصها غامق في الفاتح */
html[data-theme="light"] .select,
html[data-theme="light"] .checkbox,
html[data-theme="light"] .btn-soft{
  color: var(--text);
}

/* الشيبس/التاجات: ألوان عالية التباين في الفاتح */
html[data-theme="light"] .chip{
  background: #eaf0ff;
  color: #0b1220;
  border-color: #c7d2fe;
}
html[data-theme="light"] .chip.qs{
  background:#e0e7ff;   /* عدّاد الأسئلة */
  color:#1e293b;
  border-color:#c7d2fe;
}
html[data-theme="light"] .chip.ok{
  background:#dcfce7;   /* processed */
  color:#065f46;
  border-color:#a7f3d0;
}
html[data-theme="light"] .chip.pend{
  background:#fff7d6;   /* pending */
  color:#854d0e;
  border-color:#fde68a;
}
html[data-theme="light"] .chip.proc{
  background:#e0f2fe;   /* processing */
  color:#0c4a6e;
  border-color:#bae6fd;
}
html[data-theme="light"] .chip.fail{
  background:#fee2e2;   /* failed */
  color:#991b1b;
  border-color:#fecaca;
}

/* زر Open يظل واضح على الفاتح */
html[data-theme="light"] .btn-soft:hover{
  border-color: #818cf8;
}
  .btn-soft:hover{ border-color:color-mix(in srgb,var(--accent) 35%, var(--border)) }

  [dir="rtl"] .row{ flex-direction: row-reverse }
</style>

<div class="wrap" dir="{{ $dir }}">
  {{-- Filters --}}
  <div class="filters mb-3">
    <div class="row">
      <div class="title">{{ __('documents.list_title') }}</div>
      <div class="muted">{{ __('documents.total_docs', ['n' => $documents->total()]) }}</div>
    </div>
    <form method="GET" class="mt-3">
      <div class="ctrl">
        <label>
          <div class="muted">{{ __('documents.language') }}</div>
          <select class="select" name="lang">
            <option value="all"     {{ request('lang','all')==='all'?'selected':'' }}>{{ __('documents.lang_all') }}</option>
            <option value="arabic"  {{ request('lang')==='arabic'?'selected':'' }}>{{ __('documents.lang_ar') }}</option>
            <option value="english" {{ request('lang')==='english'?'selected':'' }}>{{ __('documents.lang_en') }}</option>
          </select>
        </label>
        <label>
          <div class="muted">{{ __('documents.status') }}</div>
          <select class="select" name="status">
            <option value="all"        {{ request('status','all')==='all'?'selected':'' }}>{{ __('documents.status_all') }}</option>
            <option value="processed"  {{ request('status')==='processed'?'selected':'' }}>{{ __('documents.status_processed') }}</option>
            <option value="pending"    {{ request('status')==='pending'?'selected':'' }}>{{ __('documents.status_pending') }}</option>
            <option value="processing" {{ request('status')==='processing'?'selected':'' }}>{{ __('documents.status_processing') }}</option>
            <option value="failed"     {{ request('status')==='failed'?'selected':'' }}>{{ __('documents.status_failed') }}</option>
          </select>
        </label>

        <label style="display:flex; align-items:center; gap:.5rem; margin-inline-start:auto">
          <input type="checkbox" class="checkbox" name="mine" value="1" {{ request('mine') ? 'checked' : '' }}>
          <span class="muted">{{ __('documents.mine_only') }}</span>
        </label>

        <button class="btn" type="submit">{{ __('documents.filter') }}</button>
      </div>
    </form>
  </div>

  {{-- Three-per-row grid --}}
  <div class="grid">
    @forelse($documents as $doc)
      @php
        $qs  = $doc->questions_count ?? ($doc->questions->count() ?? 0);
        $st  = strtolower($doc->status ?? 'pending');
        $stClass = match($st){
          'processed'  => 'chip ok',
          'processing' => 'chip proc',
          'failed'     => 'chip fail',
          default      => 'chip pend',
        };
      @endphp

      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="title-line">
              <span>📄 {{ $doc->title }}</span>
              <span class="chip">{{ strtoupper($doc->language ?? '-') }}</span>
              <span class="chip qs">{{ $qs }} {{ __('documents.qs') }}</span>
            </div>
            <div class="row" style="gap:.6rem">
              <span class="{{ $stClass }}">
                @if($st==='processed') {{ __('documents.processed') }}
                @elseif($st==='processing') {{ __('documents.processing') }}
                @elseif($st==='failed') {{ __('documents.failed') }}
                @else {{ __('documents.pending') }} @endif
              </span>
              <a class="btn-soft" href="{{ route('documents.view', $doc->id) }}">{{ __('documents.open') }}</a>
            </div>
          </div>

          <div class="mt-2 {{ $tStart }} muted">
            {{ __('documents.uploaded_at') }}: {{ optional($doc->created_at)->format('Y-m-d') }}
          </div>
        </div>
      </div>
    @empty
      <div class="card"><div class="card-body {{ $tStart }}"><div class="muted">{{ __('documents.empty') }}</div></div></div>
    @endforelse
  </div>

  <div class="mt-3">{{ $documents->withQueryString()->links() }}</div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<style>
/* ====================== Clean Admin Dashboard ====================== */
.admin-dashboard{ direction: {{ app()->getLocale()==='ar' ? 'rtl' : 'ltr' }}; }

/* Theme Variables */
:root{
  --bg: #0b1220;
  --panel: #0f172a;
  --panel-2:#111827;
  --text:#e5e7eb;
  --muted:#9ca3af;
  --border:#1f2937;
  --primary:#6366f1;
  --primary-weak:#a5b4fc;
  --good:#16a34a;
  --good-bg:#062e1c;
  --warn:#f59e0b;
  --warn-bg:#2a1b05;
  --bad:#ef4444;
  --bad-bg:#2b0b0b;
}
html[data-theme="light"]{
  --bg:#f6f7fb;
  --panel:#ffffff;
  --panel-2:#f9fafb;
  --text:#0f172a;
  --muted:#475569;
  --border:#e5e7eb;
  --primary:#4f46e5;
  --primary-weak:#4f46e5;
  --good:#16a34a;
  --good-bg:#ecfdf5;
  --warn:#d97706;
  --warn-bg:#fff7ed;
  --bad:#dc2626;
  --bad-bg:#fff1f2;
}

.admin-dashboard::before{
  content:"";position:fixed;inset:0;z-index:-1;background:var(--bg);
}

/* ====================== Toolbar ====================== */
.toolbar{
  display:flex;justify-content:space-between;align-items:center;gap:.75rem;margin:1rem 0;
  position: sticky; top: .75rem; z-index: 5;
  backdrop-filter: blur(8px);
  background: color-mix(in srgb, var(--panel) 85%, transparent);
  border: 1px solid var(--border);
  border-radius: .8rem;
  padding: .6rem .8rem;
}
.toolbar h4{margin:0;color:var(--text);font-weight:800}
.chip{
  display:inline-flex;gap:.4rem;align-items:center;
  padding:.35rem .7rem;border-radius:999px;
  color:var(--muted);background:var(--panel-2);border:1px solid var(--border);
  font-weight:700;font-size:.78rem
}
.btn-soft{
  border:1px solid var(--border);background:var(--panel-2);color:var(--text);
  padding:.55rem .9rem;border-radius:.65rem;font-weight:700
}
.btn-soft:hover{filter:brightness(1.05)}
.btn-glow{
  background:var(--primary);color:#fff;border:0;border-radius:.65rem;
  padding:.6rem 1rem;font-weight:800
}
.btn-glow:hover{filter:brightness(1.05)}

/* ====================== Table ====================== */
.card-wrap{
  background:var(--panel);border:1px solid var(--border);border-radius:1rem;overflow:hidden
}
.card-header{
  background:var(--panel-2);border-bottom:1px solid var(--border);
  color:var(--text);padding:.9rem 1rem;font-weight:800;display:flex;gap:.5rem;align-items:center
}
.table{width:100%;border-collapse:separate;border-spacing:0}
.table thead th{
  text-align:start;padding:.85rem .8rem;border-bottom:1px solid var(--border);
  color:var(--muted);font-weight:800;letter-spacing:.3px;
  background: var(--panel-2);
  position: sticky; top: 0; z-index: 2;
}
.table td{padding:.8rem .8rem;border-bottom:1px solid var(--border);color:var(--text);vertical-align:middle}
.table tbody tr:hover{background:color-mix(in srgb, var(--primary) 10%, transparent)}
.table tbody tr:nth-child(2n){ background: color-mix(in srgb, var(--panel-2) 18%, transparent); }

.text-muted{color:var(--muted)}
.badge{display:inline-block;padding:.2rem .5rem;border-radius:.5rem;background:var(--panel-2);border:1px solid var(--border);color:var(--muted);font-weight:700}
.actions{display:flex;gap:.4rem;justify-content:flex-end;white-space:nowrap;direction:ltr}
.btn-sm{padding:.4rem .6rem;border-radius:.55rem;font-weight:700;border:1px solid var(--border);background:var(--panel-2);color:var(--text)}
.btn-sm:hover{filter:brightness(1.05)}
.btn-outline-primary{border-color:var(--primary);color:var(--primary)}
.btn-outline-primary:hover{ background: color-mix(in srgb, var(--primary) 12%, transparent) }
.btn-outline-danger{border-color:var(--bad);color:var(--bad)}
.btn-outline-danger:hover{ background: color-mix(in srgb, var(--bad) 12%, transparent) }
.actions .btn-sm i{ width: 1.1rem; text-align: center }

/* ====================== Pills ====================== */
.pill{font-weight:800;padding:.28rem .6rem;border-radius:999px;font-size:.75rem;border:1px solid transparent;display:inline-flex;align-items:center;gap:.35rem;box-shadow: 0 0 0 1px color-mix(in srgb, currentColor 25%, transparent) inset}
.pill-processed{color:var(--good);background:var(--good-bg);border-color:color-mix(in srgb, var(--good) 28%, transparent)}
.pill-processing{color:var(--warn);background:var(--warn-bg);border-color:color-mix(in srgb, var(--warn) 28%, transparent)}
.pill-failed{color:var(--bad);background:var(--bad-bg);border-color:color-mix(in srgb, var(--bad) 28%, transparent)}
.pill-pending{color:var(--primary);background:color-mix(in srgb, var(--primary) 8%, var(--panel));border-color:color-mix(in srgb, var(--primary) 28%, transparent)}

/* ====================== Cards View ====================== */
.cards-grid{ display:grid; gap:18px; grid-template-columns: repeat( auto-fill, minmax(320px,1fr) ); }
.card-sec{
  background:var(--panel);
  border:1px solid var(--border);
  border-radius:18px; overflow:hidden;
  box-shadow:0 10px 24px rgba(0,0,0,.2);
}
.card-sec .head{
  background:var(--panel-2);
  padding:12px 14px; display:flex; align-items:center; justify-content:space-between;
  border-bottom:1px solid var(--border)
}
.card-sec .head .t{ font-weight:900; color:var(--text) }
.badge-soft{ padding:.25rem .5rem; border-radius:999px; border:1px solid var(--border); color:var(--muted); background:var(--panel) }
.list-doc{ padding:10px 12px; display:flex; flex-direction:column; gap:10px; max-height: 420px; overflow:auto }
.doc-row{
  display:flex; gap:10px; align-items:center; justify-content:space-between;
  background: color-mix(in srgb, var(--panel-2) 22%, transparent);
  border:1px solid var(--border); border-radius:12px; padding:10px 12px;
}
.doc-title{ color:var(--text); font-weight:800; max-width:58%; white-space:nowrap; overflow:hidden; text-overflow:ellipsis }
.doc-meta{ display:flex; gap:8px; align-items:center; color:var(--muted); font-weight:700; flex-wrap: wrap }
.doc-actions{ display:flex; gap:6px }
.btn-xs{ padding:.3rem .5rem; border-radius:.5rem; font-weight:800; border:1px solid var(--border); background:var(--panel-2); color:var(--text) }
.btn-xs:hover{ filter:brightness(1.05) }

.lang-badge{
  background: color-mix(in srgb, var(--primary) 10%, var(--panel));
  color: var(--text); border:1px solid color-mix(in srgb, var(--primary) 25%, var(--border));
}
.numeric{ font-variant-numeric: tabular-nums; }
.auto-dir{ unicode-bidi: plaintext }
@media (max-width: 992px){
  .toolbar{flex-wrap:wrap}
  .actions{justify-content:flex-start}
}
@media (prefers-reduced-motion: reduce){
  .fa-spin{ animation: none !important }
}
</style>

<div class="admin-dashboard container py-4">

  {{-- Top bar --}}
  <div class="d-flex align-items-center justify-content-between mb-4 toolbar">
    <div class="d-flex align-items-center gap-3">
      <h4 class="mb-0 fw-bold" style="font-size:1.45rem; color:var(--text)">{{ __('dashboard.title') }}</h4>
      <span class="chip"> {{ __('dashboard.docs') }}: <span class="numeric">{{ $totalDocuments ?? 0 }}</span></span>
      <span class="chip"> {{ __('dashboard.users') }}: <span class="numeric">{{ $totalUsers ?? 0 }}</span></span>
    </div>

    <div class="d-flex align-items-center" style="gap:.5rem">
      {{-- Switch view --}}
      @php $isCards = (request('view','cards') === 'cards'); @endphp
      <a href="{{ route('admin.dashboard', array_merge(request()->except('view'), ['view'=>'cards'])) }}" class="btn btn-soft">
        <i class="fas fa-grip me-2"></i>{{ app()->getLocale()==='ar' ? 'عرض كروت' : 'Cards' }}
      </a>
      <a href="{{ route('admin.dashboard', array_merge(request()->except('view'), ['view'=>'table'])) }}" class="btn btn-soft">
        <i class="fas fa-table me-2"></i>{{ app()->getLocale()==='ar' ? 'عرض جدول' : 'Table' }}
      </a>

      <a href="{{ route('admin.documents.upload') }}" class="btn btn-glow">
        <i class="fas fa-plus me-2"></i>{{ __('dashboard.btn_upload') }}
      </a>
    </div>
  </div>

  {{-- فلاتر أعلى الصفحة --}}
  <form method="GET" class="d-flex flex-wrap align-items-center gap-2 mb-3" action="{{ route('admin.dashboard') }}">
    {{-- احتفظ بالـ view الحالي --}}
    <input type="hidden" name="view" value="{{ request('view','cards') }}">
    <select class="btn-soft" name="stream" style="min-width:170px">
      <option value="">{{ app()->getLocale()==='ar' ? 'كل الفروع' : 'All streams' }}</option>
      @foreach($streams as $s)
        <option value="{{ $s->slug }}" @selected(($streamSlug??null)==$s->slug)>{{ app()->getLocale()==='ar' ? $s->name_ar : $s->name_en }}</option>
      @endforeach
    </select>

    <select class="btn-soft" name="year" style="min-width:140px">
      <option value="">{{ app()->getLocale()==='ar' ? 'كل السنوات' : 'All years' }}</option>
      @foreach(($availableYears ?? []) as $y)
        <option value="{{ $y }}" @selected(($yearNumber??null)==$y)>{{ $y }}</option>
      @endforeach
    </select>

    <select class="btn-soft" name="subject" style="min-width:180px">
      <option value="">{{ app()->getLocale()==='ar' ? 'كل المواد' : 'All subjects' }}</option>
      @foreach(($availableSubjects ?? []) as $sub)
        <option value="{{ $sub->id }}" @selected(($subjectId??null)==$sub->id)>{{ app()->getLocale()==='ar' ? $sub->name_ar : $sub->name_en }}</option>
      @endforeach
    </select>

    {{-- اختياري: تصنيفات لو متوفرة --}}
    @if(!empty($availableCategories))
      <select class="btn-soft" name="category" style="min-width:200px">
        <option value="">{{ app()->getLocale()==='ar' ? 'كل التصنيفات' : 'All categories' }}</option>
        @foreach($availableCategories as $cat)
          <option value="{{ $cat->id }}" @selected(($categoryId??null)==$cat->id)>{{ app()->getLocale()==='ar' ? $cat->name_ar : $cat->name_en }}</option>
        @endforeach
      </select>
    @endif

    <button class="btn btn-soft" type="submit"><i class="fa fa-filter me-1"></i>{{ app()->getLocale()==='ar' ? 'تصفية' : 'Filter' }}</button>

    @if(request()->hasAny(['stream','year','subject','category']))
      <a class="btn btn-soft" href="{{ route('admin.dashboard', ['view'=>request('view','cards')]) }}"><i class="fa fa-rotate-left me-1"></i>{{ app()->getLocale()==='ar' ? 'إعادة ضبط' : 'Reset' }}</a>
    @endif
  </form>

  {{-- ======== عرض الكروت ======== --}}
  @if($isCards)
    @php
      // نبني المجموعات هنا في الواجهة: فرع -> سنة
      $grouped = collect($documents ?? [])->groupBy(function($d){
        return optional($d->stream)->slug ?? '—';
      })->map(function($byStream){
        return $byStream->groupBy(function($d){
          return optional($d->year)->year ?? '—';
        })->sortKeysDesc();
      });
    @endphp

    <div class="cards-grid mb-4">
      @forelse($grouped as $slug => $byYear)
        @foreach($byYear as $yearNum => $rows)
          @php
            $first = $rows->first();
            $streamLabel = app()->getLocale()==='ar'
              ? (optional($first->stream)->name_ar ?? $slug)
              : (optional($first->stream)->name_en ?? $slug);
            $count = $rows->count();
          @endphp

          <div class="card-sec">
            <div class="head">
              <div class="t"><i class="fas fa-layer-group me-2"></i>{{ $streamLabel }} — {{ $yearNum }}</div>
              <span class="badge-soft">{{ $count }} {{ app()->getLocale()==='ar' ? 'ملف' : 'docs' }}</span>
            </div>

            <div class="list-doc">
              @foreach($rows as $doc)
                @php
                  $statusKey = strtolower($doc->status ?? 'pending');
                  $pill = match($statusKey){
                    'processed'  => 'pill-processed',
                    'processing' => 'pill-processing',
                    'failed'     => 'pill-failed',
                    default      => 'pill-pending',
                  };
                @endphp
                <div class="doc-row">
                  <div class="doc-title auto-dir">
                    <i class="fas fa-file-lines me-2" style="color:var(--muted)"></i>
                    {{ $doc->title }}
                  </div>

                  <div class="doc-meta">
                    <span class="badge lang-badge">{{ strtoupper($doc->language ?? '-') }}</span>
                    @if($doc->subject)
                      <span class="badge"><i class="fa fa-book me-1"></i>{{ app()->getLocale()==='ar' ? $doc->subject->name_ar : $doc->subject->name_en }}</span>
                    @endif
                    <span class="pill {{ $pill }}">
                      @if($statusKey === 'processed') <i class="fas fa-check me-1"></i>
                      @elseif($statusKey === 'processing') <i class="fas fa-spinner fa-spin me-1"></i>
                      @elseif($statusKey === 'failed') <i class="fas fa-xmark me-1"></i>
                      @else <i class="fas fa-clock me-1"></i>
                      @endif
                      {{ __('dashboard.status.'.$statusKey) }}
                    </span>
                  </div>

                  <div class="doc-actions">
                    <a class="btn-xs" href="{{ route('documents.view', $doc->id) }}" target="_blank"><i class="fa fa-eye"></i></a>
                    <a class="btn-xs" href="{{ route('admin.documents.edit', $doc->id) }}"><i class="fa fa-pen-to-square"></i></a>
                    <form method="POST" action="{{ route('admin.documents.destroy', $doc->id) }}" class="d-inline delete-form">
                      @csrf @method('DELETE')
                      <button type="submit" class="btn-xs btn-delete-nice"><i class="fa fa-trash"></i></button>
                    </form>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        @endforeach
      @empty
        <div class="card-wrap">
          <div class="p-4 text-center text-muted">{{ __('dashboard.empty') }}</div>
        </div>
      @endforelse
    </div>
  @endif

  {{-- ======== عرض الجدول القديم (لللي بحبّه) ======== --}}
  @if(!$isCards)
  <div class="card-wrap">
    <div class="card-header">
      <i class="fas fa-cogs"></i>
      <span>{{ __('dashboard.documents_mgmt') }}</span>
    </div>

    <div class="p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" role="table" aria-label="{{ __('dashboard.documents_mgmt') }}">
          <thead class="table-dark">
          <tr>
            <th style="width:28%"><i class="fas fa-heading me-2"></i>{{ __('dashboard.col_title') }}</th>
            <th style="width:12%"><i class="fas fa-info-circle me-2"></i>{{ __('dashboard.col_status') }}</th>
            <th style="width:10%"><i class="fas fa-language me-2"></i>{{ __('dashboard.col_lang') }}</th>
            <th style="width:22%"><i class="fas fa-tags me-2"></i>{{ app()->getLocale()==='ar' ? 'التصنيفات' : 'Categories' }}</th>
            <th style="width:12%"><i class="fas fa-calendar me-2"></i>{{ __('dashboard.col_date') }}</th>
            <th class="text-end" style="width:16%"><i class="fas fa-tools me-2"></i>{{ __('dashboard.col_actions') }}</th>
          </tr>
          </thead>
          <tbody>
          @forelse($documents ?? [] as $doc)
            @php
              $statusKey = strtolower($doc->status ?? 'pending');
              $pill = match($statusKey){
                'processed'  => 'pill-processed',
                'processing' => 'pill-processing',
                'failed'     => 'pill-failed',
                default      => 'pill-pending',
              };
            @endphp
            <tr>
              <td>
                <div class="fw-semibold auto-dir" style="color:var(--text); max-width:420px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                  <i class="fas fa-file-lines me-2" style="color:var(--muted)"></i>{{ $doc->title }}
                </div>
                <div class="small file-name" style="color:var(--muted)" title="{{ $doc->file_name }}">
                  {{ \Illuminate\Support\Str::limit($doc->file_name ?? '', 64) }}
                </div>
              </td>

              <td>
                <span class="pill {{ $pill }}">
                  @if($statusKey === 'processed') <i class="fas fa-check me-1"></i>
                  @elseif($statusKey === 'processing') <i class="fas fa-spinner fa-spin me-1"></i>
                  @elseif($statusKey === 'failed') <i class="fas fa-xmark me-1"></i>
                  @else <i class="fas fa-clock me-1"></i>
                  @endif
                  {{ __('dashboard.status.'.$statusKey) }}
                </span>
              </td>

              <td><span class="badge bg-secondary lang-badge">{{ strtoupper($doc->language ?? '-') }}</span></td>

              <td class="auto-dir">
                @if(method_exists($doc,'categories') && $doc->relationLoaded('categories') || true)
                  @forelse(($doc->categories ?? collect()) as $cat)
                    <span class="badge" style="margin:2px 3px; display:inline-block">
                      {{ app()->getLocale()==='ar' ? ($cat->name_ar ?? $cat->name ?? '') : ($cat->name_en ?? $cat->name ?? '') }}
                    </span>
                  @empty
                    <span class="text-muted">{{ app()->getLocale()==='ar' ? 'بدون' : 'None' }}</span>
                  @endforelse
                @else
                  <span class="text-muted">{{ app()->getLocale()==='ar' ? 'بدون' : 'None' }}</span>
                @endif
              </td>

              <td class="numeric" dir="ltr">{{ optional($doc->created_at)->format('Y-m-d') }}</td>

              <td class="text-end">
                <div class="actions">
                  <a class="btn btn-sm btn-outline-secondary" href="{{ route('documents.view', $doc->id) }}" target="_blank" aria-label="{{ __('dashboard.view') }}">
                    <i class="fas fa-eye"></i> {{ __('dashboard.view') }}
                  </a>
                  <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.documents.edit', $doc->id) }}" aria-label="{{ __('dashboard.edit') }}">
                    <i class="fas fa-pen-to-square"></i> {{ __('dashboard.edit') }}
                  </a>
                  <form method="POST" action="{{ route('admin.documents.destroy', $doc->id) }}" class="d-inline delete-form">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger btn-delete-nice" aria-label="{{ __('dashboard.delete') }}">
                      <i class="fas fa-trash"></i> {{ __('dashboard.delete') }}
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center" style="color:var(--muted); padding:3rem 0">
                <i class="fas fa-inbox fa-3x mb-3 d-block" style="color:var(--border)"></i>
                <div>{{ __('dashboard.empty') }}</div>
                <div class="small mt-2">{{ __('dashboard.empty_hint') }}</div>
              </td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif

  {{-- Footer actions --}}
  @php $arrow = app()->getLocale()==='ar' ? 'fa-arrow-right' : 'fa-arrow-left'; @endphp
  <div class="footer-actions mt-3">
    <button type="button" class="btn btn-soft"
            title="{{ __('dashboard.back') }}"
            onclick="if (document.referrer && document.referrer !== location.href) { history.back(); } else { window.location='{{ route('admin.dashboard') }}'; }">
      <i class="fas {{ $arrow }} me-2"></i>{{ __('dashboard.back') }}
    </button>
  </div>
</div>

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
@endpush

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function(){
  const isRTL = @json(app()->getLocale()==='ar');
  const isDark = () => (document.documentElement.getAttribute('data-theme') || '').toLowerCase() === 'night';

  const swalSkin = () => ({
    popup: (isDark() ? 'swal2-skin-dark' : 'swal2-skin-light') + (isRTL ? ' rtl' : '')
  });

  const circleWarnSvg =
    `<svg width="84" height="84" viewBox="0 0 84 84" fill="none" xmlns="http://www.w3.org/2000/svg">
      <circle cx="42" cy="42" r="32" stroke="#F59E0B" stroke-width="6" fill="none"/>
      <line x1="42" y1="26" x2="42" y2="48" stroke="#F59E0B" stroke-width="6" stroke-linecap="round"/>
      <circle cx="42" cy="57" r="4" fill="#F59E0B"/>
    </svg>`;

  // تأكيد قبل الحذف
  document.addEventListener('click', (e)=>{
    const btn = e.target.closest('.btn-delete-nice');
    if(!btn) return;
    e.preventDefault();
    const form = btn.closest('form');

    Swal.fire({
      title: @json(__('dashboard.confirm_delete_title')),
      html:  @json(__('dashboard.confirm_delete_text')),
      icon: undefined,
      iconHtml: circleWarnSvg,
      showCancelButton: true,
      confirmButtonText: @json(__('dashboard.confirm_yes')),
      cancelButtonText:  @json(__('dashboard.confirm_cancel')),
      reverseButtons: true,
      focusCancel: true,
      allowEscapeKey: true,
      allowEnterKey: true,
      customClass: Object.assign({}, swalSkin(), {
        confirmButton: 'swal2-confirm-custom',
        cancelButton:  'swal2-cancel-custom'
      })
    }).then(r => { if (r.isConfirmed) form.submit(); });
  });

  // Toasts
  const toast = (type, msg) => {
    Swal.fire({
      toast:true,
      position: isRTL ? 'top-start' : 'top-end',
      icon: type,
      title: msg,
      showConfirmButton:false,
      timer:2600,
      timerProgressBar:true,
      customClass: swalSkin()
    });
  };

  @if(session('success')) toast('success', @json(session('success'))); @endif
  @if(session('error'))   toast('error',   @json(session('error')));   @endif
})();
</script>
@endsection

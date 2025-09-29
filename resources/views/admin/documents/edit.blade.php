@extends('layouts.app')

@php
  $rtl    = app()->getLocale()==='ar';
  $arrow  = $rtl ? '→' : '←';
  $qCount = $document->questions->count();
@endphp

@section('content')
<style>
  /* ===================== Design Tokens ===================== */
  :root{
    /* Dark base */
    --bg:#0f1720;
    --panel:#111827;
    --panel-2:#0b1220;
    --border:#1f2937;
    --text:#e5e7eb;
    --muted:#9aa4b2;
    /* Brand */
    --accent:#6757ff;
    --accent-600:#5949f6;
    /* Feedback */
    --success:#16a34a;
    --danger:#e11d48;
    --warning:#f59e0b;
    /* Shadow & Radius */
    --shadow:0 10px 28px rgba(0,0,0,.28);
    --r-lg:16px; --r-md:12px;
  }
  html[data-theme="light"],
  @media (prefers-color-scheme: light) {
    :root{
      --bg:#f6f7fb;
      --panel:#ffffff;
      --panel-2:#f9fafb;
      --border:#e5e7eb;
      --text:#0f172a;
      --muted:#475569;
      --accent:#5b50ff;
      --accent-600:#4b40ff;
      --success:#16a34a;
      --danger:#dc2626;
      --shadow:0 16px 36px rgba(2,6,23,.08);
    }
  }

  /* ===================== Invert-Colors Mode ===================== */
  /* نفّذ العكس على كامل الصفحة */
  html.invert-colors{
    filter: invert(1) hue-rotate(180deg);
  }
  /* أعد الصور/الفيديو/الـSVG لوضعها الطبيعي داخل الوضع المعكوس */
  html.invert-colors img,
  html.invert-colors video,
  html.invert-colors canvas,
  html.invert-colors svg,
  html.invert-colors picture{
    filter: invert(1) hue-rotate(180deg);
  }
  /* أي عنصر ما بدك ينعكس، أعطه class="no-invert" */
  html.invert-colors .no-invert{
    filter: invert(1) hue-rotate(180deg) !important;
  }

  /* ===================== Page Layout ===================== */
  body{background:var(--bg)}
  .q-edit{max-width:1100px;margin-inline:auto;padding:20px;color:var(--text)}
  .q-edit *{box-sizing:border-box}

  .toolbar{display:flex;align-items:center;justify-content:space-between;gap:.75rem;margin-bottom:14px}
  .toolbar .left{display:flex;gap:.75rem;align-items:center}
  .toolbar .right{display:flex;gap:.5rem;align-items:center}

  .chip{
    display:inline-flex;gap:.5rem;align-items:center;
    padding:.45rem .85rem;border-radius:999px;
    color:var(--muted);background:var(--panel-2);border:1px solid var(--border);
    font-weight:700;font-size:.8rem
  }
  .btn-soft{
    border:1px solid var(--border);background:var(--panel-2);color:var(--text);
    padding:.55rem .9rem;border-radius:.7rem;font-weight:700;transition:.15s;
  }
  .btn-soft:hover{filter:brightness(1.04)}
  .btn-accent{
    background:linear-gradient(135deg,var(--accent) 0%, var(--accent-600) 60%);
    color:#fff;border:0;border-radius:.7rem;padding:.6rem 1rem;font-weight:800;transition:.1s;
    box-shadow:0 6px 18px color-mix(in srgb,var(--accent) 24%, transparent);
  }
  .btn-accent:hover{filter:brightness(1.03)}
  .btn-danger{
    background:color-mix(in srgb,var(--danger) 92%, white 8%);color:#fff;border:0;border-radius:.55rem;
    padding:.45rem .7rem;font-weight:800
  }

  /* ===================== Hero (No-overlap) ===================== */
  .hero{
    display:flex;flex-direction:column;align-items:center;justify-content:center;
    gap:8px;text-align:center;padding:22px 18px;margin-bottom:14px;
    background:linear-gradient(180deg,var(--panel-2) 0%, var(--panel) 100%);
    border:1px solid var(--border);border-radius:var(--r-lg);box-shadow:var(--shadow)
  }
  .hero, .hero * {position:static !important;}
  .hero-title{
    display:block;margin:0;font-size:26px;font-weight:800;line-height:1.35;
    white-space:normal;word-break:break-word;color:var(--text)
  }
  .hero-subtitle{
    display:block;margin:0;font-size:14px;color:var(--muted);line-height:1.65;
    white-space:normal;word-break:break-word
  }

  /* ===================== Cards & Forms ===================== */
  .glass{background:var(--panel);border:1px solid var(--border);border-radius:var(--r-lg);box-shadow:var(--shadow)}
  .card-header{
    background:var(--panel-2);border-bottom:1px solid var(--border);color:var(--text);
    padding:13px 16px;border-radius:var(--r-lg) var(--r-lg) 0 0;display:flex;align-items:center;justify-content:space-between
  }
  .form-label{display:block;font-weight:800;margin-bottom:6px}
  .form-control{
    background:var(--panel-2);color:var(--text);border:1px solid var(--border);
    border-radius:12px;padding:10px 12px;font-size:14px;transition:.15s
  }
  .form-control::placeholder{color:color-mix(in srgb,var(--muted) 82%, transparent)}
  .form-control:focus{
    outline:none;border-color:var(--accent);
    box-shadow:0 0 0 3px color-mix(in srgb,var(--accent) 24%, transparent)
  }
  .dir-auto{direction:auto;text-align:start}

  .opts-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:10px}

  details.q{background:var(--panel-2);border:1px solid var(--border);border-radius:12px;margin-bottom:10px;transition:.2s}
  details.q[open]{border-color:var(--accent)}
  details.q summary{
    list-style:none;cursor:pointer;padding:14px 16px;display:flex;gap:12px;align-items:center;justify-content:space-between
  }
  details.q summary::-webkit-details-marker{display:none}
  .q-title{font-weight:800;flex:1;font-size:14px;color:var(--text)}
  .handle{
    cursor:grab;user-select:none;font-weight:900;color:var(--muted);font-size:18px;padding:6px 8px;
    border-radius:10px;background:var(--panel);border:1px solid var(--border)
  }
  .question-content{padding:16px;border-top:1px solid var(--border)}
  .tip{
    background:var(--panel);color:var(--muted);padding:8px 10px;border-radius:10px;border:1px dashed var(--border);
    margin-top:6px;font-size:12.5px
  }

  /* Alerts */
  .alert{border-radius:12px;border:1px solid var(--border);padding:12px 14px;margin-bottom:16px;font-weight:800}
  .alert-success{background:color-mix(in srgb,var(--success) 14%, transparent);color:#bff9cf}
  .alert-danger{ background:color-mix(in srgb,var(--danger) 12%, transparent);color:#ffd0d7}
  .alert-warning{background:color-mix(in srgb,var(--warning) 14%, transparent);color:#fde68a}

  @media (max-width:768px){
    .opts-grid{grid-template-columns:1fr}
  }
</style>

<div class="q-edit" dir="{{ $rtl ? 'rtl' : 'ltr' }}">
  {{-- Toolbar --}}
  <div class="toolbar">
    <div class="left">
      <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('admin.dashboard') }}"
         class="btn-soft" title="{{ __('questions.back') }}">
        {{ $rtl ? 'رجوع '.$arrow : $arrow.' '.__('questions.back') }}
      </a>
      <span class="chip">📊 {{ $qCount }} {{ __('questions.stats_questions') }}</span>
    </div>

    <div class="right">
      {{-- زر عكس الألوان --}}
      <button type="button" id="invertToggle" class="btn-soft" title="{{ $rtl ? 'عكس الألوان' : 'Invert colors' }}">
        🌓 {{ $rtl ? 'عكس الألوان' : 'Invert Colors' }}
      </button>
    </div>
  </div>

  {{-- Hero (العنوان + الوصف بدون أي تراكب) --}}
  <div class="hero">
    <span class="hero-title">{{ __('questions.title') }}</span>
    <span class="hero-subtitle">{{ __('questions.subtitle') }}</span>
  </div>

  {{-- Flash --}}
  @if(session('success'))
    <div class="alert alert-success">✅ <strong>{{ __('questions.success') }}</strong> {{ session('success') }}</div>
  @endif
  @if(session('warning'))
    <div class="alert alert-warning">⚠️ <strong>{{ __('questions.warning') }}</strong> {{ session('warning') }}</div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger">
      ❌ <strong>{{ __('questions.fix_errors') }}</strong>
      <ul class="mb-0 mt-2" @style('padding-inline-start:1rem')}>
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
      </ul>
    </div>
  @endif

  <div class="row g-4">
    {{-- Add Question --}}
    <div class="col-lg-5">
      <div class="glass h-100">
        <div class="card-header">
          <h5 class="mb-0">{{ __('questions.add_new') }}</h5>
          <small style="opacity:.8">{{ __('questions.manual') }}</small>
        </div>

        <div class="p-4">
          <form method="POST" action="{{ route('admin.documents.storeQuestion',$document) }}">
            @csrf

            <div class="mb-3">
              <label class="form-label">{{ __('questions.q_text') }}</label>
              <textarea class="form-control dir-auto" name="question" rows="4" required
                        placeholder="{{ $rtl ? 'اكتب السؤال هنا...' : 'Type your question here...' }}"></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label">{{ __('questions.options_opt') }}</label>
              <div class="opts-grid">
                @for($i=0;$i<4;$i++)
                  <input class="form-control dir-auto" name="options[]"
                         placeholder="{{ $rtl ? 'خيار ' : 'Option ' }}{{ chr(65+$i) }}">
                @endfor
              </div>
            </div>

            <div class="mb-4">
              <label class="form-label">{{ __('questions.correct_opt') }}</label>
              <input class="form-control dir-auto" name="correct_answer"
                     placeholder="{{ $rtl ? 'يجب أن تطابق أحد الخيارات' : 'Must match one of the options' }}">
              <div class="tip">{{ __('questions.correct_tip') }}</div>
            </div>

            <div class="d-flex justify-content-end">
              <button type="submit" class="btn-accent">{{ __('questions.add_btn') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    {{-- Current Questions --}}
    <div class="col-lg-7">
      <div class="glass">
        <div class="card-header">
          <h5 class="mb-0">{{ __('questions.current') }}</h5>
          <small style="opacity:.8">{{ __('questions.reorder_hint') }}</small>
        </div>

        <div class="p-4">
          @if($qCount > 0)
            <div id="q-list" class="d-flex flex-column gap-3">
              @foreach($document->questions->sortBy('sort')->values() as $q)
                <details class="q" data-id="{{ $q->id }}">
                  <summary>
                    <span class="handle" title="{{ __('questions.drag_handle') }}">≡</span>
                    <span class="q-title dir-auto"><strong>{{ $loop->iteration }}.</strong> {{ Str::limit($q->question, 100) }}</span>
                    <button type="button" class="btn-danger"
                            onclick="if(confirm('{{ __('questions.delete_confirm') }}')) document.getElementById('del-{{ $q->id }}').submit();">
                      🗑️ {{ __('questions.delete') }}
                    </button>
                  </summary>

                  <div class="question-content">
                    <form method="POST" action="{{ route('admin.documents.updateQuestion',[$document,$q]) }}">
                      @csrf
                      <div class="mb-3">
                        <label class="form-label">{{ __('questions.q_text') }}</label>
                        <textarea class="form-control dir-auto" name="question" rows="3" required>{{ $q->question }}</textarea>
                      </div>

                      <div class="mb-3">
                        <label class="form-label">{{ __('questions.options') }}</label>
                        @php $opts = $q->options ?? []; @endphp
                        <div class="opts-grid">
                          @for($i=0;$i<4;$i++)
                            <input class="form-control dir-auto" name="options[]" value="{{ $opts[$i] ?? '' }}"
                                   placeholder="{{ $rtl ? 'خيار ' : 'Option ' }}{{ chr(65+$i) }}">
                          @endfor
                        </div>
                      </div>

                      <div class="mb-4">
                        <label class="form-label">{{ __('questions.correct') }}</label>
                        <input class="form-control dir-auto" name="correct_answer" value="{{ $q->correct_answer }}">
                        <div class="tip">{{ __('questions.correct_tip') }}</div>
                      </div>

                      <div class="d-flex justify-content-end">
                        <button type="submit" class="btn-accent">{{ __('questions.save') }}</button>
                      </div>
                    </form>

                    <form id="del-{{ $q->id }}" method="POST"
                          action="{{ route('admin.documents.destroyQuestion',[$document,$q]) }}">
                      @csrf @method('DELETE')
                    </form>
                  </div>
                </details>
              @endforeach
            </div>
          @else
            <div class="text-center py-5" style="color:var(--muted)">
              <div style="font-size:48px;margin-bottom:16px">📝</div>
              <h4>{{ __('questions.empty_title') }}</h4>
              <p>{{ __('questions.empty_text') }}</p>
            </div>
          @endif

          <div class="mt-4 pt-3" style="border-top:1px solid var(--border)">
            <a href="{{ route('admin.dashboard') }}" class="btn-soft">
              {{ $rtl ? __('questions.back_to_panel') : __('questions.back_to_dash') }}
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ===== JS: autodir + drag/sort + invert-colors ===== --}}
<script>
(() => {
  // ===== Invert Colors toggle (with persistence) =====
  const KEY = 'invertColors';
  const html = document.documentElement;
  const btn  = document.getElementById('invertToggle');

  const applyInvert = (on) => {
    if(on){ html.classList.add('invert-colors'); }
    else  { html.classList.remove('invert-colors'); }
    localStorage.setItem(KEY, on ? '1' : '0');
  };

  // load saved state
  try {
    const saved = localStorage.getItem(KEY);
    if(saved === '1') applyInvert(true);
  } catch(e){ /* ignore */ }

  btn?.addEventListener('click', () => {
    const on = !html.classList.contains('invert-colors');
    applyInvert(on);
  });

  // ===== autodir =====
  const detectDir = (t='')=>{
    t=(t||'').trim();
    for(const ch of t){
      const c=ch.codePointAt(0); if(!c) continue;
      if((c>=0x0600 && c<=0x06FF)||(c>=0x0750 && c<=0x077F)||(c>=0x08A0 && c<=0x08FF)||(c>=0xFB50 && c<=0xFDFF)||(c>=0xFE70 && c<=0xFEFF)) return 'rtl';
      if((c>=0x0041 && c<=0x007A)||(c>=0x0030 && c<=0x0039)) return 'ltr';
    } return '';
  };
  const applyDir = el=>{
    const v=el.value||el.textContent||'';
    const d=detectDir(v)||'auto';
    el.setAttribute('dir',d);
    el.style.textAlign = d==='ltr'?'left':d==='rtl'?'right':'start';
  };
  document.querySelectorAll('.dir-auto, .q-title').forEach(el=>{
    applyDir(el); el.addEventListener('input',()=>applyDir(el)); el.addEventListener('change',()=>applyDir(el));
  });

  // ===== drag sort =====
  const list=document.getElementById('q-list'); if(!list) return;
  let dragging=null;

  list.querySelectorAll('details.q').forEach(card=>{
    const handle=card.querySelector('.handle');
    handle.addEventListener('mousedown', ()=>{
      dragging=card; card.style.opacity='.72'; card.style.transform='rotate(1deg)';
      document.body.style.userSelect='none'; handle.style.cursor='grabbing';
    });
  });

  document.addEventListener('mouseup', ()=>{
    if(!dragging) return;
    dragging.style.opacity='1'; dragging.style.transform=''; document.body.style.userSelect='';
    const h=dragging.querySelector('.handle'); if(h) h.style.cursor='grab';
    sendOrder(); dragging=null;
  });

  document.addEventListener('mousemove', e=>{
    if(!dragging) return;
    const after=getAfter(list,e.clientY);
    if(after==null) list.appendChild(dragging); else list.insertBefore(dragging,after);
  });

  function getAfter(container,y){
    const els=[...container.querySelectorAll('details.q:not([style*="opacity: .72"])')];
    return els.reduce((closest,el)=>{
      const box=el.getBoundingClientRect(); const offset=y-box.top-box.height/2;
      if(offset<0 && offset>closest.offset) return {offset,element:el};
      return closest;
    },{offset:Number.NEGATIVE_INFINITY}).element;
  }

  function sendOrder(){
    const order={};
    [...list.querySelectorAll('details.q')].forEach((el,i)=>order[el.dataset.id]=i);
    fetch(@json(route('admin.documents.reorder', $document)),{
      method:'POST', headers:{'X-CSRF-TOKEN':@json(csrf_token()),'Content-Type':'application/json'},
      body:JSON.stringify({order})
    }).catch(()=>{});
  }
})();
</script>
@endsection

@extends('layouts.app')

@section('content')
@php
  $rtl = app()->getLocale()==='ar';
  $preset = [
    'stream'   => request('stream'),    // scientific | literary (slug)
    'year'     => request('year'),      // e.g. 2023
    'semester' => request('semester'),  // first | second
  ];
  // نجلب أسماء الفرع لو موجود preset للعرض على شكل badge
  $presetStream = $preset['stream']
      ? \App\Models\Stream::select('slug','name_ar','name_en')->where('slug',$preset['stream'])->first()
      : null;

  // القوائم الأساسية عندما لا يوجد preset
  $streams = \App\Models\Stream::select('slug','name_ar','name_en')->orderBy('id')->get();
  $years   = \App\Models\Year::orderBy('year','desc')->pluck('year');
@endphp

<style>
:root{
  --bg:#0b1220;--panel:#0f172a;--panel-2:#111827;--border:#1f2937;
  --text:#e5e7eb;--muted:#94a3b8;--primary:#6366f1;--primary-2:#4f46e5;
}
.page{direction:{{ $rtl?'rtl':'ltr' }}}
.page::before{content:"";position:fixed;inset:0;background:var(--bg);z-index:-1}
.wrap{max-width:980px;margin-inline:auto;background:var(--panel);border:1px solid var(--border);border-radius:1rem;padding:1.25rem}
.header{display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem}
.title{color:var(--text);font-weight:800;margin:0}
.btn{padding:.55rem .9rem;border-radius:.75rem;border:1px solid var(--border);background:var(--panel-2);color:var(--text);font-weight:700}
.btn:hover{filter:brightness(1.05)}
.btn-primary{background:var(--primary);border:0;color:#fff}
.btn-primary:hover{background:var(--primary-2)}

.grid{display:grid;gap:1rem;grid-template-columns:repeat(12,1fr);margin-top:.75rem}
.col-12{grid-column:span 12}
.col-6{grid-column:span 12}
@media(min-width:780px){ .col-6{grid-column:span 6} }

.label{display:block;color:var(--muted);font-weight:800;margin-bottom:.35rem}
.input,.select,.textarea{
  width:100%;padding:.7rem .8rem;border-radius:.7rem;background:var(--panel-2);
  border:1px solid var(--border);color:var(--text);font-weight:600
}
.badges{display:flex;gap:.5rem;flex-wrap:wrap;margin:.25rem 0 .75rem}
.badge{background:#111827;border:1px solid var(--border);color:var(--text);border-radius:999px;padding:.25rem .65rem;font-weight:700}
.help{color:var(--muted);font-size:.9rem;margin-top:.25rem}

.drop{border:2px dashed var(--border);border-radius:1rem;display:flex;align-items:center;justify-content:center;
  padding:1.1rem;background:var(--panel-2);color:var(--muted);text-align:center}
.drop.dragover{border-color:var(--primary);color:var(--text)}
.actions{display:flex;justify-content:flex-end;margin-top:.75rem}
</style>

<div class="container py-4 page">
  <div class="wrap">
    <div class="header">
      <h4 class="title">
        <i class="fa-solid fa-cloud-arrow-up"></i>
        {{ $rtl ? 'رفع مستند (PDF / TXT)' : 'Upload Document (PDF / TXT)' }}
      </h4>
      <a href="{{ url()->previous() }}" class="btn">
        <i class="fa-solid fa-arrow-{{ $rtl?'right':'left' }}"></i>
        {{ $rtl?'رجوع':'Back' }}
      </a>
    </div>

    <form method="POST" action="{{ route('admin.documents.store') }}" enctype="multipart/form-data" id="uploadForm">
      @csrf

      <div class="grid">

        {{-- اللغة + العنوان --}}
        <div class="col-6">
          <label class="label">{{ $rtl?'اللغة':'Language' }}</label>
          <select name="language" class="select" required>
            <option value="arabic">{{ $rtl?'العربية':'Arabic' }}</option>
            <option value="english">{{ $rtl?'الإنجليزية':'English' }}</option>
          </select>
        </div>
        <div class="col-6">
          <label class="label">{{ $rtl?'العنوان':'Title' }}</label>
          <input name="title" class="input" placeholder="{{ $rtl?'اكتب عنوان المستند':'Document title' }}" required>
        </div>

        {{-- =============== الفرع / السنة / الفصل =============== --}}
        @if($preset['stream'] || $preset['year'] || $preset['semester'])
          {{-- عند الدخول من شاشة السنة/الفصل: نخفي الحقول ونثبت القيم --}}
          @if($preset['stream'])
            <input type="hidden" name="stream_slug" value="{{ $preset['stream'] }}">
          @endif
          @if($preset['year'])
            <input type="hidden" name="year" value="{{ (int)$preset['year'] }}">
          @endif
          @if($preset['semester'])
            <input type="hidden" name="semester" value="{{ $preset['semester'] }}">
          @endif

          <div class="col-12">
            <div class="badges">
              @if($presetStream)
                <span class="badge">{{ $rtl?($presetStream->name_ar ?? $presetStream->name_en):($presetStream->name_en ?? $presetStream->name_ar) }}</span>
              @endif
              @if($preset['year'])
                <span class="badge">{{ $preset['year'] }}</span>
              @endif
              @if($preset['semester'])
                <span class="badge">
                  {{ $preset['semester']==='first' ? ($rtl?'الفصل الأول':'First') : ($rtl?'الفصل الثاني':'Second') }}
                </span>
              @endif
            </div>
          </div>
        @else
          {{-- ADMIN PANEL: نعطي المستخدم اختيار الفرع/السنة/الفصل --}}
          <div class="col-6">
            <label class="label">{{ $rtl?'الفرع':'Stream' }}</label>
            <select name="stream_slug" id="stream" class="select" required>
              <option value="">{{ $rtl?'اختر الفرع':'Choose stream' }}</option>
              @foreach($streams as $s)
                <option value="{{ $s->slug }}">{{ $rtl?($s->name_ar ?? $s->name_en):($s->name_en ?? $s->name_ar) }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-3">
            <label class="label">{{ $rtl?'السنة':'Year' }}</label>
            <select name="year" id="year" class="select" required>
              <option value="">{{ $rtl?'اختر السنة':'Choose year' }}</option>
              @foreach($years as $y)
                <option value="{{ $y }}">{{ $y }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-3">
            <label class="label">{{ $rtl?'الفصل':'Semester' }}</label>
            <select name="semester" id="semester" class="select" required>
              <option value="">{{ $rtl?'اختر الفصل':'Choose semester' }}</option>
              <option value="first">{{ $rtl?'الفصل الأول':'First' }}</option>
              <option value="second">{{ $rtl?'الفصل الثاني':'Second' }}</option>
            </select>
          </div>
        @endif
        {{-- ===================================================== --}}

        {{-- التخصص (يتوقف على الفرع) --}}
        <div class="col-6">
          <label class="label">{{ $rtl?'التخصص (اختياري)':'Specialty (optional)' }}</label>
          <select name="specialty_id" id="specialty" class="select" {{ $preset['stream'] ? '' : 'disabled' }}>
            <option value="">{{ $rtl?'اختر التخصص':'Choose specialty' }}</option>
          </select>
        </div>

        {{-- المادة (يتوقف على التخصص) --}}
        <div class="col-6">
          <label class="label">{{ $rtl?'المادة':'Subject' }}</label>
          <select name="subject_id" id="subject" class="select" disabled required>
            <option value="">{{ $rtl?'اختر المادة':'Choose subject' }}</option>
          </select>
          <div class="help">
            {{ $rtl?'اختر الفرع ثم التخصص لتظهر المواد.':'Pick stream then specialty to load subjects.' }}
          </div>
        </div>

        {{-- الملف --}}
        <div class="col-12">
          <label class="label">{{ $rtl?'الملف':'File' }}</label>
          <div class="drop" id="dropzone">
            <div>
              <i class="fa-solid fa-file-arrow-up"></i>
              <div style="margin-top:.35rem">
                {{ $rtl?'اسحب الملف إلى هنا أو انقر للاختيار':'Drag & drop or click to choose a file' }}
                <br/>{{ $rtl?'مسموح: pdf, txt — حتى 10MB':'Allowed: pdf, txt — up to 10MB' }}
              </div>
            </div>
          </div>
          <input type="file" name="document" id="fileInput" accept=".pdf,.txt,text/plain,application/pdf" hidden required>
        </div>

        <div class="col-12 actions">
          <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-cloud-arrow-up"></i> {{ $rtl?'رفع':'Upload' }}
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
@endpush

<script>
(function(){
  const isRTL = {{ $rtl ? 'true' : 'false' }};
  const presetStream = @json($preset['stream']);
  const $stream    = document.getElementById('stream');     // قد لا يكون موجودًا عند preset
  const $specialty = document.getElementById('specialty');
  const $subject   = document.getElementById('subject');
  const drop       = document.getElementById('dropzone');
  const fileInput  = document.getElementById('fileInput');

  async function fetchJSON(url){
    const res = await fetch(url, {headers:{'X-Requested-With':'XMLHttpRequest'}});
    if(!res.ok) throw new Error('HTTP '+res.status);
    return res.json();
  }
  function resetSelect(sel, placeholder){
    sel.innerHTML = '';
    const opt = document.createElement('option');
    opt.value = '';
    opt.textContent = placeholder;
    sel.appendChild(opt);
  }

  // تحميل التخصصات تبع الفرع
  async function loadSpecialtiesByStream(slug){
    resetSelect($specialty, isRTL ? 'اختر التخصص' : 'Choose specialty');
    resetSelect($subject,   isRTL ? 'اختر المادة'   : 'Choose subject');
    $specialty.disabled = true; $subject.disabled = true;
    if(!slug) return;
    try{
      const data = await fetchJSON(`/api/specialties/${slug}`);
      data.forEach(sp=>{
        const opt = document.createElement('option');
        opt.value = sp.id;
        opt.textContent = isRTL ? (sp.name_ar||sp.name_en) : (sp.name_en||sp.name_ar);
        $specialty.appendChild(opt);
      });
      $specialty.disabled = false;
    }catch(e){ console.error(e); }
  }

  // تحميل المواد تبع التخصص
  async function loadSubjectsBySpecialty(id){
    resetSelect($subject, isRTL ? 'اختر المادة' : 'Choose subject');
    $subject.disabled = true;
    if(!id) return;
    try{
      const data = await fetchJSON(`/api/subjects/${id}`);
      data.forEach(sb=>{
        const opt = document.createElement('option');
        opt.value = sb.id;
        opt.textContent = isRTL ? (sb.name_ar||sb.name_en) : (sb.name_en||sb.name_ar);
        $subject.appendChild(opt);
      });
      $subject.disabled = false;
    }catch(e){ console.error(e); }
  }

  // لو الصفحة دخلت مع preset stream نحمّل التخصصات مباشرة
  if(presetStream){ loadSpecialtiesByStream(presetStream); }

  // عند تغيير الفرع (وذلك في وضع ADMIN PANEL فقط)
  if($stream){
    $stream.addEventListener('change', ()=> loadSpecialtiesByStream($stream.value));
  }

  // عند تغيير التخصص
  $specialty.addEventListener('change', ()=> loadSubjectsBySpecialty($specialty.value));

  // سحب وإفلات الملف
  function openPicker(){ fileInput.click(); }
  drop.addEventListener('click', openPicker);
  ['dragenter','dragover'].forEach(ev=>{
    drop.addEventListener(ev, e=>{ e.preventDefault(); drop.classList.add('dragover'); });
  });
  ['dragleave','drop'].forEach(ev=>{
    drop.addEventListener(ev, e=>{ e.preventDefault(); drop.classList.remove('dragover'); });
  });
  drop.addEventListener('drop', e=>{
    const f = e.dataTransfer?.files?.[0];
    if(f){ fileInput.files = e.dataTransfer.files; }
  });
})();
</script>
@endsection

@extends('layouts.app', ['title' => __('Upload')])

@section('content')
@php
  use App\Models\{Stream,Year,Subject};
  $rtl = app()->getLocale()==='ar';

  $streams = Stream::select('id','slug','name_ar','name_en')->get();
  $years   = Year::orderBy('year','desc')->get();

  // subjectsByStream: map slug => subjects[]
  $subjectsByStream = [];
  foreach ($streams as $s) {
      $subjectsByStream[$s->slug] = Subject::query()
        ->select('subjects.id','subjects.name_ar','subjects.name_en')
        ->join('specialties','specialties.id','=','subjects.specialty_id')
        ->where('specialties.stream_id',$s->id)
        ->orderBy($rtl?'subjects.name_ar':'subjects.name_en')
        ->get()->toArray();
  }
@endphp

<style>
  .upload-shell{ max-width:980px; margin:24px auto; padding:0 14px }
  .card{ background:linear-gradient(145deg,rgba(15,23,42,.95),rgba(30,41,59,.9));
         border:1px solid rgba(148,163,184,.12); border-radius:22px; padding:22px }
  .grid{ display:grid; grid-template-columns:1fr 1fr; gap:12px }
  @media(max-width:768px){ .grid{ grid-template-columns:1fr } }
  .control{ display:flex; flex-direction:column; gap:8px }
  .label{ color:#cbd5e1; font-weight:800; font-size:.9rem }
  .control-input,.control-select{ width:100%; padding:.9rem 1rem; background:rgba(15,23,42,.86);
                                  border:2px solid rgba(148,163,184,.22); border-radius:12px;
                                  color:#e2e8f0; font-weight:600 }
  .drop{ position:relative; margin-top:12px; border:2px dashed rgba(148,163,184,.3);
         border-radius:14px; padding:28px 18px; text-align:center; color:#cbd5e1 }
  .drop input[type=file]{ position:absolute; inset:0; opacity:0; cursor:pointer }
  .btn-primary{ display:inline-flex; gap:.6rem; padding:.75rem 1.2rem; border:0; border-radius:12px; color:#fff;
                background:linear-gradient(135deg,#6366f1,#8b5cf6); font-weight:900 }
</style>

<div class="upload-shell" dir="{{ $rtl ? 'rtl' : 'ltr' }}">
  <div class="card">
    <form id="uploadForm" method="POST" action="{{ route('admin.documents.store') }}" enctype="multipart/form-data">
      @csrf

      <div class="grid">
        <div class="control">
          <label class="label">{{ $rtl ? 'العنوان' : 'Title' }}</label>
          <input class="control-input" name="title" placeholder="{{ $rtl ? 'اكتب عنوان المستند' : 'Enter document title' }}" required>
        </div>

        <div class="control">
          <label class="label">{{ $rtl ? 'اللغة' : 'Language' }}</label>
          <select class="control-select" name="language" required>
            <option value="arabic">{{ $rtl ? 'العربية' : 'Arabic' }}</option>
            <option value="english">{{ $rtl ? 'الإنجليزية' : 'English' }}</option>
          </select>
        </div>

        <div class="control">
          <label class="label">{{ $rtl ? 'الفرع' : 'Stream' }}</label>
          <select class="control-select" id="streamSelect" name="stream_slug" required>
            <option value="" selected disabled>{{ $rtl ? 'اختر الفرع' : 'Choose stream' }}</option>
            @foreach($streams as $s)
              <option value="{{ $s->slug }}">{{ $rtl ? $s->name_ar : $s->name_en }}</option>
            @endforeach
          </select>
        </div>

        <div class="control">
          <label class="label">{{ $rtl ? 'السنة' : 'Year' }}</label>
          <select class="control-select" id="yearSelect" name="year" required>
            <option value="" selected disabled>{{ $rtl ? 'اختر السنة' : 'Choose year' }}</option>
            @foreach($years as $y)
              <option value="{{ $y->year }}">{{ $y->year }}</option>
            @endforeach
          </select>
        </div>

        <div class="control">
          <label class="label">{{ $rtl ? 'المادة' : 'Subject' }}</label>
          <select class="control-select" id="subjectSelect" name="subject_id" required>
            <option value="" selected disabled>{{ $rtl ? 'اختر المادة' : 'Choose subject' }}</option>
          </select>
        </div>

        {{-- عمود فارغ لموازنة الشبكة --}}
        <div></div>
      </div>

      <label class="control" style="margin-top:8px;">
        <span class="label">{{ $rtl ? 'الملف' : 'File' }}</span>
        <div id="dropZone" class="drop">
          <div style="font-weight:900">{{ $rtl ? 'اسحب الملف أو انقر للاختيار' : 'Drag & drop or click to choose' }}</div>
          <div class="drop-help">{{ $rtl ? 'مسموح: ‎.pdf .txt حتى 10MB' : 'Allowed: .pdf .txt up to 10MB' }}</div>
          <input id="fileInput" type="file" name="document" accept=".pdf,.txt" required>
        </div>
      </label>

      <div style="margin-top:16px">
        <button id="submitBtn" type="submit" class="btn-primary">
          <i class="fa-solid fa-upload"></i> {{ $rtl ? 'رفع' : 'Upload' }}
        </button>
      </div>
    </form>
  </div>
</div>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<script>
(() => {
  const subjectsByStream = @json($subjectsByStream, JSON_UNESCAPED_UNICODE);
  const streamSelect  = document.getElementById('streamSelect');
  const subjectSelect = document.getElementById('subjectSelect');
  const drop   = document.getElementById('dropZone');
  const input  = document.getElementById('fileInput');
  const form   = document.getElementById('uploadForm');
  const submit = document.getElementById('submitBtn');

  function fillSubjects(){
    const slug = streamSelect.value;
    subjectSelect.innerHTML = '<option value="" disabled selected>' + (document.documentElement.lang==='ar' ? 'اختر المادة' : 'Choose subject') + '</option>';
    const items = subjectsByStream[slug] || [];
    for(const it of items){
      const opt = document.createElement('option');
      opt.value = it.id;
      opt.textContent = (document.documentElement.lang==='ar') ? it.name_ar : it.name_en;
      subjectSelect.appendChild(opt);
    }
  }
  streamSelect.addEventListener('change', fillSubjects);

  // dropzone
  ['dragenter','dragover','dragleave','drop'].forEach(ev => {
    drop.addEventListener(ev, e => { e.preventDefault(); e.stopPropagation(); });
  });
  drop.addEventListener('dragover', () => drop.style.borderColor = '#6366f1');
  ['dragleave','drop'].forEach(() => drop.style.borderColor = 'rgba(148,163,184,.3)');
  drop.addEventListener('drop', e => { if(e.dataTransfer.files?.length){ input.files = e.dataTransfer.files; input.dispatchEvent(new Event('change',{bubbles:true})); }});
  input.addEventListener('change', () => {
    if (input.files?.[0]) {
      drop.querySelector('.drop-help').textContent = (document.documentElement.lang==='ar' ? 'تم اختيار: ' : 'Selected: ') + input.files[0].name;
      drop.style.borderColor = '#10b981';
    }
  });

  // منع الإرسال المزدوج
  form.addEventListener('submit', (e) => {
    if (!input.files || !input.files.length) {
      e.preventDefault();
      drop.style.borderColor = '#ef4444';
      drop.querySelector('.drop-help').textContent = (document.documentElement.lang==='ar') ? 'يرجى اختيار ملف أولاً' : 'Please choose a file first';
      return;
    }
    submit.disabled = true;
    submit.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> ' + (document.documentElement.lang==='ar' ? 'جاري الرفع...' : 'Uploading...');
  });

  // Debug بسيط للمساعدة
  console.log('subjectsByStream keys:', Object.keys(subjectsByStream));
})();
</script>
@endsection

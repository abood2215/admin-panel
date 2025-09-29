{{-- resources/views/documents/show.blade.php --}}
@php
  $rtl   = app()->getLocale()==='ar';
  $dir   = $rtl ? 'rtl' : 'ltr';
  $docId = $document->id;

  /**
   * إزالة أي ترميز للحروف من نص الخيار عند العرض (A) أو A) أو A. أو (أ) …إلخ
   */
  function view_clean_choice_text($s){
      // 1) قص المسافات
      $s = trim((string)$s);

      // 2) إزالة البادئات مثل: "A) " ، "A. " ، "أ) " …
      $s = preg_replace('/^\s*(?:[A-F]|[أبجده])\s*[\)\.\-]\s*/u', '', $s);

      // 3) إزالة اللواحق مثل: " (A)" أو "(أ)"
      $s = preg_replace('/\s*[\(（]\s*(?:[A-F]|[أبجده])\s*[\)）]\s*$/u', '', $s);

      // 4) تنظيف متفرقات مزدوجة المسافات/الفواصل
      $s = preg_replace('/\s{2,}/', ' ', $s);
      return trim($s);
  }

  /**
   * استخراج حرف الخيار (A/B/C/… أو أ/ب/ج/…)
   * إذا لم يوجد ضمن النص، نولد الحرف اعتمادًا على الترتيب.
   */
  function view_choice_letter($index, $raw){
      // جرّب التقاط من النص
      if (preg_match('/^\s*([A-F]|[أبجده])\s*[\)\.\-]/u', (string)$raw, $m)) {
          return mb_strtoupper($m[1], 'UTF-8');
      }
      // توليد افتراضي بالإنجليزي
      $labels = ['A','B','C','D','E','F'];
      return $labels[$index] ?? chr(65+$index);
  }
@endphp

@extends('layouts.app')

@section('title', $document->title)

@section('content')
<div class="container my-4" dir="{{ $dir }}">
  {{-- Header --}}
  <div class="rounded-2 p-3 mb-3" style="background:linear-gradient(90deg,#22c55e,#06b6d4);color:#fff;">
    <div class="d-flex align-items-center gap-3">
      <div class="fw-bold fs-5">
        {{ $document->id }}
      </div>
      <span class="badge bg-dark-subtle text-dark">{{ $document->questions->count() }} سؤال</span>
      <div class="ms-auto fw-bold">{{ $document->title }}</div>
    </div>
  </div>

  {{-- Questions --}}
  @forelse($document->questions->sortBy('sort') as $qi => $q)
    <div class="mb-4 rounded-3 border" style="border-color:#2b3240;background:#2a313d;">
      <div class="p-3 border-bottom d-flex align-items-center justify-content-between" style="border-color:#394151;">
        <div class="fw-bold">
          {{ $qi+1 }}. {{ $q->question }}
        </div>
        <span class="small text-muted">{{ __('تم رصد الإجابة الصحيحة') }}</span>
      </div>

      {{-- خيارات --}}
      <div class="list-group list-group-flush">
        @php
          $opts = array_values((array)($q->options ?? []));
        @endphp

        @foreach($opts as $oi => $raw)
          @php
            $letter = view_choice_letter($oi, $raw);
            $text   = view_clean_choice_text($raw);
            // صحيح/غير صحيح (مطابقة نصية بعد التنظيف لكلا الطرفين)
            $correctText = $q->correct_answer ? view_clean_choice_text($q->correct_answer) : null;
            $isCorrect   = $correctText !== null && $text !== '' && ($text === $correctText);
          @endphp

          <label class="list-group-item d-flex align-items-center gap-3" style="background:#333a49;border-color:#394151;color:#e5e7eb;">
            <input type="radio" class="form-check-input" name="q{{ $q->id }}" disabled {{ $isCorrect ? 'checked' : '' }}>
         
            <span class="flex-grow-1">{{ $text }}</span>
            @if($isCorrect)
              <span class="badge bg-success">{{ $rtl ? 'صحيح' : 'Correct' }}</span>
            @endif
          </label>
        @endforeach
      </div>
    </div>
  @empty
    <div class="alert alert-info">{{ $rtl ? 'لا توجد أسئلة بعد.' : 'No questions yet.' }}</div>
  @endforelse
</div>

{{-- autodir بسيط لعناوين الخيارات إن لزم --}}
<script>
(function(){
  const isRTLChar = c => /[\u0600-\u06FF]/.test(c);
  const autodir = el => {
    const t = (el.textContent||'').trim();
    for (let i=0;i<t.length;i++){
      const ch=t[i];
      if (/\p{L}|\p{N}/u.test(ch)){
        const rtl=isRTLChar(ch);
        el.setAttribute('dir', rtl?'rtl':'ltr');
        el.style.textAlign = rtl?'right':'left';
        return;
      }
    }
    el.removeAttribute('dir');
    el.style.textAlign='start';
  };

  document.querySelectorAll('.list-group-item span.flex-grow-1').forEach(autodir);
})();
</script>
@endsection

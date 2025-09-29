@extends('layouts.app')

@section('content')
<style>
  /* ====== الشكل العام (نفس روح التصميم القديم مع صقل بسيط) ====== */
  .glass{background:rgba(17,24,39,.6);backdrop-filter:blur(6px);border:1px solid rgba(148,163,184,.14);border-radius:14px}
  .header-grad{background:linear-gradient(90deg,#22c55e 0,#06b6d4 70%);border-radius:14px;color:#052e2b;font-weight:800}
  .muted{color:#94a3b8}
  .fw-800{font-weight:800}
  .q-title{font-weight:800;color:#e5e7eb}

  /* شارة الحالة */
  .chip{display:inline-flex;align-items:center;gap:.45rem;border-radius:999px;padding:.28rem .6rem;font-weight:700;font-size:.88rem}
  .chip-ok{background:#064e3b;color:#d1fae5}
  .chip-bad{background:#7f1d1d;color:#fee2e2}
  .chip-neutral{background:#334155;color:#cbd5e1}

  /* شريط التقدّم */
  .progress{height:10px;background:#0b1220;border-radius:999px}
  .bar{height:100%;background:linear-gradient(90deg,#22c55e,#06b6d4);border-radius:999px}

  /* الخيارات (نَفَس القديم) */
  .opt{display:flex;align-items:center;gap:.7rem;background:rgba(2,6,23,.25);border:1px solid rgba(148,163,184,.14);border-radius:12px;padding:.65rem .8rem;margin:.45rem 0;transition:.15s}
  .opt:hover{transform:translateY(-1px);border-color:#64748b}
  .opt small{letter-spacing:.2px;opacity:.95}
  .opt .badge{min-width:2rem;text-align:center;border-radius:8px;padding:.2rem .45rem;font-weight:800}
  .badge-gray{background:#0b1220;color:#93a5be;border:1px solid rgba(148,163,184,.16)}

  .opt.correct{background:#064e3b;border-color:#065f46;color:#e7fff6}
  .opt.correct .badge{background:#065f46;color:#ccfbf1;border:none}
  .opt.wrong.sel{background:#7f1d1d;border-color:#991b1b;color:#ffecec}
  .opt.wrong.sel .badge{background:#991b1b;color:#fee2e2;border:none}

  .k-label{color:#cbd5e1;font-weight:700}
  .note{color:#94a3b8}

  /* زر الإرسال */
  .btn-primary.rounded-3{border-radius:12px}

  /* ====== دعم اتجاه اللغة لكل سؤال/خياراته ====== */
  .rtl      {direction:rtl;text-align:right}
  .ltr      {direction:ltr;text-align:left}
  .opt.rtl  {flex-direction:row-reverse}
  .opt.rtl .badge {margin-left:.35rem;margin-right:0}
  .opt.ltr .badge {margin-right:.35rem;margin-left:0}
  /* ضبط مكان دائرة الـ radio مع الانعكاس */
  .opt input[type="radio"]{transform:translateY(1px)}
  .opt.rtl  input[type="radio"]{margin-right:0;margin-left:.2rem}
  .opt.ltr  input[type="radio"]{margin-left:0;margin-right:.2rem}
</style>

@php
  $res = session('results');          // نتائج التصحيح (إن وُجدت)
  $locked = isset($res);              // عند وجود نتائج نقفل الإدخال
  $detailsByIndex = [];
  if ($res && isset($res['details'])) {
      foreach ($res['details'] as $row) $detailsByIndex[$row['index']] = $row;
  }

  // دالة بسيطة لاكتشاف العربية داخل النص
  $isArabic = function($text){
      return (bool) preg_match('/\p{Arabic}/u', (string)$text);
  };

  /**
   * تنظيف نص الخيار المعروض من أي ترميز مثل:
   * A) ... / (A) ... / أ) ... / ... (A)
   * + إزالة محارف الاتجاه الخفية.
   * ملاحظة: لا نغيّر قيمة الخيار الفعلية المرسلة للـform.
   */
  function view_clean_choice_text($s){
      $s = (string)$s;
      // إزالة محارف اتجاه LTR/RTL
      $s = preg_replace('/[\x{200E}\x{200F}\x{202A}-\x{202E}\x{2066}-\x{2069}]/u', '', $s);
      $s = trim($s);

      // إزالة بادئة: (A) | A) | A. | (أ) | أ) | أ. | ... إلخ
      $s = preg_replace('/^\s*(?:[\(\[\{]?\s*(?:[A-F]|[أبجده])\s*[\)\]\}]?\s*(?:[\.\-:])?)\s+/u','',$s);

      // إزالة لاحقة: ... (A) | ... A) | ... A. | ... (أ) | ... أ) | ... أ.
      $s = preg_replace('/\s*(?:[\(\[\{]?\s*(?:[A-F]|[أبجده])\s*[\)\]\}]?\.?)\s*$/u','',$s);

      // مسافات زائدة
      $s = preg_replace('/\s{2,}/',' ',$s);

      return trim($s);
  }
@endphp

{{-- رأس الصفحة --}}
<div class="header-grad glass p-3 mb-3">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div class="fw-800">📄 {{ $document->title }}</div>
    <div class="chip chip-neutral">🗂️ {{ $document->questions->count() }} سؤال</div>
  </div>
</div>

{{-- تنبيه أعلى (تم التصحيح) --}}
@if($res)
  <div class="glass px-3 py-2 mb-2 text-end muted">Your answers were graded.</div>
@endif

{{-- بطاقة النتيجة --}}
@if($res)
  <div class="glass p-3 mb-3">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div class="chip {{ $res['score'] >= 60 ? 'chip-ok' : 'chip-bad' }}">
        <span>النتيجة:</span>
        <span>{{ $res['correct'] }} / {{ $res['answered'] }} صحيحة</span>
        <span>({{ $res['score'] }}%)</span>
      </div>
      <div class="flex-grow-1">
        <div class="progress"><div class="bar" style="width: {{ $res['score'] }}%"></div></div>
        <div class="muted mt-1">من أصل {{ $res['total'] }} سؤال</div>
      </div>
    </div>
  </div>
@endif

{{-- رسائل فلاش --}}
@if(session('success'))  <div class="alert alert-success glass">{{ session('success') }}</div> @endif
@if(session('warning'))  <div class="alert alert-warning glass">{{ session('warning') }}</div> @endif

<form method="POST" action="{{ route('documents.submit', $document->id) }}">
  @csrf

  @forelse($document->questions as $index => $q)
    @php
      // تحديد اتجاه هذا السؤال حسب النص أو حسب لغة الوثيقة
      $rtl = $isArabic($q->question) || (isset($document->language) && $document->language === 'arabic');
      $dirClass = $rtl ? 'rtl' : 'ltr';

      $opts = $q->options ?? [];
      $row  = $detailsByIndex[$index] ?? null;
      $userAnswer = $row['user_answer'] ?? ($answers[$index] ?? null);
      $isCorrect  = $row['is_correct'] ?? null;
      $correctAns = $row['correct_answer'] ?? $q->correct_answer;
      $letters    = ['A','B','C','D','E','F'];
    @endphp

    <div class="glass p-3 mb-3 {{ $dirClass }}">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="q-title">
          {{ $index+1 }}. {{ $q->question }}
        </div>
        @if($isCorrect === true)
          <span class="chip chip-ok">إجابتك صحيحة</span>
        @elseif($isCorrect === false)
          <span class="chip chip-bad">إجابتك خاطئة</span>
        @elseif($correctAns)
          <span class="chip chip-neutral">تم رصد الإجابة الصحيحة</span>
        @endif
      </div>

      <div class="mt-3">
        {{-- خيارات متعددة --}}
        @if(!empty($opts))
          @foreach($opts as $k => $opt)
            @php
              $isUser   = trim((string)$userAnswer) === trim((string)$opt);
              $isRight  = $correctAns && (trim((string)$correctAns) === trim((string)$opt));
              $cls      = 'opt '.$dirClass;
              if ($res) {
                if ($isRight)      $cls .= ' correct';
                elseif ($isUser)   $cls .= ' wrong sel';
              }
              // نعرض النص بعد تنظيفه من الترميز، مع الإبقاء على قيمة الـ form الأصلية
              $display  = view_clean_choice_text($opt);
              $id       = "q{$index}_".substr(md5($opt.$k),0,8);
            @endphp

            <label for="{{ $id }}" class="{{ $cls }}">
              {{-- شارة الحرف تبقى في الـUI --}}
              <span class="badge badge-gray">{{ $letters[$k] ?? chr(65+$k) }}</span>

              {{-- الـ radio --}}
              <input id="{{ $id }}" type="radio" class="form-check-input"
                     name="answers[{{ $index }}]" value="{{ $opt }}"
                     @checked($isUser) @disabled($locked)>
              <small>{{ $display }}</small>
            </label>
          @endforeach
        @else
          {{-- إجابة نصية إن لا توجد خيارات --}}
          <input type="text" class="form-control"
                 name="answers[{{ $index }}]"
                 value="{{ $userAnswer }}"
                 placeholder="{{ $rtl ? 'أدخل إجابتك' : 'Type your answer' }}"
                 @disabled($locked)>
        @endif
      </div>

      {{-- إظهار الإجابة الصحيحة والملاحظة --}}
      @if($res)
        <div class="mt-3">
          @if($correctAns)
            <div class="k-label">{{ $rtl ? 'الإجابة الصحيحة' : 'Correct answer' }}:
              <span class="text-success fw-bold">{{ view_clean_choice_text($correctAns) }}</span>
            </div>
          @endif
          @if(!empty($row['feedback']))
            <div class="note mt-1">{{ $rtl ? 'ملاحظة' : 'Note' }}: {{ $row['feedback'] }}</div>
          @endif
        </div>
      @endif
    </div>
  @empty
    <div class="glass alert alert-info {{ (isset($document->language) && $document->language==='arabic') ? 'rtl' : 'ltr' }}">
      {{ (isset($document->language) && $document->language==='arabic') ? 'لا توجد أسئلة لهذا المستند.' : 'No questions extracted for this document.' }}
    </div>
  @endforelse

  @unless($locked)
    <div class="d-flex justify-content-end">
      <button type="submit" class="btn btn-primary px-4 py-2 rounded-3">{{ (isset($document->language) && $document->language==='arabic') ? 'إرسال' : 'Submit' }}</button>
    </div>
  @endunless
</form>

{{-- زر الرجوع (خارج النموذج) --}}
<div class="mt-4 pt-3" style="border-top:1px solid rgba(148,163,184,.14)">
  <a href="{{ route('admin.dashboard') }}" class="btn btn-outline">
    {{ (isset($document->language) && $document->language==='arabic') ? '← Back to Control Panel ' : '← Back to Dashboard' }}
  </a>
</div>
</div>
@endsection

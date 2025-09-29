@extends('layouts.app')

@section('content')
<style>
.form-wrap{max-width:760px;margin:24px auto;padding:16px;border:1px solid var(--border,#1f2937);border-radius:14px;background:var(--panel,#0f172a)}
.label{color:#e5e7eb;font-weight:800;margin:.35rem 0}
.select,.input{width:100%;padding:.8rem 1rem;border-radius:.65rem;border:1px solid var(--border,#1f2937);background:#141a2a;color:#e5e7eb}
.btn{display:inline-flex;align-items:center;gap:.5rem;padding:.7rem 1.1rem;border-radius:.7rem;border:0;background:#6366f1;color:#fff;font-weight:900}
.btn:hover{filter:brightness(1.05)}
</style>

<div class="container py-4" dir="{{ app()->getLocale()==='ar'?'rtl':'ltr' }}">
  <div class="form-wrap">
    <h3 class="mb-3" style="color:#e5e7eb;font-weight:900">
      {{ app()->getLocale()==='ar' ? 'اختيار السنة والفصل' : 'Choose Year & Semester' }}
    </h3>

    <form method="POST" action="{{ route('admin.specialties.browse') }}">
      @csrf
      <input type="hidden" name="specialty_id" value="{{ $specialty->id }}">

      <div class="mb-3">
        <div class="label">{{ app()->getLocale()==='ar' ? 'التخصص' : 'Specialty' }}</div>
        <div class="input" style="opacity:.8" disabled>
          {{ app()->getLocale()==='ar' ? $specialty->name_ar : $specialty->name_en }}
        </div>
      </div>

      <div class="mb-3">
        <label class="label">{{ app()->getLocale()==='ar' ? 'السنة' : 'Year' }}</label>
        <select class="select" name="year" required>
          <option value="" disabled selected>{{ app()->getLocale()==='ar' ? 'اختر السنة' : 'Choose year' }}</option>
          @foreach($years as $y)
            <option value="{{ $y->year }}">{{ $y->year }}</option>
          @endforeach
        </select>
      </div>

      <div class="mb-4">
        <label class="label">{{ app()->getLocale()==='ar' ? 'الفصل' : 'Semester' }}</label>
        <select class="select" name="semester" required>
          <option value="" disabled selected>{{ app()->getLocale()==='ar' ? 'اختر الفصل' : 'Choose semester' }}</option>
          @foreach($semesters as $k => $name)
            <option value="{{ $k }}">{{ $name }}</option>
          @endforeach
        </select>
      </div>

      <button class="btn" type="submit">
        <i class="fa-solid fa-arrow-{{ app()->getLocale()==='ar' ? 'left' : 'right' }}"></i>
        {{ app()->getLocale()==='ar' ? 'متابعة' : 'Continue' }}
      </button>
    </form>
  </div>
</div>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
@endsection

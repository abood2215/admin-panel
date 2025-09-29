@extends('layouts.app')
@section('content')
@php $rtl = app()->getLocale()==='ar'; @endphp
<style>
.board{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px}
.card{background:#0f172a;border:1px solid #1f2937;color:#e5e7eb;border-radius:12px}
.card-header{display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #1f2937;padding:.6rem .75rem}
.badge{background:#111827;border:1px solid #1f2937;color:#9ca3af;font-weight:700}
.doc{border:1px dashed #1f2937;border-radius:10px;padding:.55rem;margin:.55rem}
.actions{display:flex;gap:.35rem}
</style>

<div class="container py-4" style="direction: {{ $rtl?'rtl':'ltr' }}">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <div>
      <h4 class="fw-bold mb-1">{{ $rtl ? 'مواد التخصص' : 'Specialty Subjects' }}</h4>
      <div class="text-muted">
        {{ $rtl ? $specialty->name_ar : $specialty->name_en }} —
        {{ $year->year }} —
        {{ $semester==1 ? ($rtl?'الفصل الأول':'First') : ($rtl?'الفصل الثاني':'Second') }}
      </div>
    </div>
    <a class="btn btn-secondary" href="{{ route('admin.specialties.choose',$specialty->id) }}">
      {{ $rtl ? 'تغيير السنة/الفصل' : 'Change Year/Semester' }}
    </a>
  </div>

  <div class="board">
    @forelse($subjects as $sub)
      <div class="card">
        <div class="card-header">
          <div class="fw-bold">{{ $rtl ? $sub->name_ar : $sub->name_en }}</div>
          <a class="btn btn-sm btn-primary"
             href="{{ route('admin.documents.upload') }}?stream={{ optional($specialty->stream)->slug ?? '' }}&year={{ $year->year }}&subject={{ $sub->id }}&semester={{ $semester }}">
             + {{ $rtl ? 'رفع لهذا القسم' : 'Upload here' }}
          </a>
        </div>
        <div class="p-2">
          @forelse($docsBySubject[$sub->id] as $doc)
            <div class="doc">
              <div class="d-flex justify-content-between mb-1">
                <span class="text-truncate" style="max-width:68%">{{ $doc->title }}</span>
                <span class="badge">{{ strtoupper($doc->language) }}</span>
              </div>
              <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">{{ optional($doc->created_at)->format('Y-m-d') }}</small>
                <div class="actions">
                  <a class="btn btn-sm btn-outline-light" href="{{ route('documents.view',$doc->id) }}" target="_blank">{{ $rtl?'عرض':'View' }}</a>
                  <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.documents.edit',$doc->id) }}">{{ $rtl?'تعديل':'Edit' }}</a>
                </div>
              </div>
            </div>
          @empty
            <div class="text-muted p-2">{{ $rtl ? 'لا يوجد مستندات.' : 'No documents.' }}</div>
          @endforelse
        </div>
      </div>
    @empty
      <div class="text-muted">{{ $rtl ? 'لا يوجد مواد لهذا التخصص.' : 'No subjects for this specialty.' }}</div>
    @endforelse
  </div>
</div>
@endsection

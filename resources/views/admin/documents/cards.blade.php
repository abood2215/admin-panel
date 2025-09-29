@extends('layouts.app')

@section('content')
<style>
.board{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px}
.col{background:#0f172a;border:1px solid #1f2937;border-radius:14px;color:#e5e7eb;overflow:hidden}
.col .hd{display:flex;justify-content:space-between;align-items:center;padding:.7rem .9rem;background:#111827;border-bottom:1px solid #1f2937;font-weight:900}
.item{border-top:1px dashed #1f2937;padding:.6rem .75rem}
.actions{display:flex;gap:.35rem;direction:ltr}
.badge{background:#1f2937;color:#cbd5e1;border:1px solid #334155;border-radius:999px;padding:.15rem .5rem;font-size:.72rem}
.lang{background:rgba(79,70,229,.15);border:1px solid rgba(79,70,229,.35)}
.pill{border:1px solid #14532d;background:#052e1a;color:#16a34a;border-radius:999px;padding:.1rem .45rem;font-weight:800;font-size:.72rem}
</style>

<div class="container py-4" dir="{{ app()->getLocale()==='ar'?'rtl':'ltr' }}">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <div class="fw-bold" style="color:#e5e7eb">
      {{ app()->getLocale()==='ar' ? 'التخصص' : 'Specialty' }}:
      <span class="badge">{{ app()->getLocale()==='ar' ? $specialty->name_ar : $specialty->name_en }}</span>
      <span class="badge">{{ $yearNumber ?: '—' }}</span>
      <span class="badge">{{ $semester==='first' ? (app()->getLocale()==='ar'?'الأول':'First') : (app()->getLocale()==='ar'?'الثاني':'Second') }}</span>
    </div>
    <a class="btn btn-secondary" href="{{ route('admin.specialties.choose',$specialty->id) }}">
      <i class="fa-solid fa-arrow-{{ app()->getLocale()==='ar'?'right':'left' }}"></i>
      {{ app()->getLocale()==='ar' ? 'رجوع' : 'Back' }}
    </a>
  </div>

  <div class="board">
    @forelse($grouped as $year => $rows)
      <div class="col">
        <div class="hd">{{ $year }} <span class="badge">{{ $rows->count() }} {{ app()->getLocale()==='ar'?'ملف':'file' }}</span></div>
        @foreach($rows as $doc)
          <div class="item">
            <div class="d-flex align-items-center justify-content-between">
              <div style="max-width:65%;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                <i class="fa-regular fa-file-lines me-1" style="color:#94a3b8"></i>
                {{ $doc->title }}
              </div>
              <div class="actions">
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('documents.view',$doc->id) }}" target="_blank" title="{{ __('dashboard.view') }}"><i class="fa-solid fa-eye"></i></a>
                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.documents.edit',$doc->id) }}" title="{{ __('dashboard.edit') }}"><i class="fa-solid fa-pen-to-square"></i></a>
                <form method="POST" action="{{ route('admin.documents.destroy',$doc->id) }}" onsubmit="return confirm('{{ app()->getLocale()==='ar'?'حذف الملف؟':'Delete?'}}')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger" title="{{ __('dashboard.delete') }}"><i class="fa-solid fa-trash"></i></button>
                </form>
              </div>
            </div>
            <div class="mt-1 d-flex align-items-center" style="gap:.35rem;flex-wrap:wrap">
              <span class="badge lang">{{ strtoupper($doc->language) }}</span>
              @if($doc->subject)<span class="badge"><i class="fa-solid fa-book-open me-1"></i>{{ app()->getLocale()==='ar'?$doc->subject->name_ar:$doc->subject->name_en }}</span>@endif
              <span class="pill">{{ app()->getLocale()==='ar'?'مُعالج':'Processed' }}</span>
            </div>
          </div>
        @endforeach
      </div>
    @empty
      <div class="text-muted">{{ app()->getLocale()==='ar' ? 'لا يوجد ملفات' : 'No files' }}</div>
    @endforelse
  </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
@endsection

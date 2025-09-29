{{-- resources/views/admin/browse/documents.blade.php --}}
@extends('layouts.app')

@section('content')
@php $rtl = app()->getLocale()==='ar'; @endphp
<style>
.page{direction:{{ $rtl?'rtl':'ltr' }}}
.wrap{max-width:1100px;margin:24px auto;background:#0f172a;border:1px solid #1f2937;border-radius:14px;padding:18px}
.hdr{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
.title{color:#e5e7eb;font-weight:800}
.btn{padding:.55rem .9rem;border-radius:.7rem;border:0;background:#4f46e5;color:#fff;font-weight:700}
.card{background:#0b1220;border:1px solid #1f2937;border-radius:12px;padding:14px;margin-bottom:10px;color:#cbd5e1}
.badge{display:inline-flex;gap:6px;align-items:center;background:#111827;border:1px solid #1f2937;color:#cbd5e1;border-radius:999px;padding:.25rem .6rem;font-size:.85rem}
.row{display:flex;gap:10px;align-items:center;flex-wrap:wrap}
.actions a{margin-inline-start:8px}
.btn-gray{background:#111827;color:#e5e7eb}
.btn-red{background:#ef4444}
</style>

<div class="container page">
  <div class="wrap">

    <div class="hdr">
      <div class="title">
        {{ $rtl?'المستندات — ':'' }}
        {{ $stream->name_ar ?? $stream->name_en }}
        • {{ $year }}
        • {{ $semester === 'first' ? ($rtl?'الفصل الأول':'First') : ($rtl?'الفصل الثاني':'Second') }}
      </div>

      <div class="row">
        <a class="btn"
           href="{{ route('admin.documents.upload', [
                'stream'   => $stream->slug,
                'year'     => $year,
                'semester' => $semester,
           ]) }}">
          <i class="fa-solid fa-cloud-arrow-up"></i>
          {{ $rtl?'رفع لهذه السنة/الفصل':'Upload for this Year/Semester' }}
        </a>

        <a class="btn btn-gray" href="{{ url()->previous() }}">
          <i class="fa-solid fa-arrow-{{ $rtl?'right':'left' }}"></i>
          {{ $rtl?'رجوع':'Back' }}
        </a>
      </div>
    </div>

    @forelse($documents as $doc)
      <div class="card">
        <div class="row" style="justify-content:space-between">
          <div style="font-weight:800">{{ $doc->title ?: $doc->file_name }}</div>
          <div class="row">
            <span class="badge">{{ strtoupper($doc->language) }}</span>
            <span class="badge">{{ $doc->file_size ? ('KB '.intval($doc->file_size/1024)) : '—' }}</span>
            <span class="badge">{{ $rtl?'الحالة:':'status:' }} {{ $doc->status }}</span>
            <span class="badge">{{ $doc->created_at? $doc->created_at->format('Y-m-d'):'' }}</span>
          </div>
        </div>

        <div class="actions" style="margin-top:10px">
          <a class="btn btn-gray" href="{{ route('documents.view',$doc->id) }}" target="_blank">{{ $rtl?'عرض':'View' }}</a>
          <a class="btn btn-gray" href="{{ route('admin.documents.edit',$doc->id) }}">{{ $rtl?'تعديل':'Edit' }}</a>
          <a class="btn btn-red"  href="{{ route('admin.documents.destroy',$doc->id) }}"
             onclick="return confirm('{{ $rtl?'حذف المستند؟':'Delete document?' }}')">{{ $rtl?'حذف':'Delete' }}</a>
        </div>
      </div>
    @empty
      <div class="card" style="text-align:center">{{ $rtl?'لا يوجد بيانات لعرضها':'No data to display' }}</div>
    @endforelse

  </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
@endpush
@endsection

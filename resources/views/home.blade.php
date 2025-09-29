@extends('layouts.app')
@section('title', __('Home'))

@section('content')
<section class="min-h-[70vh] flex flex-col items-center justify-center text-center px-4">
  <h1 class="text-4xl md:text-5xl font-extrabold mb-4 tracking-tight">
    {{ __('Welcome') }} <span class="inline-block align-[-2px]">👋</span>
  </h1>
  <p class="max-w-xl text-base-content/70 mb-6">
    {{ __('Upload PDFs (by admin), let the system extract multiple-choice questions and grade answers automatically.') }}
  </p>
  <div class="flex gap-3">
    <a href="{{ route('documents.index') }}" class="btn btn-primary">{{ __('My Documents') }}</a>
    <a href="{{ route('pricing') }}" class="btn btn-outline">{{ __('Pricing') }}</a>
  </div>
</section>
@endsection

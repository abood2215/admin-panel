@extends('layouts.app')
@section('title', __('Pricing'))

@section('content')
<div class="max-w-7xl mx-auto">
  <div class="grid md:grid-cols-3 gap-8">

    {{-- Starter --}}
    <div class="card bg-base-100 shadow-xl border">
      <div class="card-body">
        <div class="flex items-center justify-between">
          <span class="text-2xl font-semibold">$0</span>
          <span class="badge">{{ __('Free') }}</span>
        </div>
        <h3 class="text-2xl font-bold mt-2">{{ __('Starter') }}</h3>
        <ul class="mt-4 space-y-2 text-base-content/80">
          <li>✔ {{ __('Basic image generation') }}</li>
          <li>✔ {{ __('Community support') }}</li>
          <li class="opacity-50">✖ {{ __('High-res output') }}</li>
          <li class="opacity-50">✖ {{ __('Custom styles') }}</li>
        </ul>
        <a class="btn btn-outline mt-6">{{ __('Get Started') }}</a>
      </div>
    </div>

    {{-- Pro --}}
    <div class="card bg-base-100 shadow-2xl border-2 border-primary">
      <div class="card-body">
        <div class="flex items-center justify-between">
          <span class="text-2xl font-semibold">mo/<span class="text-lg">$12</span></span>
          <span class="badge badge-primary">{{ __('Pro') }}</span>
        </div>
        <h3 class="text-2xl font-bold mt-2">{{ __('Pro') }}</h3>
        <ul class="mt-4 space-y-2 text-base-content/80">
          <li>✔ {{ __('High-resolution image generation') }}</li>
          <li>✔ {{ __('Custom style templates') }}</li>
          <li>✔ {{ __('Batch processing') }}</li>
          <li>✔ {{ __('Priority support') }}</li>
          <li class="opacity-50">✖ {{ __('AI enhancements') }}</li>
          <li class="opacity-50">✖ {{ __('Cloud integration') }}</li>
        </ul>
        <a class="btn btn-primary mt-6">{{ __('Start Pro') }}</a>
      </div>
    </div>

    {{-- Premium --}}
    <div class="card bg-base-100 shadow-xl border">
      <div class="card-body">
        <div class="flex items-center justify-between">
          <span class="text-2xl font-semibold">mo/<span class="text-lg">$29</span></span>
          <span class="badge badge-warning">{{ __('Premium') }}</span>
        </div>
        <h3 class="text-2xl font-bold mt-2">{{ __('Premium') }}</h3>
        <ul class="mt-4 space-y-2 text-base-content/80">
          <li>✔ {{ __('Everything in Pro') }}</li>
          <li>✔ {{ __('AI-driven image enhancements') }}</li>
          <li>✔ {{ __('Seamless cloud integration') }}</li>
          <li>✔ {{ __('Real-time collaboration') }}</li>
          <li>✔ {{ __('Enterprise support') }}</li>
        </ul>
        <a class="btn btn-warning text-white mt-6">{{ __('Go Premium') }}</a>
      </div>
    </div>

  </div>
</div>
@endsection

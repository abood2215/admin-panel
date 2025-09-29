@extends('layouts.app')
@section('title', __('About Us'))

@section('content')
<div class="max-w-6xl mx-auto">
  <div class="bg-base-100 rounded-2xl shadow-2xl border-t-4 border-primary p-8">
    <h1 class="text-center text-3xl md:text-4xl font-extrabold mb-6">{{ __('About Eqra Tech') }}</h1>

    <div class="grid md:grid-cols-2 gap-10 items-center">
      <div class="{{ app()->getLocale()==='ar' ? 'order-2 md:order-1' : '' }}">
        <p class="text-base-content/80 leading-relaxed">
          {{ __('Eqra Tech is a forward-thinking team specializing in AI image generation and digital workflow solutions. Our mission is to empower creators and businesses with smart, user-friendly tools.') }}
        </p>

        <h3 class="mt-6 font-bold text-success">{{ __('What we offer:') }}</h3>
        <ul class="mt-2 space-y-2">
          <li>✔ {{ __('AI-driven logo and image generation') }}</li>
          <li>✔ {{ __('Workflow integration with top platforms') }}</li>
          <li>✔ {{ __('Custom solutions for digital teams') }}</li>
          <li>✔ {{ __('support & consultations 24/7') }}</li>
        </ul>

        <div class="flex gap-3 mt-6 {{ app()->getLocale()==='ar' ? 'justify-end' : '' }}">
          <a href="mailto:info@eqratech.com" class="btn btn-primary">{{ __('Contact Us') }}</a>
          <a href="{{ route('pricing') }}" class="btn btn-outline">{{ __('Our Pricing') }}</a>
        </div>
      </div>

      <div class="flex justify-center {{ app()->getLocale()==='ar' ? 'order-1 md:order-2' : '' }}">
        <img src="{{ asset('images/eqra.png') }}" alt="Eqra Tech"
             class="rounded-xl shadow-lg border-4 border-primary/40 max-w-sm">
      </div>
    </div>
  </div>
</div>
@endsection

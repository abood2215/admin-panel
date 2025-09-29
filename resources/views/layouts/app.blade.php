<!DOCTYPE html>
@php
  $locale = app()->getLocale();
  $dir    = $locale === 'ar' ? 'rtl' : 'ltr';
  $isRTL  = $dir === 'rtl';
@endphp
<html lang="{{ str_replace('_','-', $locale) }}" dir="{{ $dir }}" data-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>@yield('title', config('app.name', 'Eqra Tech'))</title>

  {{-- boot theme early to avoid FOUC --}}
  <script>
    (function () {
      try {
        const saved = localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const theme = saved ? saved : (prefersDark ? 'night' : 'light');
        document.documentElement.setAttribute('data-theme', theme);
      } catch (_) {}
    })();
  </script>

  @vite(['resources/css/app.css','resources/js/app.js'])
  @livewireStyles
  @stack('styles')
</head>
<body class="min-h-screen bg-base-200 text-base-content">

  {{-- Navbar --}}
  @includeIf('layouts.navbar')

  <main class="container mx-auto px-4 py-8">
    @yield('content')
  </main>

  {{-- Footer (اختياري) --}}
  @includeIf('layouts.footer')

  {{-- Chat widget: ثابت أسفل يمين/يسار حسب اللغة --}}
  <div class="fixed z-[9999] bottom-6 {{ $isRTL ? 'left-6' : 'right-6' }}">
    @includeIf('livewire.chat-widget')
  </div>

  {{-- Theme toggler helper --}}
  <script>
    window.toggleTheme = function () {
      const html  = document.documentElement;
      const now   = html.getAttribute('data-theme') || 'light';
      const next  = now === 'night' ? 'light' : 'night';
      html.setAttribute('data-theme', next);
      try { localStorage.setItem('theme', next); } catch (_) {}
      try { document.dispatchEvent(new CustomEvent('theme:changed', {detail: next})); } catch (_) {}
    }
  </script>

  @livewireScripts
  @stack('scripts')
</body>
</html>

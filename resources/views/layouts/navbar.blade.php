@php
  $locale = app()->getLocale();
  $isRTL  = $locale === 'ar';
  $dir    = $isRTL ? 'rtl' : 'ltr';
  $accent = 'from-[#6d65ff] to-[#4f46e5]';
  $active = fn(string $name) => request()->routeIs($name)
      ? 'text-white opacity-100'
      : 'text-white/80 hover:text-white';
@endphp


<nav dir="{{ $dir }}" class="sticky top-0 z-50 w-full bg-[#0f1627]/95 backdrop-blur supports-[backdrop-filter]:backdrop-blur ring-1 ring-white/10 shadow-[0_6px_30px_rgba(0,0,0,.25)]">
  {{-- شريط كامل الصفحة --}}
  <div class="max-w-7xl mx-auto px-3 sm:px-5">
    <div class="h-12 sm:h-14 flex items-center justify-between">

      {{-- Logo --}}
      <a href="{{ route('home') }}" class="font-extrabold tracking-wider text-xs sm:text-sm">
        <span class="bg-gradient-to-r {{ $accent }} bg-clip-text text-transparent">EQRA</span>
        <span class="text-white/70"> TECH</span>
      </a>

      {{-- زر الموبايل --}}
      <button id="nb-toggle" class="md:hidden text-white/80 hover:text-white text-xl leading-none">☰</button>

      {{-- الروابط (ديسكتوب) --}}
      <ul id="nb-links" class="hidden md:flex items-center gap-6 text-[11px] sm:text-[12px] tracking-wide">
        <li><a class="{{ $active('home') }}"    href="{{ route('home') }}">{{ __('Home') }}</a></li>
        <li><a class="{{ $active('pricing') }}" href="{{ route('pricing') }}">{{ __('Pricing') }}</a></li>
        <li><a class="{{ $active('about') }}"   href="{{ route('about') }}">{{ __('About Us') }}</a></li>
      </ul>

      {{-- يمين الشريط: اللغة + الثيم + البروفايل --}}
      <div class="hidden md:flex items-center gap-2">
        {{-- اللغة --}}
        <div class="flex rounded-full bg-white/5 ring-1 ring-white/10 p-0.5">
          <a href="{{ route('locale.set','en') }}"
             class="px-3 py-1 rounded-full text-[11px] {{ $locale==='en' ? 'bg-gradient-to-r '.$accent.' text-white' : 'text-white/80 hover:text-white' }}">EN</a>
          <a href="{{ route('locale.set','ar') }}"
             class="px-3 py-1 rounded-full text-[11px] {{ $locale==='ar' ? 'bg-gradient-to-r '.$accent.' text-white' : 'text-white/80 hover:text-white' }}">ع</a>
        </div>

        {{-- الثيم --}}
        <button id="theme-toggle" class="ml-1 px-3 py-1 rounded-full text-[11px] bg-white/5 hover:bg-white/10 ring-1 ring-white/10">
          <span class="theme-light inline">🌙</span>
          <span class="theme-dark hidden">☀️</span>
        </button>

        {{-- البروفايل --}}
        @auth
          <div class="relative">
            <button id="profile-btn" class="ml-1 flex items-center gap-2 rounded-full bg-white/5 ring-1 ring-white/10 px-2 py-1 hover:bg-white/10">
              <div class="w-7 h-7 rounded-full ring-2 ring-[#6d65ff]/70 overflow-hidden">
                <img src="{{ Auth::user()->avatar ?? asset('images/profile.png') }}" alt="avatar" class="w-full h-full object-cover">
              </div>
              <span class="hidden sm:inline text-xs text-white/90">{{ Str::limit(Auth::user()->name, 14) }}</span>
            </button>

            <div id="profile-menu"
                 class="hidden absolute {{ $isRTL ? 'right-0' : 'left-0' }} mt-2 w-64 bg-[#0b1220] text-white ring-1 ring-white/10 rounded-xl shadow-xl overflow-hidden">
              <div class="px-4 py-3 border-b border-white/10">
                <div class="font-semibold">{{ Auth::user()->name }}</div>
                <div class="text-xs text-white/60">{{ Auth::user()->email }}</div>
              </div>
              <div class="py-1 text-sm">
                @if(!auth()->user()->is_admin)
                  <a href="{{ route('documents.index') }}" class="block px-4 py-2 hover:bg-white/5">{{ __('My Documents') }}</a>
                @endif
                <a href="{{ route('profile.show') }}" class="block px-4 py-2 hover:bg-white/5">{{ __('Profile') }}</a>
                @if(auth()->user()->is_admin)
                  <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 hover:bg-white/5">{{ __('Admin Panel') }}</a>
                @endif
              </div>
              <div class="border-t border-white/10">
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button type="submit" class="w-full text-left px-4 py-2 text-red-300 hover:bg-red-500/10">{{ __('Logout') }}</button>
                </form>
              </div>
            </div>
          </div>
        @else
          <a href="{{ route('login') }}" class="ml-2 px-3 py-1 rounded-full text-[12px] bg-gradient-to-r {{ $accent }} text-white">
            {{ __('Login') }}
          </a>
        @endauth
      </div>
    </div>

    {{-- لوحة الموبايل --}}
    <div id="nb-panel" class="hidden md:hidden pb-3">
      <ul class="flex flex-col gap-2 text-sm">
        <li><a class="{{ $active('home') }}"    href="{{ route('home') }}">{{ __('Home') }}</a></li>
        <li><a class="{{ $active('pricing') }}" href="{{ route('pricing') }}">{{ __('Pricing') }}</a></li>
        <li><a class="{{ $active('about') }}"   href="{{ route('about') }}">{{ __('About Us') }}</a></li>

        <div class="mt-2 flex items-center gap-2">
          <div class="flex rounded-full bg-white/5 ring-1 ring-white/10 p-0.5">
            <a href="{{ route('locale.set','en') }}"
               class="px-3 py-1 rounded-full text-[11px] {{ $locale==='en' ? 'bg-gradient-to-r '.$accent.' text-white' : 'text-white/80 hover:text-white' }}">EN</a>
            <a href="{{ route('locale.set','ar') }}"
               class="px-3 py-1 rounded-full text-[11px] {{ $locale==='ar' ? 'bg-gradient-to-r '.$accent.' text-white' : 'text-white/80 hover:text-white' }}">ع</a>
          </div>
          <button id="theme-toggle-m" class="px-3 py-1 rounded-full text-[11px] bg-white/5 hover:bg-white/10 ring-1 ring-white/10">
            <span class="theme-light inline">🌙</span>
            <span class="theme-dark hidden">☀️</span>
          </button>
        </div>

        @auth
          <div class="mt-3 rounded-lg ring-1 ring-white/10 bg-white/5">
            <div class="px-3 py-2 border-b border-white/10">
              <div class="font-semibold text-sm">{{ Auth::user()->name }}</div>
              <div class="text-xs text-white/60">{{ Auth::user()->email }}</div>
            </div>
            <a href="{{ route('profile.show') }}" class="block px-3 py-2 hover:bg-white/10">{{ __('Profile') }}</a>
            @if(!auth()->user()->is_admin)
              <a href="{{ route('documents.index') }}" class="block px-3 py-2 hover:bg-white/10">{{ __('My Documents') }}</a>
            @endif
            @if(auth()->user()->is_admin)
              <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 hover:bg-white/10">{{ __('Admin Panel') }}</a>
            @endif
            <form method="POST" action="{{ route('logout') }}" class="border-t border-white/10">
              @csrf
              <button type="submit" class="w-full text-left px-3 py-2 text-red-300 hover:bg-red-500/10">{{ __('Logout') }}</button>
            </form>
          </div>
        @else
          <a href="{{ route('login') }}" class="mt-2 inline-block px-3 py-2 rounded-lg bg-gradient-to-r {{ $accent }}">{{ __('Login') }}</a>
        @endauth
      </ul>
    </div>
  </div>
</nav>

{{-- سكربت التحكم --}}
<script>
(function () {
  const html = document.documentElement;
  const saved = localStorage.getItem('theme');
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
  const initial = saved ? saved : (prefersDark ? 'night' : 'light');
  html.setAttribute('data-theme', initial);
  updateThemeIndicators(initial);

  function toggleTheme() {
    const now = html.getAttribute('data-theme') || 'light';
    const next = (now === 'night') ? 'light' : 'night';
    html.setAttribute('data-theme', next);
    try { localStorage.setItem('theme', next); } catch (_) {}
    updateThemeIndicators(next);
  }
  function updateThemeIndicators(theme) {
    const isDark = theme === 'night';
    document.querySelectorAll('.theme-light').forEach(el => {
      el.classList.toggle('hidden', isDark);
      el.classList.toggle('inline', !isDark);
    });
    document.querySelectorAll('.theme-dark').forEach(el => {
      el.classList.toggle('hidden', !isDark);
      el.classList.toggle('inline', isDark);
    });
  }

  const t1 = document.getElementById('theme-toggle');
  const t2 = document.getElementById('theme-toggle-m');
  t1 && t1.addEventListener('click', toggleTheme);
  t2 && t2.addEventListener('click', toggleTheme);

  const b = document.getElementById('nb-toggle');
  const panel = document.getElementById('nb-panel');
  const links = document.getElementById('nb-links');
  if (b) b.addEventListener('click', () => {
    panel.classList.toggle('hidden');
    links.classList.toggle('hidden');
  });

  const pBtn = document.getElementById('profile-btn');
  const pMenu = document.getElementById('profile-menu');
  if (pBtn && pMenu) {
    pBtn.addEventListener('click', () => pMenu.classList.toggle('hidden'));
    document.addEventListener('click', (e) => {
      if (!pMenu.contains(e.target) && !pBtn.contains(e.target)) pMenu.classList.add('hidden');
    });
  }
})();
</script>

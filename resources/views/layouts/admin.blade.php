<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Admin Control Panel – @yield('title')</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  {{-- Livewire Styles --}}
  @livewireStyles
</head>
<body class="flex h-screen bg-black text-white overflow-hidden">

  {{-- الشريط الجانبي --}}
  <aside class="w-64 bg-black flex-shrink-0 flex flex-col border-r border-gray-800">
    <div class="p-6 text-2xl font-bold text-orange-400">Eqra Tech</div>
    <nav class="flex-1 px-2 space-y-1">
      <a href="{{ route('admin.dashboard') }}"
         class="group flex items-center px-3 py-2 rounded-md text-sm font-medium
           {{ request()->routeIs('admin.dashboard')
              ? 'bg-orange-700 text-white'
              : 'text-orange-300 hover:bg-orange-600 hover:text-white' }}">
        <svg class="h-5 w-5 mr-2" fill="currentColor"><!-- icon --></svg>
        Dashboard
      </a>
      <a href="{{ route('admin.users') }}"
         class="group flex items-center px-3 py-2 rounded-md text-sm font-medium
           {{ request()->routeIs('admin.users')
              ? 'bg-orange-700 text-white'
              : 'text-orange-300 hover:bg-orange-600 hover:text-white' }}">
        <svg class="h-5 w-5 mr-2" fill="currentColor"><!-- icon --></svg>
        Users
      </a>
    </nav>
    <div class="p-4">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
                class="w-full px-3 py-2 bg-orange-600 hover:bg-orange-500 rounded-md text-sm font-medium">
          Logout
        </button>
      </form>
    </div>
  </aside>

  {{-- المحتوى الرئيسي --}}
  <div class="flex-1 flex flex-col overflow-auto">
    <header class="px-6 py-4 bg-black border-b border-gray-800 flex justify-between items-center">
      <h1 class="text-2xl font-semibold text-orange-300">@yield('title')</h1>
      
      {{-- هنا تستدعي مكوّن الـ Livewire --}}
      <livewire:admin-menu />
    </header>

    <main class="flex-1 p-6 overflow-auto bg-black flex justify-center items-start">
      <div class="w-full max-w-6xl">
        @yield('content')
      </div>
    </main>
  </div>

  {{-- Livewire Scripts --}}
  @livewireScripts
</body>
</html>

{{-- resources/views/livewire/admin-menu.blade.php --}}
<div class="dropdown dropdown-end">
  {{-- زرّ الصورة كـ toggle --}}
  <label tabindex="0"
         class="btn btn-ghost btn-circle avatar ring-2 ring-orange-500 hover:ring-orange-400 transition">
    <div class="w-10 rounded-full overflow-hidden">
      <img
        src="{{ Auth::user()->avatar_url ?? asset('images/Admin.png') }}"
        alt="avatar"
        class="object-cover w-full h-full"
      />
    </div>
  </label>

  {{-- المحتوى المنسدِل --}}
  <ul tabindex="0"
      class="dropdown-content menu p-2 shadow bg-neutral-800 rounded-box mt-2 w-52 text-orange-300">
    <li class="px-4 py-2 border-b border-neutral-700">
      <span class="font-semibold text-orange-400">{{ strtoupper(Auth::user()->name) }}</span>
      <span class="block text-sm text-neutral-400">{{ Auth::user()->email }}</span>
    </li>
    {{-- أزلنا هنا روابط Profile و Logout --}}
  </ul>
</div>

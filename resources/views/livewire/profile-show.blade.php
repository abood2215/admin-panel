<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#181c22] min-h-screen flex flex-col">

{{-- resources/views/livewire/navbar.blade.php --}}
@include('layouts.navbar')
    @include('livewire.chat-widget')

    <div class="flex flex-1 flex-col items-center justify-center">
        <div class="bg-[#232833] shadow-xl rounded-2xl p-8 w-full max-w-md border border-[#203155]">
            <div class="flex flex-col items-center">
                <div class="relative">
                    <img src="{{ Auth::user()->avatar ?? Auth::user()->picture ?? asset('images/profile.png') }}"
                        alt="Profile"
                        class="w-32 h-32 rounded-full ring-4 ring-[#3881ff] shadow mb-2 object-cover border-4 border-[#181c22] bg-gray-100" />
                    <span class="absolute bottom-3 right-3 block w-4 h-4 bg-green-400 border-2 border-[#232833] rounded-full"></span>
                </div>
                <h2 class="text-2xl font-extrabold text-[#3881ff] mt-2 mb-1 uppercase tracking-wide break-words text-center">
                    {{ Auth::user()->name }}
                </h2>
                <p class="text-[#b8c4d7] mb-5 text-center text-sm">{{ Auth::user()->email }}</p>
            </div>
            <div class="flex gap-3 justify-center mt-6">
                <a href="{{ route('home') }}" class="flex-1 btn bg-[#1e2632] hover:bg-[#263145] text-[#81a1c1] border border-[#293448] flex items-center justify-center gap-1 rounded-lg font-semibold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back
                </a>
                <a href="{{ route('profile.edit') }}" class="flex-1 btn bg-[#3881ff] hover:bg-[#2264b3] text-white font-bold flex items-center justify-center gap-1 rounded-lg shadow">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 112.828 2.828L11.828 15.828a2 2 0 01-2.828 0L5 11l4-4z"/>
                    </svg>
                    Edit Profile
                </a>
            </div>
        </div>
    </div>
</body>
</html>

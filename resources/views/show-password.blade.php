<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-base-200 min-h-screen flex flex-col">

    @include('livewire.navbar')
    @include('livewire.chat-widget')

    <div class="flex flex-1 flex-col items-center justify-center">
        <div class="bg-white/95 shadow-xl rounded-2xl p-8 w-full max-w-md border border-gray-200">
            <div class="flex flex-col items-center">
                <div class="relative">
                    <img src="{{ Auth::user()->avatar ?? Auth::user()->picture ?? asset('images/chatbot.jpg') }}"
                        alt="Profile"
                        class="w-28 h-28 rounded-full ring-4 ring-blue-400 shadow mb-2 object-cover border border-white bg-gray-100" />
                    <span class="absolute bottom-2 right-2 block w-4 h-4 bg-green-400 border-2 border-white rounded-full"></span>
                </div>

                <h2 class="text-2xl font-bold text-[#154287] mt-2 mb-1 uppercase tracking-wide">{{ Auth::user()->name }}</h2>
                <p class="text-gray-500 mb-5">{{ Auth::user()->email }}</p>
            </div>

            <div class="flex gap-3 justify-center mt-6">
                <!-- زر العودة -->
                <a href="{{ route('home') }}" class="flex-1 btn bg-gray-100 hover:bg-gray-200 text-blue-700 font-bold flex items-center justify-center gap-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back
                </a>
                <!-- زر التعديل -->
                <a href="{{ route('profile.edit') }}" class="flex-1 btn bg-blue-600 hover:bg-blue-700 text-white font-bold flex items-center justify-center gap-1 rounded-lg">
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

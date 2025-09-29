<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#181c22] min-h-screen flex flex-col">

@include('layouts.navbar')
    @include('livewire.chat-widget')

    <div class="flex flex-1 flex-col items-center justify-center relative z-20">
        <div class="bg-[#232833] shadow-xl rounded-2xl p-8 w-full max-w-md border border-gray-800 relative z-20">
            <h2 class="text-2xl font-bold text-[#3881ff] mb-6 text-center">Edit Profile</h2>

            {{-- عرض رسالة النجاح --}}
            @if(session('success'))
                <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-800 shadow text-center">
                    {{ session('success') }}
                </div>
            @endif

            {{-- عرض رسائل الأخطاء --}}
            @if($errors->any())
                <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-800 shadow text-center">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf

                {{-- حقل الاسم --}}
                <div class="mb-4 text-left">
                    <label class="block text-sm font-medium text-[#8ca0bc] mb-1">Name</label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', Auth::user()->name) }}"
                        required
                        class="input w-full rounded-lg bg-[#181c22] text-white border border-[#274173] placeholder-gray-400 focus:ring-2 focus:ring-[#3881ff] focus:border-[#3881ff]"
                    >
                </div>

                {{-- حقل الإيميل للقراءة فقط --}}
                <div class="mb-4 text-left">
                    <label class="block text-sm font-medium text-[#8ca0bc] mb-1">Email</label>
                    <input
                        type="email"
                        name="email"
                        value="{{ Auth::user()->email }}"
                        readonly
                        class="input w-full rounded-lg bg-[#181c22] text-gray-400 border border-[#232c3b] placeholder-gray-500"
                    >
                </div>

                <div class="flex gap-3 mt-6">
                    <a
                        href="{{ route('profile.show') }}"
                        class="flex-1 btn bg-[#1e2632] hover:bg-[#263145] text-[#81a1c1] border border-[#293448]"
                    >
                        Cancel
                    </a>
                    <button
                        type="submit"
                        class="flex-1 btn bg-[#3881ff] hover:bg-[#2264b3] text-white font-bold shadow"
                    >
                        Save Changes
                    </button>
                </div>
            </form>

            {{-- رابط تغيير كلمة المرور --}}
            <div class="mt-4 text-center">
                <a
                    href="{{ route('password.edit') }}"
                    class="text-[#3881ff] font-bold underline hover:text-[#2264b3]"
                >
                    Change Password
                </a>
            </div>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#181c22] min-h-screen flex flex-col">

    @include('livewire.navbar')
    {{-- إذا كانت الدردشة تعترض، يمكنك مؤقتًا تعطيلها أو رفع z-index للنموذج --}}
    @include('livewire.chat-widget')

    <div class="flex flex-1 items-center justify-center relative z-30">
        <div class="bg-[#232833] shadow-xl rounded-2xl p-8 w-full max-w-md border border-gray-800 relative z-30">
            <h2 class="text-2xl font-bold text-[#3881ff] mb-6 text-center">Change Password</h2>

            {{-- عرض رسائل الخطأ والنجاح --}}
            @if(session('error'))
                <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-800 text-center">
                    {{ session('error') }}
                </div>
            @endif
            @if(session('success'))
                <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-800 text-center">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-800">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-[#8ca0bc] mb-1">Current Password</label>
                    <input type="password"
                           name="current_password"
                           required
                           class="w-full rounded-lg bg-[#181c22] text-white border border-[#274173] p-2 focus:ring-2 focus:ring-[#3881ff]"/>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-[#8ca0bc] mb-1">New Password</label>
                    <input type="password"
                           name="new_password"
                           required
                           class="w-full rounded-lg bg-[#181c22] text-white border border-[#274173] p-2 focus:ring-2 focus:ring-[#3881ff]"/>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-[#8ca0bc] mb-1">Confirm New Password</label>
                    <input type="password"
                           name="new_password_confirmation"
                           required
                           class="w-full rounded-lg bg-[#181c22] text-white border border-[#274173] p-2 focus:ring-2 focus:ring-[#3881ff]"/>
                </div>

                <div class="flex gap-3 mt-6">
                    <a href="{{ route('profile.edit') }}"
                       class="flex-1 py-2 rounded-lg border border-[#293448] text-[#81a1c1] bg-[#1e2632] hover:bg-[#263145] text-center">
                        Cancel
                    </a>
                    <button type="submit"
                            class="flex-1 py-2 rounded-lg bg-[#3881ff] hover:bg-[#2264b3] text-white font-bold">
                        Change Password
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>

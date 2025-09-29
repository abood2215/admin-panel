<div class="hero bg-base-200 min-h-screen" dir="rtl">
  <div class="hero-content text-center">
    <div class="max-w-md">
      <h1 class="text-5xl font-bold mb-2">مرحبًا بك</h1>
      <p class="py-4 text-base-content/80">
        ارفع ملفات الـ PDF واجعل النظام يستخرج الأسئلة ويصحّح إجاباتك تلقائيًا.
      </p>

      @auth
        <div class="flex flex-wrap items-center justify-center gap-3">
          <a href="{{ route('documents.index') }}" class="btn btn-primary">مستنداتي</a>
          <a href="{{ route('documents.upload') }}" class="btn btn-ghost">رفع مستند</a>
          @if(auth()->user()?->is_admin)
            <a href="{{ route('admin.dashboard') }}" class="btn btn-warning">لوحة التحكم</a>
          @endif
        </div>
      @else
        <div class="flex flex-wrap items-center justify-center gap-3">
          <a href="{{ route('login') }}" class="btn btn-primary">تسجيل الدخول</a>
          <a href="{{ route('signUp') }}" class="btn btn-ghost">إنشاء حساب</a>
        </div>
      @endauth
    </div>
  </div>
</div>

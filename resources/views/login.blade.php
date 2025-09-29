<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>تسجيل الدخول - منصة النجاح التعليمية</title>
  <!-- إن كنت تستخدم Laravel Mix/Vite يمكنك تفعيل السطر التالي -->
  <!-- @vite(['resources/css/app.css','resources/js/app.js']) -->
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    :root {
      --primary: #4f46e5;
      --primary-dark: #3730a3;
      --secondary: #06b6d4;
      --accent: #8b5cf6;
      --success: #10b981;
      --warning: #f59e0b;
      --error: #ef4444;
      --dark: #0f172a;
      --gray: #64748b;
      --light: #f8fafc;
      --white: #ffffff;
    }

    body {
      font-family: 'Cairo', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      position: relative;
      overflow-x: hidden;
      min-height: 100vh;
    }

    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background:
        radial-gradient(600px at 20% 30%, rgba(120,119,198,.30), transparent 70%),
        radial-gradient(800px at 80% 70%, rgba(255,255,255,.15), transparent 70%),
        radial-gradient(400px at 40% 40%, rgba(139,92,246,.20), transparent 70%);
      z-index: -2;
    }

    .floating-shapes { position: fixed; inset: 0; pointer-events: none; z-index: -1; overflow: hidden; }
    .shape { position: absolute; background: rgba(255,255,255,.10); border-radius: 50%; animation: float 6s ease-in-out infinite; }
    .shape:nth-child(1){ width:80px;height:80px; top:20%; left:10%; animation-delay:0s; }
    .shape:nth-child(2){ width:120px;height:120px; top:60%; right:10%; animation-delay:2s; }
    .shape:nth-child(3){ width:60px;height:60px; top:30%; right:30%; animation-delay:4s; }
    .shape:nth-child(4){ width:100px;height:100px; bottom:20%; left:20%; animation-delay:1s; }
    @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-20px)} }

    .container { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }

    .card { width: 100%; max-width: 800px; background: rgba(255,255,255,.95); backdrop-filter: blur(20px); border-radius: 32px; box-shadow: 0 32px 64px rgba(0,0,0,.20), 0 0 0 1px rgba(255,255,255,.30); overflow: hidden; position: relative; animation: slideUp .6s ease-out; }
    @keyframes slideUp { from { opacity:0; transform: translateY(30px); } to { opacity:1; transform: translateY(0); } }

    .card-header { background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%); padding: 40px 32px; text-align: center; position: relative; overflow: hidden; }
    .card-header::before { content:''; position:absolute; top:-50%; left:-50%; width:200%; height:200%; background: radial-gradient(circle, rgba(255,255,255,.1) 0%, transparent 70%); animation: rotate 20s linear infinite; }
    @keyframes rotate { from{transform:rotate(0)} to{transform:rotate(360deg)} }

    .brand { display:flex; align-items:center; justify-content:center; gap:16px; margin-bottom:16px; position:relative; z-index:1; }
    .brand-icon { width:64px; height:64px; background: rgba(255,255,255,.20); border-radius:20px; display:flex; align-items:center; justify-content:center; font-size:28px; backdrop-filter: blur(10px); }
    .brand-text { color:#fff; font-size:32px; font-weight:800; letter-spacing:-.5px; }
    .card-subtitle { color: rgba(255,255,255,.9); font-size:18px; font-weight:400; position:relative; z-index:1; }

    .card-body { padding: 48px 32px; }

    .welcome-banner { background: linear-gradient(135deg, rgba(79,70,229,.08) 0%, rgba(139,92,246,.08) 100%); border: 1px solid rgba(79,70,229,.20); border-radius: 24px; padding: 24px; margin-bottom: 32px; text-align:center; position:relative; overflow:hidden; }
    .welcome-banner::before { content:''; position:absolute; top:0; left:-100%; width:100%; height:100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,.20), transparent); animation: shine 3s infinite; }
    @keyframes shine { 0%{left:-100%} 50%{left:100%} 100%{left:100%} }
    .welcome-title { font-size:24px; font-weight:700; color:var(--dark); margin-bottom:8px; position:relative; }
    .welcome-desc { color:var(--gray); font-size:16px; position:relative; }

    .form { display:flex; flex-direction:column; gap:24px; }
    .form-grid { display:grid; grid-template-columns:1fr; gap:24px; }
    @media (min-width:768px){ .form-grid { grid-template-columns:1fr 1fr; } }

    .form-group { position: relative; }
    .form-label { display:block; font-weight:600; color:var(--dark); margin-bottom:8px; font-size:15px; }

    .input-wrapper { position:relative; display:flex; align-items:center; }
    .input-icon { position:absolute; right:16px; z-index:2; width:20px; height:20px; color:var(--gray); }
    .form-input { width:100%; height:56px; border:2px solid #e2e8f0; border-radius:16px; padding:0 50px 0 16px; font-size:16px; font-family:inherit; background:var(--white); transition: all .3s cubic-bezier(.4,0,.2,1); outline:none; text-align:right; }
    .form-input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(79,70,229,.10); transform: translateY(-1px); }
    .form-input::placeholder { color:#94a3b8; font-weight:400; }

    .password-toggle { position:absolute; left:16px; background:none; border:none; cursor:pointer; color:var(--gray); font-size:20px; z-index:2; width:24px; height:24px; display:flex; align-items:center; justify-content:center; }

    .error { color: var(--error); font-size:14px; margin-top:6px; font-weight:500; }

    .remember-wrapper { display:flex; align-items:center; gap:10px; }
    .remember-wrapper input { width:18px; height:18px; }

    .submit-button { width:100%; height:56px; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color:#fff; border:none; border-radius:16px; font-size:16px; font-weight:700; cursor:pointer; transition: all .3s cubic-bezier(.4,0,.2,1); position:relative; overflow:hidden; margin-top:8px; }
    .submit-button::before { content:''; position:absolute; top:0; left:-100%; width:100%; height:100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,.20), transparent); transition:left .5s; }
    .submit-button:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(79,70,229,.40); }
    .submit-button:hover::before { left:100%; }

    .login-foot { text-align:center; margin-top:24px; color:var(--gray); font-size:15px; }
    .login-foot a { color: var(--primary); text-decoration:none; font-weight:600; }
    .login-foot a:hover { color: var(--primary-dark); }

    .alert { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; padding:12px; border-radius:12px; text-align:center; }
    .success { background:#dcfce7; color:#166534; border:1px solid #86efac; }

    .oauth { display:grid; grid-template-columns: 1fr; gap:12px; max-width:680px; margin: 10px auto 0; }
    .btn-oauth { height:52px; border-radius:999px; border:0; cursor:pointer; font-weight:800; background:#6f52f1; color:#fff; box-shadow:0 12px 26px rgba(82,56,224,.30); display:flex; align-items:center; justify-content:center; gap:10px; }
    .btn-oauth:hover { filter: brightness(1.05); }

    @media (max-width:768px){ .container{padding:10px} .card-body{padding:32px 24px} .brand-text{font-size:24px} .welcome-title{font-size:20px} .form-grid{gap:20px} }
  </style>
</head>
<body>
  <div class="floating-shapes">
    <div class="shape"></div>
    <div class="shape"></div>
    <div class="shape"></div>
    <div class="shape"></div>
  </div>

  <div class="container">
    <div class="card">
      <div class="card-header">
        <div class="brand">
          <div class="brand-icon">🎓</div>
          <div class="brand-text">منصة النجاح</div>
        </div>
        <div class="card-subtitle">منصتك التعليمية المتطورة</div>
      </div>

      <div class="card-body">
        <div class="welcome-banner">
          <div class="welcome-title">مرحبًا بك من جديد ✨</div>
          <div class="welcome-desc">سجّل دخولك واستكمل رحلتك التعليمية بسهولة</div>
        </div>

        <!-- رسائل Laravel إن رغبت -->
        <!--
        @if(session('error'))
          <div class="alert">{{ session('error') }}</div>
        @endif
        @if(session('account_created'))
          <div class="alert success">تم إنشاء الحساب بنجاح! يمكنك تسجيل الدخول الآن.</div>
        @endif
        -->

        <form class="form" id="loginForm" method="POST" action="{{ route('login.perform') }}" novalidate>
          @csrf  {{-- ✅ ضروري لمنع 419 --}}

          <div class="form-grid">
            <div class="form-group">
              <label class="form-label">البريد الإلكتروني</label>
              <div class="input-wrapper">
                <input type="email" class="form-input" name="email" id="email" placeholder="example@email.com" autocomplete="username" required value="{{ old('email') }}">
                <svg class="input-icon" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.89 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
              </div>
              <div class="error" id="emailError"></div>
            </div>

            <div class="form-group">
              <label class="form-label">كلمة المرور</label>
              <div class="input-wrapper">
                <input type="password" class="form-input" name="password" id="password" placeholder="••••••••" autocomplete="current-password" required>
                <button type="button" class="password-toggle" onclick="togglePassword('password')">👁️</button>
                <svg class="input-icon" fill="currentColor" viewBox="0 0 24 24"><path d="M18,8h-1V6c0-2.76-2.24-5-5-5S7,3.24,7,6v2H6c-1.1,0-2,0.9-2,2v10c0,1.1,0.9,2,2,2h12c1.1,0,2-0.9,2-2V10C20,8.9,19.1,8,18,8z M12,17c-1.1,0-2-0.9-2-2s0.9-2,2-2s2,0.9,2,2S13.1,17,12,17z M15.1,8H8.9V6c0-1.71,1.39-3.1,3.1-3.1s3.1,1.39,3.1,3.1V8z"/></svg>
              </div>
              <div class="error" id="passwordError"></div>
            </div>
          </div>

          <div class="remember-wrapper" style="margin-top:-8px">
            <input type="checkbox" id="remember" name="remember" value="1">
            <label for="remember" style="color:var(--gray); font-size:14px">تذكرني على هذا الجهاز</label>
          </div>

          <button type="submit" class="submit-button" id="submitBtn">تسجيل الدخول الآن</button>

          <div class="login-foot">
            ليس لديك حساب؟ <a href="{{ route('signUp') }}" style="color:#6d28d9;font-weight:900">إنشاء حساب</a>
          </div>

          <div class="oauth">
            <a href="{{ route('auth.google') }}" class="btn-oauth" style="text-decoration:none">
              <span>متابعة باستخدام Google</span>
              <svg width="18" height="18" viewBox="0 0 48 48" aria-hidden="true"><path fill="#fff" d="M43.6 20.5H42V20H24v8h11.3A12.9 12.9 0 1 1 24 11a12.7 12.7 0 0 1 8.4 3.1l5.7-5.7A20.9 20.9 0 1 0 44 24c0-1.2-.1-2.3-.4-3.5z"/></svg>
            </a>
            <a href="{{ route('github.login') }}" class="btn-oauth" style="text-decoration:none">
              <span>متابعة باستخدام GitHub</span>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="#fff" aria-hidden="true"><path d="M12 .5a12 12 0 0 0-3.8 23.4c.6.1.8-.2.8-.5v-2c-3.3.7-4-1.4-4-1.4-.6-1.5-1.5-1.9-1.5-1.9-1.2-.8.1-.8.1-.8 1.3.1 2 .9 2 .9 1.2 2 2.9 1.4 3.6 1.1.1-.9.5-1.4.9-1.7-2.6-.3-5.3-1.3-5.3-5.9 0-1.3.5-2.4 1.2-3.3-.1-.3-.5-1.6.1-3.2 0 0 1-.3 3.4 1.3a11.7 11.7 0 0 1 6.2 0c2.5-1.6 3.5-1.3 3.5-1.3.6 1.6.2 2.9.1 3.2.8.9 1.2 2 1.2 3.3 0 4.6-2.8 5.6-5.4 5.9.6.5 1 .4 1 .7v2.5c0 .3.2 .6 .8 .5A12 12 0 0 0 12 .5z"/></svg>
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    function togglePassword(inputId) {
      const input = document.getElementById(inputId);
      const button = input.nextElementSibling; // زر إظهار/إخفاء
      if (!input) return;
      if (input.type === 'password') { input.type = 'text'; button.textContent = '🙈'; }
      else { input.type = 'password'; button.textContent = '👁️'; }
    }

    function validateLogin() {
      let ok = true;
      const email = document.getElementById('email');
      const pass  = document.getElementById('password');
      const emailErr = document.getElementById('emailError');
      const passErr  = document.getElementById('passwordError');
      emailErr.textContent = '';
      passErr.textContent  = '';

      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!email.value || !emailRegex.test(email.value)) {
        emailErr.textContent = 'يرجى إدخال بريد إلكتروني صحيح';
        ok = false;
      }
      if (!pass.value || pass.value.length < 8) {
        passErr.textContent = 'كلمة المرور يجب أن تكون 8 أحرف على الأقل';
        ok = false;
      }
      return ok;
    }

    document.getElementById('loginForm')?.addEventListener('submit', function(e){
      if (!validateLogin()) {
        e.preventDefault();
        return false;
      }
      const btn = document.getElementById('submitBtn');
      if (btn) {
        btn.disabled = true; btn.textContent = 'جاري تسجيل الدخول...';
      }
    });

    document.querySelectorAll('.form-input').forEach(inp => inp.addEventListener('blur', validateLogin));
  </script>
</body>
</html>

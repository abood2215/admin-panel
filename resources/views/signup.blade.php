<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>إنشاء حساب - منصة النجاح التعليمية</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&display=swap');
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
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
    }
    
    body::before {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: 
        radial-gradient(600px at 20% 30%, rgba(120, 119, 198, 0.3), transparent 70%),
        radial-gradient(800px at 80% 70%, rgba(255, 255, 255, 0.15), transparent 70%),
        radial-gradient(400px at 40% 40%, rgba(139, 92, 246, 0.2), transparent 70%);
      z-index: -2;
    }
    
    .floating-shapes {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      pointer-events: none;
      z-index: -1;
      overflow: hidden;
    }
    
    .shape {
      position: absolute;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 50%;
      animation: float 6s ease-in-out infinite;
    }
    
    .shape:nth-child(1) {
      width: 80px;
      height: 80px;
      top: 20%;
      left: 10%;
      animation-delay: 0s;
    }
    
    .shape:nth-child(2) {
      width: 120px;
      height: 120px;
      top: 60%;
      right: 10%;
      animation-delay: 2s;
    }
    
    .shape:nth-child(3) {
      width: 60px;
      height: 60px;
      top: 30%;
      right: 30%;
      animation-delay: 4s;
    }
    
    .shape:nth-child(4) {
      width: 100px;
      height: 100px;
      bottom: 20%;
      left: 20%;
      animation-delay: 1s;
    }
    
    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-20px); }
    }
    
    .container {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    
    .register-card {
      width: 100%;
      max-width: 800px;
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      border-radius: 32px;
      box-shadow: 
        0 32px 64px rgba(0, 0, 0, 0.2),
        0 0 0 1px rgba(255, 255, 255, 0.3);
      overflow: hidden;
      position: relative;
      animation: slideUp 0.6s ease-out;
    }
    
    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .card-header {
      background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
      padding: 40px 32px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    
    .card-header::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
      animation: rotate 20s linear infinite;
    }
    
    @keyframes rotate {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }
    
    .brand {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 16px;
      margin-bottom: 16px;
      position: relative;
      z-index: 1;
    }
    
    .brand-icon {
      width: 64px;
      height: 64px;
      background: rgba(255, 255, 255, 0.2);
      border-radius: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 28px;
      backdrop-filter: blur(10px);
    }
    
    .brand-text {
      color: white;
      font-size: 32px;
      font-weight: 800;
      letter-spacing: -0.5px;
    }
    
    .card-subtitle {
      color: rgba(255, 255, 255, 0.9);
      font-size: 18px;
      font-weight: 400;
      position: relative;
      z-index: 1;
    }
    
    .card-body {
      padding: 48px 32px;
    }
    
    .welcome-banner {
      background: linear-gradient(135deg, rgba(79, 70, 229, 0.08) 0%, rgba(139, 92, 246, 0.08) 100%);
      border: 1px solid rgba(79, 70, 229, 0.2);
      border-radius: 24px;
      padding: 24px;
      margin-bottom: 32px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    
    .welcome-banner::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      animation: shine 3s infinite;
    }
    
    @keyframes shine {
      0% { left: -100%; }
      50% { left: 100%; }
      100% { left: 100%; }
    }
    
    .welcome-title {
      font-size: 24px;
      font-weight: 700;
      color: var(--dark);
      margin-bottom: 8px;
      position: relative;
    }
    
    .welcome-desc {
      color: var(--gray);
      font-size: 16px;
      position: relative;
    }
    
    .form {
      display: flex;
      flex-direction: column;
      gap: 24px;
    }
    
    .form-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 24px;
    }
    
    @media (min-width: 768px) {
      .form-grid {
        grid-template-columns: 1fr 1fr;
      }
    }
    
    .form-group {
      position: relative;
    }
    
    .form-label {
      display: block;
      font-weight: 600;
      color: var(--dark);
      margin-bottom: 8px;
      font-size: 15px;
    }
    
    .input-wrapper {
      position: relative;
      display: flex;
      align-items: center;
    }
    
    .input-icon {
      position: absolute;
      right: 16px;
      z-index: 2;
      width: 20px;
      height: 20px;
      color: var(--gray);
    }
    
    .form-input {
      width: 100%;
      height: 56px;
      border: 2px solid #e2e8f0;
      border-radius: 16px;
      padding: 0 50px 0 16px;
      font-size: 16px;
      font-family: inherit;
      background: var(--white);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      outline: none;
      text-align: right;
    }
    
    .form-input:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
      transform: translateY(-1px);
    }
    
    .form-input::placeholder {
      color: #94a3b8;
      font-weight: 400;
    }
    
    .password-toggle {
      position: absolute;
      left: 16px;
      background: none;
      border: none;
      cursor: pointer;
      color: var(--gray);
      font-size: 20px;
      z-index: 2;
      width: 24px;
      height: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .error {
      color: var(--error);
      font-size: 14px;
      margin-top: 6px;
      font-weight: 500;
    }
    
    .checkbox-wrapper {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      margin: 8px 0;
    }
    
    .checkbox {
      width: 20px;
      height: 20px;
      border: 2px solid #d1d5db;
      border-radius: 6px;
      position: relative;
      cursor: pointer;
      flex-shrink: 0;
      margin-top: 2px;
    }
    
    .checkbox input {
      opacity: 0;
      position: absolute;
      width: 100%;
      height: 100%;
      cursor: pointer;
    }
    
    .checkbox input:checked + .checkmark {
      background: var(--primary);
      border-color: var(--primary);
    }
    
    .checkbox input:checked + .checkmark::after {
      opacity: 1;
      transform: scale(1);
    }
    
    .checkmark {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      border-radius: 4px;
      transition: all 0.2s;
    }
    
    .checkmark::after {
      content: '✓';
      position: absolute;
      color: white;
      font-size: 12px;
      font-weight: bold;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) scale(0);
      opacity: 0;
      transition: all 0.2s;
    }
    
    .checkbox-label {
      color: var(--gray);
      font-size: 14px;
      line-height: 1.5;
      cursor: pointer;
    }
    
    .checkbox-label strong {
      color: var(--primary);
      font-weight: 600;
    }
    
    .submit-button {
      width: 100%;
      height: 56px;
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      color: white;
      border: none;
      border-radius: 16px;
      font-size: 16px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      margin-top: 8px;
    }
    
    .submit-button::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: left 0.5s;
    }
    
    .submit-button:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 20px rgba(79, 70, 229, 0.4);
    }
    
    .submit-button:hover::before {
      left: 100%;
    }
    
    .submit-button:active {
      transform: translateY(0);
    }
    
    .login-link {
      text-align: center;
      margin-top: 24px;
      color: var(--gray);
      font-size: 15px;
    }
    
    .login-link a {
      color: var(--primary);
      text-decoration: none;
      font-weight: 600;
      transition: color 0.3s;
    }
    
    .login-link a:hover {
      color: var(--primary-dark);
    }
    
    .success-modal {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.8);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 1000;
      backdrop-filter: blur(10px);
      animation: modalAppear 0.3s ease-out;
    }
    
    @keyframes modalAppear {
      from {
        opacity: 0;
        backdrop-filter: blur(0px);
      }
      to {
        opacity: 1;
        backdrop-filter: blur(10px);
      }
    }
    
    .modal-content {
      background: white;
      border-radius: 24px;
      padding: 40px;
      text-align: center;
      max-width: 400px;
      width: 90%;
      box-shadow: 0 32px 64px rgba(0, 0, 0, 0.3);
      position: relative;
      animation: modalSlideUp 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    
    @keyframes modalSlideUp {
      from {
        opacity: 0;
        transform: translateY(30px) scale(0.9);
      }
      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }
    
    .success-icon {
      width: 80px;
      height: 80px;
      background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 40px;
      color: white;
      margin: 0 auto 20px;
      animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.05); }
    }
    
    .modal-title {
      font-size: 24px;
      font-weight: 700;
      color: var(--dark);
      margin-bottom: 12px;
    }
    
    .modal-description {
      color: var(--gray);
      font-size: 16px;
      margin-bottom: 24px;
    }
    
    .modal-button {
      background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
      color: white;
      border: none;
      padding: 12px 24px;
      border-radius: 12px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: transform 0.2s;
    }
    
    .modal-button:hover {
      transform: translateY(-2px);
    }

    @media (max-width: 768px) {
      .container {
        padding: 10px;
      }
      
      .card-body {
        padding: 32px 24px;
      }
      
      .brand-text {
        font-size: 24px;
      }
      
      .welcome-title {
        font-size: 20px;
      }
      
      .form-grid {
        gap: 20px;
      }
    }
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
    <div class="register-card">
      <div class="card-header">
        <div class="brand">
          <div class="brand-icon">🎓</div>
          <div class="brand-text">منصة النجاح</div>
        </div>
        <div class="card-subtitle">منصتك التعليمية المتطورة</div>
      </div>

      <div class="card-body">
        <div class="welcome-banner">
          <div class="welcome-title">مرحباً بك في رحلة التعلم ✨</div>
          <div class="welcome-desc">أنشئ حسابك الآن واستمتع بتجربة تعليمية فريدة ومتطورة</div>
        </div>

        <form class="form" id="registerForm">
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label">الاسم الكامل</label>
              <div class="input-wrapper">
                <input type="text" class="form-input" name="name" placeholder="أدخل اسمك الكامل" required>
                <svg class="input-icon" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
              </div>
              <div class="error" id="nameError"></div>
            </div>

            <div class="form-group">
              <label class="form-label">البريد الإلكتروني</label>
              <div class="input-wrapper">
                <input type="email" class="form-input" name="email" placeholder="example@email.com" required>
                <svg class="input-icon" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.89 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                </svg>
              </div>
              <div class="error" id="emailError"></div>
            </div>
          </div>

          <div class="form-grid">
            <div class="form-group">
              <label class="form-label">كلمة المرور</label>
              <div class="input-wrapper">
                <input type="password" class="form-input" name="password" id="password" placeholder="••••••••" required>
                <button type="button" class="password-toggle" onclick="togglePassword('password')">👁️</button>
                <svg class="input-icon" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M18,8h-1V6c0-2.76-2.24-5-5-5S7,3.24,7,6v2H6c-1.1,0-2,0.9-2,2v10c0,1.1,0.9,2,2,2h12c1.1,0,2-0.9,2-2V10C20,8.9,19.1,8,18,8z M12,17c-1.1,0-2-0.9-2-2s0.9-2,2-2s2,0.9,2,2S13.1,17,12,17z M15.1,8H8.9V6c0-1.71,1.39-3.1,3.1-3.1s3.1,1.39,3.1,3.1V8z"/>
                </svg>
              </div>
              <div class="error" id="passwordError"></div>
            </div>

            <div class="form-group">
              <label class="form-label">تأكيد كلمة المرور</label>
              <div class="input-wrapper">
                <input type="password" class="form-input" name="password_confirmation" id="confirmPassword" placeholder="أعد إدخال كلمة المرور" required>
                <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword')">👁️</button>
                <svg class="input-icon" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M18,8h-1V6c0-2.76-2.24-5-5-5S7,3.24,7,6v2H6c-1.1,0-2,0.9-2,2v10c0,1.1,0.9,2,2,2h12c1.1,0,2-0.9,2-2V10C20,8.9,19.1,8,18,8z M12,17c-1.1,0-2-0.9-2-2s0.9-2,2-2s2,0.9,2,2S13.1,17,12,17z M15.1,8H8.9V6c0-1.71,1.39-3.1,3.1-3.1s3.1,1.39,3.1,3.1V8z"/>
                </svg>
              </div>
              <div class="error" id="confirmPasswordError"></div>
            </div>
          </div>

          <div class="checkbox-wrapper">
            <label class="checkbox">
              <input type="checkbox" name="terms" required>
              <span class="checkmark"></span>
            </label>
            <label class="checkbox-label">
              أوافق على <strong>الشروط والأحكام</strong> و<strong>سياسة الخصوصية</strong> الخاصة بالمنصة
            </label>
          </div>
          <div class="error" id="termsError"></div>

          <button type="submit" class="submit-button">
            إنشاء الحساب الآن
          </button>

          <div class="login-link">
            لديك حساب بالفعل؟ <a href="{{ route('login') }}" class="link" style="color:#6d28d9;font-weight:900">تسجيل الدخول</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Success Modal -->
  <div class="success-modal" id="successModal" style="display: none;">
    <div class="modal-content">
      <div class="success-icon">✅</div>
      <div class="modal-title">تم إنشاء الحساب بنجاح!</div>
      <div class="modal-description">
        مرحباً بك في منصة النجاح. سيتم توجيهك الآن إلى صفحة تسجيل الدخول.
  
  </div>

  <script>
    function togglePassword(inputId) {
      const input = document.getElementById(inputId);
      const button = input.nextElementSibling;
      
      if (input.type === 'password') {
        input.type = 'text';
        button.textContent = '🙈';
      } else {
        input.type = 'password';
        button.textContent = '👁️';
      }
    }

    function validateForm() {
      const form = document.getElementById('registerForm');
      const formData = new FormData(form);
      let isValid = true;

      // Clear previous errors
      document.querySelectorAll('.error').forEach(error => error.textContent = '');

      // Validate name
      const name = formData.get('name');
      if (!name || name.trim().length < 2) {
        document.getElementById('nameError').textContent = 'يرجى إدخال اسم صحيح (أكثر من حرفين)';
        isValid = false;
      }

      // Validate email
      const email = formData.get('email');
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!email || !emailRegex.test(email)) {
        document.getElementById('emailError').textContent = 'يرجى إدخال بريد إلكتروني صحيح';
        isValid = false;
      }

      // Validate password
      const password = formData.get('password');
      if (!password || password.length < 8) {
        document.getElementById('passwordError').textContent = 'كلمة المرور يجب أن تكون 8 أحرف على الأقل';
        isValid = false;
      }

      // Validate password confirmation
      const confirmPassword = formData.get('password_confirmation');
      if (password !== confirmPassword) {
        document.getElementById('confirmPasswordError').textContent = 'كلمتا المرور غير متطابقتين';
        isValid = false;
      }

      // Validate terms
      const terms = formData.get('terms');
      if (!terms) {
        document.getElementById('termsError').textContent = 'يجب الموافقة على الشروط والأحكام';
        isValid = false;
      }

      return isValid;
    }

    function showSuccessModal() {
      document.getElementById('successModal').style.display = 'flex';
      document.body.style.overflow = 'hidden';
      
      // Auto redirect after 3 seconds
      setTimeout(() => {
        redirectToLogin();
      }, 3000);
    }

  

    // Form submission
    document.getElementById('registerForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      if (validateForm()) {
        // Simulate registration process
        const submitButton = document.querySelector('.submit-button');
        submitButton.textContent = 'جاري إنشاء الحساب...';
        submitButton.disabled = true;
        
        setTimeout(() => {
          submitButton.textContent = 'إنشاء الحساب الآن';
          submitButton.disabled = false;
          showSuccessModal();
        }, 2000);
      }
    });

    // Real-time validation
    document.querySelectorAll('.form-input').forEach(input => {
      input.addEventListener('blur', validateForm);
    });

    // Login link
  
  </script>
</body>
</html>
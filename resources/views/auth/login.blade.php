<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Puwinter</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #F8FAFC;
        }

        /* LEFT PANEL */
        .left-panel {
            width: 480px;
            flex-shrink: 0;
            background: #0F172A;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            top: -100px; left: -100px;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(37,99,235,0.3) 0%, transparent 70%);
            pointer-events: none;
        }

        .left-panel::after {
            content: '';
            position: absolute;
            bottom: -80px; right: -80px;
            width: 350px; height: 350px;
            background: radial-gradient(circle, rgba(124,58,237,0.2) 0%, transparent 70%);
            pointer-events: none;
        }

        .left-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
            z-index: 1;
        }

        .left-logo-icon {
            width: 42px; height: 42px;
            background: #2563EB;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
        }

        .left-logo-text { color: #fff; font-size: 18px; font-weight: 800; }
        .left-logo-sub  { color: #2563EB; font-size: 11px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; }

        .left-hero {
            position: relative;
            z-index: 1;
        }

        .left-hero h1 {
            font-size: 32px;
            font-weight: 800;
            color: #fff;
            line-height: 1.25;
            margin-bottom: 16px;
        }

        .left-hero h1 span { color: #60A5FA; }

        .left-hero p {
            font-size: 14px;
            color: #94A3B8;
            line-height: 1.7;
            margin-bottom: 32px;
        }

        .stat-pills {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .stat-pill {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 10px;
            padding: 12px 16px;
        }

        .stat-pill-icon {
            width: 36px; height: 36px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
        }

        .stat-pill-val  { font-size: 15px; font-weight: 800; color: #fff; }
        .stat-pill-label{ font-size: 12px; color: #94A3B8; margin-top: 1px; }

        .left-footer {
            font-size: 12px;
            color: #475569;
            position: relative;
            z-index: 1;
        }

        /* RIGHT PANEL */
        .right-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
        }

        .form-card {
            width: 100%;
            max-width: 420px;
        }

        .form-card h2 {
            font-size: 24px;
            font-weight: 800;
            color: #1E293B;
            margin-bottom: 6px;
        }

        .form-card .subtitle {
            font-size: 13.5px;
            color: #64748B;
            margin-bottom: 32px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        .form-group .input-wrap {
            position: relative;
        }

        .form-group .input-wrap i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 11px 14px 11px 40px;
            border: 1.5px solid #E2E8F0;
            border-radius: 8px;
            font-size: 13.5px;
            font-family: inherit;
            color: #1E293B;
            background: #fff;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .form-group input:focus {
            border-color: #2563EB;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }

        .form-group input.is-invalid {
            border-color: #EF4444;
        }

        .invalid-feedback {
            font-size: 12px;
            color: #EF4444;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .form-extras {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
        }

        .checkbox-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #64748B;
            cursor: pointer;
        }

        .checkbox-wrap input[type="checkbox"] {
            width: 16px; height: 16px;
            accent-color: #2563EB;
            cursor: pointer;
        }

        .forgot-link {
            font-size: 13px;
            color: #2563EB;
            font-weight: 600;
            text-decoration: none;
        }

        .forgot-link:hover { text-decoration: underline; }

        .btn-submit {
            width: 100%;
            padding: 13px;
            background: #2563EB;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: background 0.15s, transform 0.1s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 20px;
        }

        .btn-submit:hover { background: #1D4ED8; }
        .btn-submit:active { transform: scale(0.99); }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #CBD5E1;
            font-size: 12px;
            margin-bottom: 20px;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #E2E8F0;
        }

        .register-link {
            text-align: center;
            font-size: 13.5px;
            color: #64748B;
        }

        .register-link a {
            color: #2563EB;
            font-weight: 700;
            text-decoration: none;
        }

        .register-link a:hover { text-decoration: underline; }

        .password-wrap input {
            padding-right: 48px;
        }

        .toggle-password {
            position: absolute;
            right: 9px;
            top: 50%;
            transform: translateY(-50%);
            width: 34px;
            height: 34px;
            border: none;
            border-radius: 7px;
            background: transparent;
            color: #64748B;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: background 0.15s, color 0.15s;
        }

        .toggle-password:hover {
            background: #F1F5F9;
            color: #2563EB;
        }

        .form-group .input-wrap .toggle-password i {
            position: static;
            left: auto;
            top: auto;
            transform: none;
            color: inherit;
            font-size: 14px;
        }

        .alert-error {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            color: #991B1B;
            border-radius: 8px;
            padding: 11px 14px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        @media (max-width: 768px) {
            .left-panel { display: none; }
        }
    </style>
</head>
<body>

{{-- LEFT PANEL --}}
<div class="left-panel">
    <div class="left-logo">
        <img src="{{ asset('images/logo.png') }}" alt="Puwinter"
             style="width:160px; height:auto; object-fit:contain; filter:brightness(0) invert(1);">
    </div>

    <div class="left-hero">
        <h1>Belajar Lebih <span>Cerdas</span>, Lolos Lebih Pasti</h1>
        <p>Platform persiapan UTBK terlengkap dengan kelas online, tryout, dan pembahasan soal bersama tutor terbaik.</p>

        <div class="stat-pills">
            <div class="stat-pill">
                <div class="stat-pill-icon" style="background:rgba(37,99,235,0.2);">
                    <i class="fas fa-users" style="color:#60A5FA;"></i>
                </div>
                <div>
                    <div class="stat-pill-val">24.560+</div>
                    <div class="stat-pill-label">Pejuang UTBK aktif</div>
                </div>
            </div>
            <div class="stat-pill">
                <div class="stat-pill-icon" style="background:rgba(16,185,129,0.2);">
                    <i class="fas fa-file-circle-check" style="color:#34D399;"></i>
                </div>
                <div>
                    <div class="stat-pill-val">12.458+</div>
                    <div class="stat-pill-label">Soal + pembahasan lengkap</div>
                </div>
            </div>
            <div class="stat-pill">
                <div class="stat-pill-icon" style="background:rgba(245,158,11,0.2);">
                    <i class="fas fa-video" style="color:#FBBF24;"></i>
                </div>
                <div>
                    <div class="stat-pill-val">245+</div>
                    <div class="stat-pill-label">Materi & kelas online tersedia</div>
                </div>
            </div>
        </div>
    </div>

    <div class="left-footer">
        © {{ date('Y') }} Puwinter. All rights reserved.
    </div>
</div>

{{-- RIGHT PANEL --}}
<div class="right-panel">
    <div class="form-card">

        <h2>Selamat datang kembali 👋</h2>
        <p class="subtitle">Login Siswa — masuk dan lanjutkan belajar.</p>

        {{-- Session error --}}
        @if(session('status'))
            <div class="alert-error">
                <i class="fas fa-circle-info"></i> {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert-error">
                <i class="fas fa-circle-xmark"></i> {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Email --}}
            <div class="form-group">
                <label>Email</label>
                <div class="input-wrap">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" value="{{ old('email') }}"
                           placeholder="nama@email.com"
                           class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                           required autofocus autocomplete="email">
                </div>
                @error('email')
                    <div class="invalid-feedback"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div>
                @enderror
            </div>

            {{-- Password --}}
            <div class="form-group">
                <label>Password</label>
                <div class="input-wrap password-wrap">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password"
                           id="password-input"
                           placeholder="Masukkan password"
                           class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                           required autocomplete="current-password">
                    <button type="button" class="toggle-password" data-toggle-password="password-input" aria-label="Tampilkan password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                @error('password')
                    <div class="invalid-feedback"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div>
                @enderror
            </div>

            {{-- Remember + Forgot --}}
            <div class="form-extras">
                <label class="checkbox-wrap">
                    <input type="checkbox" name="remember"> Ingat saya
                </label>
                @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-link">Lupa password?</a>
                @endif
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-arrow-right-to-bracket"></i> Masuk
            </button>
        </form>

        <div class="register-link">
            Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
        </div>

    </div>
</div>



<script>
    document.addEventListener('click', function (event) {
        const button = event.target.closest('[data-toggle-password]');
        if (!button) return;

        const input = document.getElementById(button.dataset.togglePassword);
        if (!input) return;

        const willShow = input.type === 'password';
        input.type = willShow ? 'text' : 'password';
        button.setAttribute('aria-label', willShow ? 'Sembunyikan password' : 'Tampilkan password');

        const icon = button.querySelector('i');
        if (icon) {
            icon.classList.toggle('fa-eye', !willShow);
            icon.classList.toggle('fa-eye-slash', willShow);
        }
    });
</script>

</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar — Puwinter</title>
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
            width: 400px;
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
            width: 300px; height: 300px;
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
            font-size: 28px;
            font-weight: 800;
            color: #fff;
            line-height: 1.3;
            margin-bottom: 14px;
        }

        .left-hero h1 span { color: #60A5FA; }

        .left-hero p {
            font-size: 13.5px;
            color: #94A3B8;
            line-height: 1.7;
            margin-bottom: 28px;
        }

        .benefit-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .benefit-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: #CBD5E1;
        }

        .benefit-item i {
            width: 28px; height: 28px;
            background: rgba(37,99,235,0.2);
            border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            color: #60A5FA;
            font-size: 12px;
            flex-shrink: 0;
        }

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
            overflow-y: auto;
        }

        .form-card {
            width: 100%;
            max-width: 480px;
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
            margin-bottom: 28px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        .form-group label .optional {
            font-size: 11px;
            font-weight: 400;
            color: #94A3B8;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap i.input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
            font-size: 13px;
            pointer-events: none;
        }

        .form-group input,
        .form-group select {
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
            appearance: none;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #2563EB;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }

        .form-group input.is-invalid,
        .form-group select.is-invalid {
            border-color: #EF4444;
        }

        .invalid-feedback {
            font-size: 11.5px;
            color: #EF4444;
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .password-strength {
            margin-top: 6px;
            display: flex;
            gap: 4px;
        }

        .strength-bar {
            flex: 1;
            height: 3px;
            background: #E2E8F0;
            border-radius: 99px;
            transition: background 0.2s;
        }

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

        .toggle-password i {
            color: inherit;
            font-size: 14px;
        }

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
            transition: background 0.15s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 8px;
            margin-bottom: 20px;
        }

        .btn-submit:hover { background: #1D4ED8; }

        .terms-note {
            font-size: 12px;
            color: #94A3B8;
            text-align: center;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .terms-note a { color: #2563EB; text-decoration: none; }
        .terms-note a:hover { text-decoration: underline; }

        .login-link {
            text-align: center;
            font-size: 13.5px;
            color: #64748B;
        }

        .login-link a {
            color: #2563EB;
            font-weight: 700;
            text-decoration: none;
        }

        .login-link a:hover { text-decoration: underline; }

        .section-divider {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94A3B8;
            margin: 4px 0 14px;
            padding-bottom: 8px;
            border-bottom: 1px solid #E2E8F0;
        }

        @media (max-width: 768px) {
            .left-panel { display: none; }
            .form-row { grid-template-columns: 1fr; }
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
        <h1>Bergabung dengan <span>24.560+</span> Pejuang UTBK</h1>
        <p>Daftar gratis dan mulai belajar hari ini. Akses materi, tryout, dan kelas online terbaik.</p>

        <div class="benefit-list">
            <div class="benefit-item">
                <i class="fas fa-check"></i>
                Akses gratis ratusan soal & pembahasan
            </div>
            <div class="benefit-item">
                <i class="fas fa-check"></i>
                Tryout UTBK gratis setiap minggu
            </div>
            <div class="benefit-item">
                <i class="fas fa-check"></i>
                Pantau progress belajarmu secara real-time
            </div>
            <div class="benefit-item">
                <i class="fas fa-check"></i>
                Leaderboard & kompetisi antar pelajar
            </div>
            <div class="benefit-item">
                <i class="fas fa-check"></i>
                Upgrade Premium kapan saja, harga terjangkau
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

        <h2>Buat Akun Baru 🚀</h2>
        <p class="subtitle">Isi data diri kamu untuk mulai belajar.</p>

        @if($errors->any())
            <div style="background:#FEF2F2; border:1px solid #FECACA; color:#991B1B; border-radius:8px; padding:11px 14px; font-size:13px; margin-bottom:20px; display:flex; align-items:center; gap:8px;">
                <i class="fas fa-circle-xmark"></i> {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            {{-- Informasi Akun --}}
            <div class="section-divider">Informasi Akun</div>

            <div class="form-group">
                <label>Nama Lengkap</label>
                <div class="input-wrap">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" name="name" value="{{ old('name') }}"
                           placeholder="Nama lengkap kamu"
                           class="{{ $errors->has('name') ? 'is-invalid' : '' }}"
                           required autofocus autocomplete="name">
                </div>
                @error('name')
                    <div class="invalid-feedback"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Email</label>
                <div class="input-wrap">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" name="email" value="{{ old('email') }}"
                           placeholder="nama@email.com"
                           class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                           required autocomplete="email">
                </div>
                @error('email')
                    <div class="invalid-feedback"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Nomor HP</label>
                <div class="input-wrap">
                    <i class="fas fa-phone input-icon"></i>
                    <input type="tel" name="phone" value="{{ old('phone') }}"
                           placeholder="Contoh: 081234567890"
                           class="{{ $errors->has('phone') ? 'is-invalid' : '' }}"
                           required autocomplete="tel" inputmode="tel" maxlength="20">
                </div>
                @error('phone')
                    <div class="invalid-feedback"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrap password-wrap">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="password"
                               placeholder="Min. 8 karakter"
                               class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                               required autocomplete="new-password"
                               id="password-input">
                        <button type="button" class="toggle-password" data-toggle-password="password-input" aria-label="Tampilkan password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="invalid-feedback"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Konfirmasi Password</label>
                    <div class="input-wrap password-wrap">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="password_confirmation"
                               id="password-confirmation-input"
                               placeholder="Ulangi password"
                               required autocomplete="new-password">
                        <button type="button" class="toggle-password" data-toggle-password="password-confirmation-input" aria-label="Tampilkan password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Informasi Sekolah --}}
            <div class="section-divider" style="margin-top:8px;">Informasi Sekolah <span style="font-size:10px; font-weight:400; color:#94A3B8;">(opsional)</span></div>

            <div class="form-group">
                <label>Nama Sekolah <span class="optional">(opsional)</span></label>
                <div class="input-wrap">
                    <i class="fas fa-school input-icon"></i>
                    <input type="text" name="school" value="{{ old('school') }}"
                           placeholder="SMA Negeri 1 ...">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Kota <span class="optional">(opsional)</span></label>
                    <div class="input-wrap">
                        <i class="fas fa-location-dot input-icon"></i>
                        <input type="text" name="city" value="{{ old('city') }}"
                               placeholder="Jakarta, Surabaya...">
                    </div>
                </div>

                <div class="form-group">
                    <label>Kelas</label>
                    <div class="input-wrap">
                        <i class="fas fa-graduation-cap input-icon"></i>
                        <select name="grade_id" required class="@error('grade_id') is-invalid @enderror">
                            <option value="">Pilih kelas</option>
                            @foreach($grades as $grade)
                                <option value="{{ $grade->id }}" {{ old('grade_id') == $grade->id ? 'selected' : '' }}>
                                    {{ $grade->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('grade_id')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                    <small class="optional" style="display:block;margin-top:6px;">
                        Kelas hanya dipilih sekali saat daftar. Untuk pindah kelas, ajukan permintaan ke admin.
                    </small>
                </div>
            </div>

            {{-- Kode Pendaftar --}}
            <div class="section-divider" style="margin-top:8px;">Kode Pendaftar <span style="font-size:10px; font-weight:400; color:#94A3B8;">(opsional)</span></div>
            <div class="form-group">
                <label>Kode Pendaftar <span class="optional">(opsional)</span></label>
                <div class="input-wrap">
                    <i class="fas fa-user-group input-icon"></i>
                    <input type="text" name="registration_code" value="{{ old('registration_code') }}"
                           placeholder="Contoh: PWIN-AB12CD34"
                           class="{{ $errors->has('registration_code') ? 'is-invalid' : '' }}"
                           style="text-transform:uppercase;">
                </div>
                @error('registration_code')
                    <div class="invalid-feedback"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div>
                @enderror
                <small class="optional" style="display:block;margin-top:6px;line-height:1.5;">
                    Isi jika kamu mendapat kode khusus dari sekolah, kelas, komunitas, atau panitia.
                </small>
            </div>


            {{-- Affiliate --}}
            <div class="section-divider" style="margin-top:8px;">Affiliate <span style="font-size:10px; font-weight:400; color:#94A3B8;">(opsional)</span></div>
            <div class="form-group">
                <label>Kode Affiliate <span class="optional">(opsional)</span></label>
                <div class="input-wrap">
                    <i class="fas fa-ticket input-icon"></i>
                    <input type="text" name="affiliate_code" value="{{ old('affiliate_code') }}"
                           placeholder="Masukkan kode teman jika ada"
                           class="{{ $errors->has('affiliate_code') ? 'is-invalid' : '' }}"
                           style="text-transform:uppercase;">
                </div>
                @error('affiliate_code')
                    <div class="invalid-feedback"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div>
                @enderror
                <small class="optional" style="display:block;margin-top:6px;line-height:1.5;">
                    Kode affiliate dipakai untuk mencatat siapa yang mengajak kamu. Benefit/reward akan diberikan kepada pemilik kode sesuai pengaturan admin.
                </small>
            </div>

            <p class="terms-note">
                Dengan mendaftar, kamu menyetujui <a href="#">Syarat & Ketentuan</a> dan <a href="#">Kebijakan Privasi</a> Puwinter.
            </p>

            <button type="submit" class="btn-submit">
                <i class="fas fa-user-plus"></i> Buat Akun Sekarang
            </button>

        </form>

        <div class="login-link">
            Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
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

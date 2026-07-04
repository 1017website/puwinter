<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email — Puwinter</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css'])
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at top, #EAF1FF 0, #F8FAFC 42%, #FFFFFF 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            background: #fff;
            border: 1px solid #E2E8F0;
            border-radius: 24px;
            padding: 42px 38px;
            max-width: 460px;
            width: 100%;
            text-align: center;
            box-shadow: 0 22px 60px rgba(15, 23, 42, 0.09);
        }
        .brand-logo-wrap {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0F172A, #1E3A8A);
            border-radius: 999px;
            padding: 10px 18px;
            margin: 0 auto 26px;
            border: 1px solid rgba(37, 99, 235, 0.24);
            box-shadow: 0 14px 32px rgba(15, 23, 42, 0.14);
        }
        .logo { width: 152px; height: auto; display: block; }
        .icon-wrap {
            width: 70px;
            height: 70px;
            background: #EFF6FF;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            border: 1px solid #DBEAFE;
        }
        h1 { font-size: 24px; font-weight: 800; color: #0F172A; margin-bottom: 10px; }
        p { font-size: 14px; color: #64748B; line-height: 1.65; margin-bottom: 22px; }
        .email-box {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 999px;
            padding: 8px 14px;
            color: #334155;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 18px;
            max-width: 100%;
            word-break: break-word;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 13px 24px;
            background: #2563EB;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 800;
            font-family: inherit;
            cursor: pointer;
            text-decoration: none;
            width: 100%;
            justify-content: center;
            box-shadow: 0 10px 24px rgba(37, 99, 235, 0.24);
        }
        .btn-outline {
            background: #FFFFFF;
            border: 1.5px solid #E2E8F0;
            color: #64748B;
            margin-top: 10px;
            box-shadow: none;
        }
        .alert {
            padding: 12px 15px;
            border-radius: 12px;
            font-size: 13px;
            line-height: 1.55;
            margin-bottom: 16px;
            text-align: left;
        }
        .alert-success { background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; }
        .alert-error   { background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; }
        .hint { font-size: 12.5px; color: #94A3B8; margin: 16px 0 0; }
    </style>
</head>
<body>
    <div class="card">
        <div class="brand-logo-wrap" aria-label="Puwinter">
            <img src="{{ asset('images/logo.png') }}" alt="Puwinter" class="logo">
        </div>

        <div class="icon-wrap">
            <i class="fas fa-envelope-open-text" style="font-size:29px; color:#2563EB;"></i>
        </div>

        <h1>Verifikasi Email Kamu</h1>
        <p>Kami sudah mengirim link verifikasi ke email berikut:</p>

        <div class="email-box">
            <i class="fas fa-envelope"></i>
            <span>{{ auth()->user()->email }}</span>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @if(session('info'))
            <div class="alert alert-success">{{ session('info') }}</div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn">
                <i class="fas fa-paper-plane"></i> Kirim Ulang Email Verifikasi
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" style="margin-top:10px;">
            @csrf
            <button type="submit" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Keluar
            </button>
        </form>

        <p class="hint">Cek folder Inbox, Updates, Promotions, atau Spam. Setelah email diverifikasi, kamu akan otomatis masuk ke dashboard.</p>
    </div>
</body>
</html>

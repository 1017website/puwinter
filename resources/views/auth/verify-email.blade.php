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
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #F8FAFC; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { background: #fff; border-radius: 16px; padding: 48px 40px; max-width: 420px; width: 100%; text-align: center; box-shadow: 0 4px 24px rgba(0,0,0,0.06); }
        .icon-wrap { width: 72px; height: 72px; background: #EFF6FF; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
        h1 { font-size: 22px; font-weight: 800; color: #1E293B; margin-bottom: 10px; }
        p { font-size: 13.5px; color: #64748B; line-height: 1.6; margin-bottom: 24px; }
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 11px 24px; background: #2563EB; color: #fff; border: none; border-radius: 8px; font-size: 14px; font-weight: 700; font-family: inherit; cursor: pointer; text-decoration: none; width: 100%; justify-content: center; }
        .btn-outline { background: transparent; border: 1.5px solid #E2E8F0; color: #64748B; margin-top: 10px; }
        .alert { padding: 11px 16px; border-radius: 8px; font-size: 13px; margin-bottom: 16px; }
        .alert-success { background: #ECFDF5; color: #065F46; }
        .alert-error   { background: #FEF2F2; color: #991B1B; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-wrap">
            <i class="fas fa-envelope-open" style="font-size:28px; color:#2563EB;"></i>
        </div>

        <h1>Verifikasi Email Kamu</h1>
        <p>Kami sudah mengirim link verifikasi ke <strong>{{ auth()->user()->email }}</strong>. Cek inbox atau folder spam kamu.</p>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
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
    </div>
</body>
</html>

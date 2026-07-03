@extends('layouts.student')

@section('title', 'Pembayaran Berhasil')

@section('content')

<div style="max-width:560px; margin:60px auto; text-align:center;">

    @if($subscription->status === 'active')
    {{-- SUCCESS --}}
    <div style="width:80px; height:80px; background:#ECFDF5; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 24px;">
        <i class="fas fa-check-circle" style="font-size:36px; color:#10B981;"></i>
    </div>

    <h2 style="font-size:26px; font-weight:800; margin-bottom:10px;">Pembayaran Berhasil! 🎉</h2>
    <p style="font-size:14px; color:var(--text-muted); margin-bottom:32px;">
        Selamat! Akun kamu sudah diupgrade ke <strong>Premium</strong>. Mulai nikmati semua fitur sekarang.
    </p>

    <div class="card" style="margin-bottom:24px; text-align:left;">
        <div style="font-size:14px; font-weight:700; margin-bottom:16px;">Detail Langganan</div>

        <div style="display:flex; flex-direction:column; gap:10px;">
            <div style="display:flex; justify-content:space-between; font-size:13.5px;">
                <span style="color:var(--text-muted);">Program</span>
                <strong>{{ $subscription->plan->name }}</strong>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:13.5px;">
                <span style="color:var(--text-muted);">Total Bayar</span>
                <strong>Rp {{ number_format($subscription->total_amount ?? $subscription->amount_paid, 0, ',', '.') }}</strong>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:13.5px;">
                <span style="color:var(--text-muted);">Metode</span>
                <strong>{{ strtoupper(str_replace('_', ' ', $subscription->payment_method ?? '-')) }}</strong>
            </div>
            <div style="height:1px; background:var(--border);"></div>
            <div style="display:flex; justify-content:space-between; font-size:13.5px;">
                <span style="color:var(--text-muted);">Aktif mulai</span>
                <strong>{{ $subscription->started_at?->format('d M Y') }}</strong>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:13.5px;">
                <span style="color:var(--text-muted);">Berlaku hingga</span>
                <strong style="color:#10B981;">{{ $subscription->expired_at?->format('d M Y') }}</strong>
            </div>
        </div>
    </div>

    <a href="{{ route('dashboard') }}" class="btn btn-primary" style="width:100%; justify-content:center; padding:14px;">
        <i class="fas fa-rocket"></i> Mulai Belajar Sekarang
    </a>

    @else
    {{-- PENDING --}}
    <div style="width:80px; height:80px; background:#FEF3C7; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 24px;"
         id="pending-icon">
        <i class="fas fa-clock" style="font-size:36px; color:#F59E0B;"></i>
    </div>

    <h2 style="font-size:26px; font-weight:800; margin-bottom:10px;">Menunggu Konfirmasi...</h2>
    <p style="font-size:14px; color:var(--text-muted); margin-bottom:32px;">
        Pembayaran sedang diproses. Halaman ini akan otomatis update setelah pembayaran dikonfirmasi.
    </p>

    <div class="card" style="margin-bottom:24px;">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:44px; height:44px; border-radius:10px; background:var(--primary-light); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <i class="fas fa-sync-alt fa-spin" style="color:var(--primary);"></i>
            </div>
            <div style="text-align:left;">
                <div style="font-size:13.5px; font-weight:700;">Mengecek status pembayaran...</div>
                <div style="font-size:12px; color:var(--text-muted);" id="check-status-text">Cek otomatis setiap 5 detik</div>
            </div>
        </div>
    </div>

    <div style="display:flex; gap:10px;">
        <a href="{{ route('upgrade.index') }}" class="btn btn-outline" style="flex:1; justify-content:center;">
            Kembali
        </a>
        <a href="{{ route('dashboard') }}" class="btn btn-primary" style="flex:1; justify-content:center;">
            Dashboard
        </a>
    </div>

    <script>
        // Auto-polling cek status pembayaran
        let attempts = 0;
        const maxAttempts = 24; // 2 menit
        const subscriptionId = {{ $subscription->id }};

        function checkPaymentStatus() {
            fetch(`/payment/check-status/${subscriptionId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(r => r.json())
            .then(data => {
                attempts++;

                if (data.status === 'active') {
                    // Pembayaran berhasil - reload halaman
                    document.getElementById('check-status-text').textContent = 'Pembayaran dikonfirmasi! Mengalihkan...';
                    window.location.href = data.redirect_url || window.location.href;
                    return;
                }

                if (attempts >= maxAttempts) {
                    document.getElementById('check-status-text').textContent = 'Timeout. Refresh manual jika sudah bayar.';
                    return;
                }

                document.getElementById('check-status-text').textContent =
                    `Cek otomatis ke-${attempts}/${maxAttempts}...`;

                setTimeout(checkPaymentStatus, 5000);
            })
            .catch(() => {
                if (attempts < maxAttempts) {
                    setTimeout(checkPaymentStatus, 5000);
                }
            });
        }

        // Mulai polling setelah 3 detik
        setTimeout(checkPaymentStatus, 3000);
    </script>
    @endif

</div>

@endsection

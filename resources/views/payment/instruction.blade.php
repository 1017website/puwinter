@extends('layouts.student')

@section('title', 'Instruksi Pembayaran')

@section('content')

@php
    $isPaid     = $subscription->status === 'active';
    $hasProof   = !empty($subscription->payment_proof);
@endphp

<div style="max-width:640px; margin:0 auto;">

    @if(session('success'))
        <div style="background:#ECFDF5; border:1px solid #A7F3D0; color:#065F46; border-radius:10px; padding:14px 18px; margin-bottom:20px; font-size:13.5px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error') || $errors->any())
        <div style="background:#FEF2F2; border:1px solid #FCA5A5; color:#991B1B; border-radius:10px; padding:14px 18px; margin-bottom:20px; font-size:13.5px;">
            <i class="fas fa-circle-exclamation"></i>
            {{ session('error') ?? $errors->first() }}
        </div>
    @endif

    <div style="text-align:center; margin-bottom:24px;">
        <h2 style="font-size:24px; font-weight:800;">Selesaikan Pembayaran</h2>
        <p style="font-size:13.5px; color:var(--text-muted); margin-top:6px;">
            Program <strong>{{ $subscription->plan->name ?? '-' }}</strong> · Transfer Bank Manual
        </p>
    </div>

    {{-- Status badge --}}
    <div style="text-align:center; margin-bottom:24px;">
        @if($isPaid)
            <span style="background:#ECFDF5; color:#059669; font-size:12.5px; font-weight:700; padding:6px 16px; border-radius:20px;">
                <i class="fas fa-check-circle"></i> Pembayaran Terverifikasi
            </span>
        @elseif($hasProof)
            <span style="background:#FFFBEB; color:#B45309; font-size:12.5px; font-weight:700; padding:6px 16px; border-radius:20px;">
                <i class="fas fa-hourglass-half"></i> Menunggu Validasi Admin
            </span>
        @else
            <span style="background:#EFF6FF; color:#2563EB; font-size:12.5px; font-weight:700; padding:6px 16px; border-radius:20px;">
                <i class="fas fa-clock"></i> Menunggu Pembayaran
            </span>
        @endif
    </div>

    {{-- Nominal --}}
    <div class="card" style="margin-bottom:16px; text-align:center;">
        <div style="font-size:12.5px; color:var(--text-muted); margin-bottom:6px;">Total yang harus ditransfer</div>
        <div style="font-size:32px; font-weight:800; color:var(--primary); letter-spacing:-0.5px;">
            Rp {{ number_format($subscription->total_amount ?? $subscription->amount_paid, 0, ',', '.') }}
        </div>
        <div style="font-size:12px; color:var(--text-muted); margin-top:8px; line-height:1.6;">
            Harga program Rp {{ number_format($subscription->amount_paid, 0, ',', '.') }}
            + <strong>kode unik {{ $subscription->unique_code }}</strong>.<br>
            Transfer <u>tepat</u> hingga 3 digit terakhir agar mudah diverifikasi otomatis.
        </div>
    </div>

    {{-- Rekening tujuan --}}
    <div class="card" style="margin-bottom:16px;">
        <div style="font-size:14px; font-weight:700; margin-bottom:14px;">
            <i class="fas fa-building-columns" style="color:var(--primary);"></i> Rekening Tujuan
        </div>
        @if(!empty($bank['bank_account']))
            <div style="display:flex; flex-direction:column; gap:10px;">
                <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 14px; background:var(--primary-light); border-radius:8px;">
                    <div>
                        <div style="font-size:11px; color:var(--text-muted);">Bank</div>
                        <div style="font-size:14px; font-weight:700;">{{ $bank['bank_name'] ?: '-' }}</div>
                    </div>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 14px; background:var(--primary-light); border-radius:8px;">
                    <div>
                        <div style="font-size:11px; color:var(--text-muted);">No. Rekening</div>
                        <div style="font-size:16px; font-weight:800; letter-spacing:1px;" id="rek">{{ $bank['bank_account'] }}</div>
                    </div>
                    <button onclick="copyRek()" type="button"
                            style="background:var(--primary); color:#fff; border:none; padding:8px 14px; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer; font-family:inherit;">
                        <i class="fas fa-copy"></i> Salin
                    </button>
                </div>
                <div style="padding:10px 14px; background:var(--primary-light); border-radius:8px;">
                    <div style="font-size:11px; color:var(--text-muted);">Atas Nama</div>
                    <div style="font-size:14px; font-weight:700;">{{ $bank['bank_holder'] ?: '-' }}</div>
                </div>
            </div>
            @if(!empty($bank['payment_note']))
                <div style="margin-top:14px; font-size:12.5px; color:var(--text-muted); background:#FFFBEB; border:1px solid #FDE68A; border-radius:8px; padding:10px 14px; line-height:1.6;">
                    <i class="fas fa-circle-info"></i> {{ $bank['payment_note'] }}
                </div>
            @endif
        @else
            <div style="font-size:13px; color:var(--text-muted); text-align:center; padding:14px;">
                Informasi rekening belum diatur. Silakan hubungi admin.
            </div>
        @endif
    </div>

    {{-- Upload bukti --}}
    @if(!$isPaid)
    <div class="card">
        <div style="font-size:14px; font-weight:700; margin-bottom:6px;">
            <i class="fas fa-receipt" style="color:var(--primary);"></i> Unggah Bukti Transfer
        </div>
        <p style="font-size:12.5px; color:var(--text-muted); margin-bottom:14px;">
            Setelah transfer, unggah foto/screenshot bukti. Admin akan memvalidasi dan mengaktifkan Premium kamu.
        </p>

        @if($hasProof)
            <div style="margin-bottom:14px;">
                <div style="font-size:12px; color:var(--text-muted); margin-bottom:6px;">Bukti terunggah:</div>
                <img src="{{ asset('uploads/proofs/' . $subscription->payment_proof) }}"
                     alt="Bukti transfer"
                     style="max-width:220px; border-radius:8px; border:1px solid var(--border);">
                <div style="font-size:11.5px; color:var(--text-muted); margin-top:6px;">
                    Diunggah {{ $subscription->proof_uploaded_at?->translatedFormat('d M Y H:i') }} WIB.
                    Kamu masih bisa mengunggah ulang bila perlu.
                </div>
            </div>
        @endif

        <form action="{{ route('upgrade.upload-proof', $subscription->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="proof" accept="image/*" required
                   style="display:block; width:100%; font-size:13px; margin-bottom:14px; padding:10px; border:1.5px dashed var(--border); border-radius:8px; background:#fafafa;">
            <button type="submit"
                    style="width:100%; padding:12px; border:none; background:var(--primary); color:#fff; border-radius:8px; font-size:14px; font-weight:700; font-family:inherit; cursor:pointer;">
                <i class="fas fa-upload"></i> {{ $hasProof ? 'Unggah Ulang Bukti' : 'Unggah Bukti Transfer' }}
            </button>
        </form>
    </div>
    @else
    <div style="text-align:center;">
        <a href="{{ route('dashboard') }}" class="btn btn-primary" style="padding:12px 28px;">
            <i class="fas fa-crown"></i> Mulai Belajar Premium
        </a>
    </div>
    @endif

</div>

@push('scripts')
<script>
function copyRek() {
    const t = document.getElementById('rek').innerText.trim();
    navigator.clipboard.writeText(t).then(() => {
        alert('Nomor rekening disalin: ' + t);
    });
}
</script>
@endpush

@endsection

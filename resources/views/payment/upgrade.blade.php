@extends('layouts.student')

@section('title', 'Upgrade ke Premium')

@section('content')

<div style="max-width:960px; margin:0 auto;">

    @if(session('error'))
        <div style="background:#FEF2F2; border:1px solid #FCA5A5; color:#991B1B; border-radius:10px; padding:14px 18px; margin-bottom:20px; font-size:13.5px;">
            <i class="fas fa-circle-exclamation"></i> {{ session('error') }}
        </div>
    @endif

    @if(!empty($pending))
        <div style="background:#FFFBEB; border:1px solid #FDE68A; border-radius:10px; padding:14px 18px; margin-bottom:20px; display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
            <div style="font-size:13.5px; color:#92400E;">
                <i class="fas fa-hourglass-half"></i>
                Kamu punya pembayaran <strong>{{ $pending->plan->name ?? 'Premium' }}</strong> yang belum selesai.
            </div>
            <a href="{{ route('upgrade.instruction', $pending->id) }}"
               style="background:#D97706; color:#fff; text-decoration:none; padding:8px 16px; border-radius:8px; font-size:12.5px; font-weight:700; white-space:nowrap;">
                Lanjutkan Pembayaran
            </a>
        </div>
    @endif

    <div style="text-align:center; margin-bottom:32px;">
        <h2 style="font-size:26px; font-weight:800;">Upgrade ke Premium</h2>
        <p style="font-size:14px; color:var(--text-muted); margin-top:6px;">Dapatkan akses penuh ke semua fitur premium dan tingkatkan peluangmu lolos UTBK!</p>
    </div>

    {{-- Diskon banner dengan countdown --}}
    <div style="background:linear-gradient(135deg,#1D4ED8,#7C3AED); border-radius:12px; padding:16px 24px; display:flex; align-items:center; justify-content:space-between; margin-bottom:32px; color:#fff;"
         x-data="countdown({{ now()->addDays(2)->timestamp }})">
        <div style="display:flex; align-items:center; gap:10px;">
            <i class="fas fa-tag" style="font-size:18px;"></i>
            <div>
                <div style="font-size:14px; font-weight:700;">Diskon Spesial Terbatas!</div>
                <div style="font-size:12px; opacity:0.8;">Upgrade sekarang dan hemat hingga 50% untuk semua paket.</div>
            </div>
        </div>
        <div style="display:flex; align-items:center; gap:8px;">
            <span style="font-size:12px; opacity:0.8;">Berakhir dalam</span>
            @foreach(['days'=>'Hari','hours'=>'Jam','minutes'=>'Menit','seconds'=>'Detik'] as $key => $label)
            <div style="text-align:center; background:rgba(255,255,255,0.2); border-radius:6px; padding:6px 10px; min-width:48px;">
                <div style="font-size:18px; font-weight:800;" x-text="{{ $key }}">00</div>
                <div style="font-size:9px; opacity:0.8;">{{ $label }}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Pricing cards --}}
    <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:20px; margin-bottom:40px;">
        @foreach($plans as $plan)
        <div style="background:#fff; border-radius:14px; padding:28px 24px; position:relative; border:2px solid {{ $plan->is_popular ? 'var(--primary)' : 'var(--border)' }};">
            @if($plan->is_popular)
            <div style="position:absolute; top:-14px; left:50%; transform:translateX(-50%); background:var(--primary); color:#fff; font-size:11px; font-weight:700; padding:4px 16px; border-radius:20px; white-space:nowrap;">
                PALING POPULER
            </div>
            @endif

            <div style="margin-bottom:16px;">
                <div style="font-size:15px; font-weight:800; color:var(--text-main);">{{ $plan->name }}</div>
                <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">
                    @if($plan->duration_months == 1) Cocok untuk persiapan intensif jangka pendek.
                    @elseif($plan->duration_months == 6) Persiapan lebih matang, hasil lebih maksimal.
                    @else Persiapan terbaik untuk hasil maksimal.
                    @endif
                </div>
            </div>

            <div style="margin-bottom:16px;">
                <div style="display:flex; align-items:baseline; gap:8px;">
                    <span style="font-size:26px; font-weight:800; color:var(--text-main);">
                        Rp {{ number_format($plan->price, 0, ',', '.') }}
                    </span>
                    <span style="font-size:11px; color:var(--text-muted);">
                        @if($plan->duration_months > 1) / {{ $plan->duration_months }} bulan @else / bulan @endif
                    </span>
                </div>
                <div style="display:flex; align-items:center; gap:8px; margin-top:4px;">
                    <span style="font-size:12px; color:var(--text-muted); text-decoration:line-through;">
                        Rp {{ number_format($plan->original_price, 0, ',', '.') }}
                    </span>
                    <span style="background:#ECFDF5; color:var(--success); font-size:11px; font-weight:700; padding:2px 7px; border-radius:20px;">
                        Hemat {{ $plan->discountPercentage() }}%
                    </span>
                </div>
            </div>

            <ul style="list-style:none; margin-bottom:20px;">
                @foreach($plan->features ?? [] as $feature)
                <li style="display:flex; align-items:center; gap:8px; font-size:13px; padding:5px 0; color:var(--text-main);">
                    <i class="fas fa-check-circle" style="color:var(--success); flex-shrink:0;"></i>
                    {{ $feature }}
                </li>
                @endforeach
            </ul>

            @if($plan->bonus)
            <div style="background:var(--primary-light); border-radius:8px; padding:10px; display:flex; align-items:center; gap:8px; font-size:12px; color:var(--primary); font-weight:600; margin-bottom:16px;">
                <i class="fas fa-gift"></i> Bonus: {{ $plan->bonus }}
            </div>
            @endif

            <form action="{{ route('upgrade.checkout', $plan->slug) }}" method="POST">
                @csrf
                <button type="submit"
                    style="width:100%; padding:12px; border:{{ $plan->is_popular ? 'none' : '1.5px solid var(--primary)' }}; background:{{ $plan->is_popular ? 'var(--primary)' : 'transparent' }}; color:{{ $plan->is_popular ? '#fff' : 'var(--primary)' }}; border-radius:8px; font-size:14px; font-weight:700; font-family:inherit; cursor:pointer; transition:all 0.15s;">
                    Pilih {{ $plan->name }}
                </button>
            </form>
        </div>
        @endforeach
    </div>

    {{-- Fitur premium --}}
    <div class="card" style="margin-bottom:24px;">
        <div style="font-size:16px; font-weight:700; margin-bottom:20px;">Semua fitur Premium untukmu</div>
        <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:16px;">
            @foreach([
                ['icon'=>'fa-graduation-cap','title'=>'Akses Semua Kelas','desc'=>'Akses seluruh kelas premium tanpa batasan setiap minggu.'],
                ['icon'=>'fa-video','title'=>'Live Class Eksklusif','desc'=>'Ikuti live class bersama tutor terbaik kapan saja.'],
                ['icon'=>'fa-bullseye','title'=>'Tryout Tanpa Batas','desc'=>'Kerjakan tryout sebanyak mungkin kapan saja.'],
                ['icon'=>'fa-book','title'=>'Materi Premium','desc'=>'Materi lengkap, ringkas, dan selalu diupdate.'],
                ['icon'=>'fa-play-circle','title'=>'Pembahasan Video Tutor','desc'=>'Pembahasan mendalam oleh tutor berpengalaman.'],
                ['icon'=>'fa-chart-line','title'=>'Analisis Belajar Detail','desc'=>'Laporan progres dan rekomendasi belajar personal.'],
                ['icon'=>'fa-ban','title'=>'Tanpa Iklan','desc'=>'Belajar lebih fokus tanpa gangguan iklan.'],
                ['icon'=>'fa-headset','title'=>'Prioritas Support','desc'=>'Dapatkan bantuan lebih cepat dari tim support.'],
            ] as $f)
            <div style="display:flex; gap:12px; align-items:flex-start;">
                <div style="width:38px; height:38px; background:var(--primary-light); border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fas {{ $f['icon'] }}" style="color:var(--primary);"></i>
                </div>
                <div>
                    <div style="font-size:13px; font-weight:700;">{{ $f['title'] }}</div>
                    <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">{{ $f['desc'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Garansi --}}
    <div style="background:#EFF6FF; border:1px solid #BFDBFE; border-radius:12px; padding:16px 20px; display:flex; align-items:center; gap:14px; margin-bottom:24px;">
        <i class="fas fa-shield-halved" style="font-size:28px; color:var(--primary);"></i>
        <div>
            <div style="font-size:14px; font-weight:700; color:var(--text-main);">Garansi 7 Hari Uang Kembali</div>
            <div style="font-size:12.5px; color:var(--text-muted); margin-top:2px;">Jika kamu merasa tidak puas dengan layanan kami, ajukan refund dalam 7 hari setelah pembelian dan uangmu akan kami kembalikan 100%.</div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function countdown(endTimestamp) {
    return {
        days: '00', hours: '00', minutes: '00', seconds: '00',
        init() {
            setInterval(() => {
                const diff = endTimestamp - Math.floor(Date.now() / 1000);
                if (diff <= 0) return;
                this.days    = String(Math.floor(diff / 86400)).padStart(2, '0');
                this.hours   = String(Math.floor((diff % 86400) / 3600)).padStart(2, '0');
                this.minutes = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
                this.seconds = String(diff % 60).padStart(2, '0');
            }, 1000);
        }
    }
}
</script>
@endpush

@endsection

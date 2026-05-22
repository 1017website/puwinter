@extends('layouts.student')

@section('title', $liveClass->title)
@php $subtitle = 'Detail Live Class'; @endphp

@section('content')

{{-- Breadcrumb --}}
<div style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--text-muted); margin-bottom:20px;">
    <a href="{{ route('student.live.index') }}" style="color:var(--primary); text-decoration:none; font-weight:600;">Live Class</a>
    <i class="fas fa-chevron-right" style="font-size:10px;"></i>
    <span style="color:var(--text-main); font-weight:600;">{{ $liveClass->title }}</span>
</div>

<div style="display:grid; grid-template-columns:1fr 300px; gap:20px; align-items:flex-start;">

    {{-- ===== KONTEN UTAMA ===== --}}
    <div>

        {{-- === STATUS: LIVE === --}}
        @if($liveClass->isLive())
            @if($liveClass->zoom_link)
                <div class="card" style="margin-bottom:20px; background:linear-gradient(135deg,#7F1D1D,#DC2626); border:none; text-align:center; padding:40px;">
                    <div style="display:inline-flex; align-items:center; gap:8px; background:rgba(255,255,255,0.15); padding:6px 16px; border-radius:20px; margin-bottom:16px;">
                        <div style="width:8px; height:8px; background:#FCA5A5; border-radius:50%; animation:pulse 1.5s infinite;"></div>
                        <span style="color:#fff; font-size:13px; font-weight:700;">Sedang Live Sekarang</span>
                    </div>
                    <h2 style="color:#fff; font-size:20px; font-weight:800; margin-bottom:8px;">{{ $liveClass->title }}</h2>
                    <p style="color:rgba(255,255,255,0.75); font-size:13px; margin-bottom:24px;">Kelas sedang berlangsung. Segera bergabung!</p>
                    <a href="{{ $liveClass->zoom_link }}" target="_blank"
                       style="display:inline-flex; align-items:center; gap:8px; padding:12px 28px; background:#fff; color:#DC2626; border-radius:10px; font-size:14px; font-weight:800; text-decoration:none;">
                        <i class="fas fa-video"></i> Bergabung ke Zoom
                    </a>
                </div>
            @else
                <div class="card" style="margin-bottom:20px; text-align:center; padding:40px; border:2px solid #FCA5A5;">
                    <i class="fas fa-video" style="font-size:40px; color:#EF4444; opacity:0.5; display:block; margin-bottom:12px;"></i>
                    <p style="font-weight:700; font-size:15px;">Kelas sedang live</p>
                    <p style="color:var(--text-muted); font-size:13px; margin-top:4px;">Link Zoom belum tersedia. Hubungi admin.</p>
                </div>
            @endif

        {{-- === STATUS: REKAMAN === --}}
        @elseif($liveClass->status === 'ended' && $liveClass->recording_url)
            @php
                $isYoutube = preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $liveClass->recording_url, $ytMatch);
                $embedUrl  = $isYoutube
                    ? 'https://www.youtube.com/embed/' . $ytMatch[1] . '?rel=0&modestbranding=1'
                    : null;
                $isDirectVideo = !$isYoutube && preg_match('/\.(mp4|webm|ogg)(\?.*)?$/i', $liveClass->recording_url);
            @endphp

            <div class="card" style="padding:0; overflow:hidden; margin-bottom:20px;">
                <div style="position:relative; padding-bottom:56.25%; height:0; background:#000; border-radius:12px;">
                    @if($isYoutube)
                        <iframe src="{{ $embedUrl }}"
                                style="position:absolute; inset:0; width:100%; height:100%; border:none; border-radius:12px;"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen></iframe>
                    @elseif($isDirectVideo)
                        <video controls controlslist="nodownload"
                               style="position:absolute; inset:0; width:100%; height:100%; border-radius:12px; background:#000;">
                            <source src="{{ $liveClass->recording_url }}" type="video/mp4">
                        </video>
                    @else
                        <div style="position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:12px;">
                            <i class="fas fa-play-circle" style="font-size:48px; color:rgba(255,255,255,0.2);"></i>
                            <a href="{{ $liveClass->recording_url }}" target="_blank" class="btn btn-primary">
                                <i class="fas fa-external-link-alt"></i> Buka Rekaman
                            </a>
                        </div>
                    @endif
                </div>
            </div>

        {{-- === STATUS: TERJADWAL === --}}
        @elseif($liveClass->status === 'scheduled')
            <div class="card" style="margin-bottom:20px; text-align:center; padding:60px 40px;">
                <i class="fas fa-calendar-clock" style="font-size:48px; color:var(--primary); opacity:0.4; display:block; margin-bottom:16px;"></i>
                <p style="font-weight:700; font-size:16px; margin-bottom:6px;">Kelas Belum Dimulai</p>
                <p style="color:var(--text-muted); font-size:13px; margin-bottom:6px;">
                    Jadwal: <strong>{{ $liveClass->formattedSchedule() }}</strong>
                </p>
                <p style="color:var(--text-muted); font-size:13px;">Link Zoom akan tersedia saat kelas dimulai.</p>
            </div>

        {{-- === STATUS: ENDED, TANPA REKAMAN === --}}
        @else
            <div class="card" style="margin-bottom:20px; text-align:center; padding:60px 40px;">
                <i class="fas fa-video-slash" style="font-size:48px; opacity:0.2; display:block; margin-bottom:16px;"></i>
                <p style="font-weight:700; font-size:16px; margin-bottom:6px;">Kelas Telah Selesai</p>
                <p style="color:var(--text-muted); font-size:13px;">Rekaman tidak tersedia untuk sesi ini.</p>
            </div>
        @endif

        {{-- Info Kelas --}}
        <div class="card">
            <h1 style="font-size:18px; font-weight:800; margin-bottom:10px;">{{ $liveClass->title }}</h1>
            @if($liveClass->description)
                <p style="font-size:13.5px; color:var(--text-muted); line-height:1.6; margin-bottom:16px;">{{ $liveClass->description }}</p>
            @endif
            <div style="display:flex; flex-wrap:wrap; gap:16px; font-size:12.5px; color:var(--text-muted); padding-top:14px; border-top:1px solid var(--border);">
                <span><i class="fas fa-user-tie" style="margin-right:5px; color:var(--primary);"></i>{{ $liveClass->mentor->name ?? '-' }}</span>
                <span><i class="fas fa-calendar" style="margin-right:5px; color:var(--primary);"></i>{{ $liveClass->formattedSchedule() }}</span>
                <span><i class="fas fa-hourglass-half" style="margin-right:5px; color:var(--primary);"></i>{{ $liveClass->duration_minutes }} menit</span>
                @if($liveClass->subject)
                    <span><i class="fas fa-book" style="margin-right:5px; color:var(--primary);"></i>{{ $liveClass->subject->name }}</span>
                @endif
                <span><i class="fas fa-users" style="margin-right:5px; color:var(--primary);"></i>{{ $liveClass->total_participants }} peserta</span>
            </div>
        </div>
    </div>

    {{-- ===== SIDEBAR ===== --}}
    <div style="position:sticky; top:80px;">
        <div class="card">
            <div style="font-size:13px; font-weight:700; margin-bottom:14px; padding-bottom:10px; border-bottom:1px solid var(--border);">
                <i class="fas fa-info-circle" style="color:var(--primary); margin-right:6px;"></i>
                Informasi Kelas
            </div>

            {{-- Status badge --}}
            <div style="margin-bottom:14px;">
                @php
                    $statusConfig = match($liveClass->status) {
                        'live'      => ['label' => 'Sedang Live',  'class' => 'badge-danger',   'icon' => 'fa-circle'],
                        'scheduled' => ['label' => 'Terjadwal',    'class' => 'badge-primary',  'icon' => 'fa-calendar'],
                        'ended'     => ['label' => 'Selesai',      'class' => 'badge-success',  'icon' => 'fa-check-circle'],
                        'cancelled' => ['label' => 'Dibatalkan',   'class' => 'badge-warning',  'icon' => 'fa-ban'],
                        default     => ['label' => $liveClass->status, 'class' => 'badge-primary', 'icon' => 'fa-circle'],
                    };
                @endphp
                <span class="badge {{ $statusConfig['class'] }}" style="font-size:12px; padding:4px 10px;">
                    <i class="fas {{ $statusConfig['icon'] }}" style="font-size:10px; margin-right:4px;"></i>
                    {{ $statusConfig['label'] }}
                </span>
                @if($liveClass->is_premium)
                    <span class="badge badge-premium" style="font-size:12px; padding:4px 10px; margin-left:4px;">
                        <i class="fas fa-crown" style="font-size:10px;"></i> Premium
                    </span>
                @endif
            </div>

            <div style="display:flex; flex-direction:column; gap:10px; font-size:13px; color:var(--text-muted);">
                <div style="display:flex; gap:10px; align-items:flex-start;">
                    <i class="fas fa-user-tie" style="color:var(--primary); width:16px; margin-top:1px;"></i>
                    <div>
                        <div style="font-size:11px; margin-bottom:1px;">Mentor</div>
                        <div style="color:var(--text-main); font-weight:600;">{{ $liveClass->mentor->name ?? '-' }}</div>
                    </div>
                </div>
                <div style="display:flex; gap:10px; align-items:flex-start;">
                    <i class="fas fa-calendar" style="color:var(--primary); width:16px; margin-top:1px;"></i>
                    <div>
                        <div style="font-size:11px; margin-bottom:1px;">Jadwal</div>
                        <div style="color:var(--text-main); font-weight:600;">{{ $liveClass->scheduled_at->translatedFormat('d M Y') }}</div>
                        <div style="color:var(--text-muted); font-size:12px;">{{ $liveClass->scheduled_at->format('H:i') }} WIB</div>
                    </div>
                </div>
                <div style="display:flex; gap:10px; align-items:flex-start;">
                    <i class="fas fa-hourglass-half" style="color:var(--primary); width:16px; margin-top:1px;"></i>
                    <div>
                        <div style="font-size:11px; margin-bottom:1px;">Durasi</div>
                        <div style="color:var(--text-main); font-weight:600;">{{ $liveClass->duration_minutes }} menit</div>
                    </div>
                </div>
                @if($liveClass->course)
                    <div style="display:flex; gap:10px; align-items:flex-start;">
                        <i class="fas fa-book-open" style="color:var(--primary); width:16px; margin-top:1px;"></i>
                        <div>
                            <div style="font-size:11px; margin-bottom:1px;">Kelas Terkait</div>
                            <a href="{{ route('student.course.show', $liveClass->course->slug) }}"
                               style="color:var(--primary); font-weight:600; font-size:13px; text-decoration:none;">
                                {{ $liveClass->course->title }}
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            @if($liveClass->is_premium && !auth()->user()->isPremium() && $liveClass->status !== 'live')
                <div style="margin-top:16px; padding-top:14px; border-top:1px solid var(--border);">
                    <a href="{{ route('upgrade.index') }}" class="btn btn-premium" style="width:100%; justify-content:center; font-size:13px;">
                        <i class="fas fa-crown"></i> Upgrade Premium
                    </a>
                </div>
            @endif
        </div>
    </div>

</div>

@endsection

@push('styles')
<style>
@keyframes pulse {
    0%, 100% { opacity:1; }
    50% { opacity:0.4; }
}
</style>
@endpush

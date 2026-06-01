@extends('layouts.student')

@section('title', 'Live Class')
@php $subtitle = 'Ikuti kelas langsung bersama mentor terbaik.'; @endphp

@section('content')

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px;">
    <div>
        <h2 style="font-size:22px; font-weight:800;">Live Class</h2>
        <p style="font-size:13px; color:var(--text-muted); margin-top:2px;">Ikuti kelas langsung bersama mentor terbaik.</p>
    </div>
</div>

{{-- Tab Reguler / Private --}}
<div style="display:flex; gap:8px; margin-bottom:24px; border-bottom:1px solid var(--border); padding-bottom:0;">
    <a href="{{ route('student.live.index', ['tab' => 'regular']) }}"
       style="padding:10px 16px; font-size:13.5px; font-weight:600; text-decoration:none; border-bottom:2px solid {{ $tab === 'regular' ? 'var(--primary)' : 'transparent' }}; color:{{ $tab === 'regular' ? 'var(--primary)' : 'var(--text-muted)' }};">
        <i class="fas fa-chalkboard-user"></i> Reguler
    </a>
    <a href="{{ route('student.live.index', ['tab' => 'private']) }}"
       style="padding:10px 16px; font-size:13.5px; font-weight:600; text-decoration:none; border-bottom:2px solid {{ $tab === 'private' ? '#7C3AED' : 'transparent' }}; color:{{ $tab === 'private' ? '#7C3AED' : 'var(--text-muted)' }};">
        <i class="fas fa-user-lock"></i> Private
        @if($privateCount > 0)
            <span style="background:#7C3AED; color:#fff; font-size:10px; font-weight:700; padding:1px 7px; border-radius:999px; margin-left:4px;">{{ $privateCount }}</span>
        @endif
    </a>
</div>

@if($tab === 'private')
<div style="background:#F5F3FF; border:1px solid #DDD6FE; color:#5B21B6; padding:12px 16px; border-radius:10px; margin-bottom:20px; font-size:12.5px;">
    <i class="fas fa-crown" style="color:#7C3AED;"></i>
    Private class bersifat eksklusif dan memerlukan langganan premium.
</div>
@endif

{{-- ===== LIVE SEKARANG ===== --}}
@if($live->isNotEmpty())
    <div style="margin-bottom:28px;">
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:14px;">
            <div style="width:10px; height:10px; background:#EF4444; border-radius:50%; animation:pulse 1.5s infinite;"></div>
            <h3 style="font-size:15px; font-weight:700; color:#EF4444;">Sedang Live Sekarang</h3>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(340px, 1fr)); gap:16px;">
            @foreach($live as $class)
                <div style="background:linear-gradient(135deg,#7F1D1D,#EF4444); border-radius:14px; padding:20px; color:#fff; position:relative; overflow:hidden;">
                    <div style="position:absolute; top:-20px; right:-20px; width:100px; height:100px; background:rgba(255,255,255,0.07); border-radius:50%;"></div>
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:12px;">
                        <span style="background:rgba(255,255,255,0.2); padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700;">
                            <i class="fas fa-circle" style="font-size:8px; color:#FCA5A5;"></i> LIVE
                        </span>
                        @if($class->is_premium)
                            <span style="background:rgba(245,158,11,0.3); color:#FCD34D; padding:3px 8px; border-radius:20px; font-size:10px; font-weight:700;">
                                <i class="fas fa-crown" style="font-size:9px;"></i> Premium
                            </span>
                        @endif
                    </div>
                    <h3 style="font-size:16px; font-weight:800; margin-bottom:6px;">{{ $class->title }}</h3>
                    <div style="font-size:12px; color:rgba(255,255,255,0.75); margin-bottom:4px;">
                        <i class="fas fa-user-tie" style="margin-right:4px;"></i>{{ $class->mentor->name ?? '-' }}
                    </div>
                    <div style="font-size:12px; color:rgba(255,255,255,0.75); margin-bottom:16px;">
                        <i class="fas fa-users" style="margin-right:4px;"></i>{{ $class->total_participants }} peserta
                    </div>
                    @if($class->is_premium && !auth()->user()->isPremium())
                        <a href="{{ route('upgrade.index') }}"
                           style="display:flex; align-items:center; justify-content:center; gap:6px; padding:10px; background:rgba(255,255,255,0.15); color:#fff; border:1px solid rgba(255,255,255,0.3); border-radius:8px; font-size:13px; font-weight:700; text-decoration:none;">
                            <i class="fas fa-crown"></i> Upgrade untuk Bergabung
                        </a>
                    @else
                        <a href="{{ route('student.live.show', $class->id) }}"
                           style="display:flex; align-items:center; justify-content:center; gap:6px; padding:10px; background:#fff; color:#EF4444; border-radius:8px; font-size:13px; font-weight:700; text-decoration:none;">
                            <i class="fas fa-play"></i> Bergabung Sekarang
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endif

{{-- ===== JADWAL MENDATANG ===== --}}
<div style="margin-bottom:28px;">
    <h3 style="font-size:15px; font-weight:700; margin-bottom:14px;">
        <i class="fas fa-calendar-alt" style="color:var(--primary); margin-right:6px;"></i>
        Jadwal Mendatang
    </h3>

    @if($upcoming->isEmpty())
        <div class="card" style="text-align:center; padding:40px; color:var(--text-muted);">
            <i class="fas fa-calendar-xmark" style="font-size:36px; opacity:0.2; display:block; margin-bottom:12px;"></i>
            <p style="font-size:14px; font-weight:600;">Belum ada jadwal live class.</p>
        </div>
    @else
        <div style="display:flex; flex-direction:column; gap:10px;">
            @foreach($upcoming as $class)
                <div class="card" style="display:flex; align-items:center; gap:16px; padding:16px 20px;">

                    {{-- Tanggal block --}}
                    <div style="text-align:center; min-width:52px; background:var(--primary-light); border-radius:10px; padding:8px;">
                        <div style="font-size:20px; font-weight:800; color:var(--primary); line-height:1;">{{ $class->scheduled_at->format('d') }}</div>
                        <div style="font-size:10px; font-weight:700; text-transform:uppercase; color:var(--primary);">{{ $class->scheduled_at->translatedFormat('M') }}</div>
                    </div>

                    {{-- Info --}}
                    <div style="flex:1; min-width:0;">
                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px; flex-wrap:wrap;">
                            <h4 style="font-size:14px; font-weight:700;">{{ $class->title }}</h4>
                            @if($class->isPrivate())
                                <span class="badge" style="background:#F5F3FF; color:#7C3AED; border:1px solid #DDD6FE;"><i class="fas fa-user-lock" style="font-size:9px;"></i> Private</span>
                            @endif
                            @if($class->is_premium)
                                <span class="badge badge-premium"><i class="fas fa-crown" style="font-size:9px;"></i> Premium</span>
                            @else
                                <span class="badge badge-success">Gratis</span>
                            @endif
                        </div>
                        <div style="display:flex; align-items:center; gap:14px; font-size:12px; color:var(--text-muted); flex-wrap:wrap;">
                            <span><i class="fas fa-user-tie" style="margin-right:3px;"></i>{{ $class->mentor->name ?? '-' }}</span>
                            <span><i class="fas fa-clock" style="margin-right:3px;"></i>{{ $class->scheduled_at->format('H:i') }} WIB</span>
                            <span><i class="fas fa-hourglass-half" style="margin-right:3px;"></i>{{ $class->duration_minutes }} menit</span>
                            @if($class->subject)
                                <span><i class="fas fa-book" style="margin-right:3px;"></i>{{ $class->subject->name }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Action --}}
                    @if($class->is_premium && !auth()->user()->isPremium())
                        <a href="{{ route('upgrade.index') }}" class="btn btn-premium" style="flex-shrink:0; font-size:12px; padding:8px 14px;">
                            <i class="fas fa-crown"></i> Upgrade
                        </a>
                    @else
                        <a href="{{ route('student.live.show', $class->id) }}"
                           class="btn btn-outline" style="flex-shrink:0; font-size:12px; padding:8px 14px;">
                            <i class="fas fa-info-circle"></i> Detail
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- ===== REKAMAN (VIDEO TANPA LIVE) ===== --}}
<div>
    <h3 style="font-size:15px; font-weight:700; margin-bottom:14px;">
        <i class="fas fa-film" style="color:#7C3AED; margin-right:6px;"></i>
        Rekaman Live Class
    </h3>

    @if($recordings->isEmpty())
        <div class="card" style="text-align:center; padding:40px; color:var(--text-muted);">
            <i class="fas fa-video-slash" style="font-size:36px; opacity:0.2; display:block; margin-bottom:12px;"></i>
            <p style="font-size:14px; font-weight:600;">Belum ada rekaman tersedia.</p>
        </div>
    @else
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px;">
            @foreach($recordings as $class)
                @php
                    $isLocked   = $class->is_premium && !auth()->user()->isPremium();
                    $isYoutube  = preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $class->recording_url ?? '', $ytMatch);
                    $thumbUrl   = $isYoutube
                        ? 'https://img.youtube.com/vi/' . $ytMatch[1] . '/mqdefault.jpg'
                        : null;
                @endphp

                <div class="card" style="padding:0; overflow:hidden; transition:transform 0.2s, box-shadow 0.2s;"
                     onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 24px rgba(0,0,0,0.1)'"
                     onmouseout="this.style.transform=''; this.style.boxShadow=''">

                    {{-- Thumbnail --}}
                    <div style="position:relative; aspect-ratio:16/9; background:#1E293B; overflow:hidden;">
                        @if($thumbUrl)
                            <img src="{{ $thumbUrl }}" alt="" style="width:100%; height:100%; object-fit:cover;">
                        @else
                            <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center;">
                                <i class="fas fa-play-circle" style="font-size:40px; color:rgba(255,255,255,0.2);"></i>
                            </div>
                        @endif

                        {{-- Overlay badges --}}
                        <div style="position:absolute; top:8px; left:8px; display:flex; gap:6px;">
                            <span style="background:rgba(0,0,0,0.7); color:#fff; font-size:10px; font-weight:700; padding:3px 7px; border-radius:6px; backdrop-filter:blur(4px);">
                                <i class="fas fa-video" style="margin-right:3px; font-size:9px;"></i>Rekaman
                            </span>
                        </div>
                        @if($isLocked)
                            <div style="position:absolute; inset:0; background:rgba(0,0,0,0.55); backdrop-filter:blur(3px); display:flex; align-items:center; justify-content:center;">
                                <div style="text-align:center; color:#fff;">
                                    <i class="fas fa-lock" style="font-size:24px; margin-bottom:6px; display:block;"></i>
                                    <span style="font-size:12px; font-weight:700;">Premium</span>
                                </div>
                            </div>
                        @else
                            <div style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center; opacity:0; transition:opacity 0.2s;"
                                 onmouseover="this.style.opacity='1'; this.style.background='rgba(0,0,0,0.3)'"
                                 onmouseout="this.style.opacity='0'; this.style.background='transparent'">
                                <div style="width:48px; height:48px; background:rgba(255,255,255,0.95); border-radius:50%; display:flex; align-items:center; justify-content:center;">
                                    <i class="fas fa-play" style="color:var(--primary); font-size:18px; margin-left:3px;"></i>
                                </div>
                            </div>
                        @endif

                        @if($class->duration_minutes)
                            <div style="position:absolute; bottom:8px; right:8px; background:rgba(0,0,0,0.75); color:#fff; font-size:10px; font-weight:700; padding:3px 7px; border-radius:6px;">
                                {{ $class->duration_minutes }} mnt
                            </div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div style="padding:14px 16px;">
                        <div style="display:flex; gap:6px; margin-bottom:7px; flex-wrap:wrap;">
                            @if($class->subject)
                                <span class="badge badge-primary" style="font-size:10px;">{{ $class->subject->name }}</span>
                            @endif
                            @if($class->is_premium)
                                <span class="badge badge-premium" style="font-size:10px;"><i class="fas fa-crown" style="font-size:9px;"></i> Premium</span>
                            @endif
                        </div>
                        <h4 style="font-size:13.5px; font-weight:700; margin-bottom:5px; line-height:1.3; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;">
                            {{ $class->title }}
                        </h4>
                        <div style="font-size:11px; color:var(--text-muted); margin-bottom:12px;">
                            <i class="fas fa-user-tie" style="margin-right:3px;"></i>{{ $class->mentor->name ?? '-' }}
                            · {{ $class->scheduled_at->translatedFormat('d M Y') }}
                        </div>

                        @if($isLocked)
                            <a href="{{ route('upgrade.index') }}"
                               style="display:flex; align-items:center; justify-content:center; gap:6px; padding:9px; background:linear-gradient(135deg,#F59E0B,#EF4444); color:#fff; border-radius:8px; font-size:12px; font-weight:700; text-decoration:none;">
                                <i class="fas fa-crown"></i> Upgrade untuk Menonton
                            </a>
                        @else
                            <a href="{{ route('student.live.show', $class->id) }}"
                               style="display:flex; align-items:center; justify-content:center; gap:6px; padding:9px; background:var(--primary); color:#fff; border-radius:8px; font-size:12px; font-weight:700; text-decoration:none; transition:background 0.15s;"
                               onmouseover="this.style.background='#1D4ED8'"
                               onmouseout="this.style.background='var(--primary)'">
                                <i class="fas fa-play"></i> Tonton Rekaman
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@endsection

@push('styles')
<style>
@keyframes pulse {
    0%, 100% { opacity:1; transform:scale(1); }
    50% { opacity:0.6; transform:scale(0.85); }
}
</style>
@endpush

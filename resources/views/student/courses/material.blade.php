@extends('layouts.student')

@section('title', $material->title . ' — ' . $course->title)

@php $subtitle = $course->title; @endphp

@push('styles')
<style>
    /* Video player container */
    .video-wrapper {
        position: relative;
        aspect-ratio: 16 / 9;
        overflow: hidden;
        border-radius: 12px;
        background: #000;
    }
    .video-wrapper iframe,
    .video-wrapper video {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        border: none;
        border-radius: 12px;
        background: #000;
    }
    .youtube-clean-player .youtube-mask-top {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 58px;
        z-index: 5;
        pointer-events: auto;
        background: linear-gradient(180deg, rgba(0,0,0,.78), rgba(0,0,0,.20), rgba(0,0,0,0));
    }
    .youtube-clean-player .youtube-mask-corner {
        position: absolute;
        right: 0;
        bottom: 0;
        width: 190px;
        height: 58px;
        z-index: 5;
        pointer-events: auto;
        background: linear-gradient(270deg, rgba(0,0,0,.88), rgba(0,0,0,.35), rgba(0,0,0,0));
    }
    .youtube-clean-player .youtube-mask-left-corner {
        position: absolute;
        left: 0;
        bottom: 0;
        width: 130px;
        height: 58px;
        z-index: 5;
        pointer-events: auto;
        background: linear-gradient(90deg, rgba(0,0,0,.88), rgba(0,0,0,.35), rgba(0,0,0,0));
    }
    .player-expand-btn {
        position: absolute;
        right: 12px;
        bottom: 12px;
        z-index: 6;
        width: 36px;
        height: 36px;
        border: 0;
        border-radius: 10px;
        background: rgba(15, 23, 42, .88);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 8px 20px rgba(0,0,0,.22);
    }
    .player-expand-btn:hover { background: rgba(37, 99, 235, .95); }
    .video-wrapper:fullscreen {
        width: 100vw;
        height: 100vh;
        aspect-ratio: auto;
        border-radius: 0;
        background: #000;
    }
    .video-wrapper:fullscreen iframe,
    .video-wrapper:fullscreen video { border-radius: 0; }
    .material-sidebar {
        width: 320px;
        flex-shrink: 0;
    }
    .material-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        border-radius: 8px;
        text-decoration: none;
        color: var(--text-main);
        font-size: 13px;
        transition: background 0.1s;
        margin-bottom: 2px;
    }
    .material-item:hover { background: #F1F5F9; }
    .material-item.active { background: var(--primary-light); color: var(--primary); font-weight: 700; }
    .material-item.done .mat-status { background: var(--success); }
    .mat-status {
        width: 20px; height: 20px; border-radius: 50%;
        border: 2px solid var(--border);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; font-size: 10px;
    }
    @media (max-width: 1024px) {
        .material-layout { flex-direction: column !important; }
        .material-sidebar { width: 100% !important; }
    }
</style>
@endpush

@section('content')

{{-- Breadcrumb --}}
<div style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--text-muted); margin-bottom:20px; flex-wrap:wrap;">
    <a href="{{ route('student.course.index') }}" style="color:var(--primary); text-decoration:none; font-weight:600;">Kelas Saya</a>
    <i class="fas fa-chevron-right" style="font-size:10px;"></i>
    <a href="{{ route('student.course.show', $course->slug) }}" style="color:var(--primary); text-decoration:none; font-weight:600;">{{ $course->title }}</a>
    <i class="fas fa-chevron-right" style="font-size:10px;"></i>
    <span style="color:var(--text-main); font-weight:600;">{{ $material->title }}</span>
</div>

<div class="material-layout" style="display:flex; gap:20px; align-items:flex-start;">

    {{-- ===== KONTEN UTAMA ===== --}}
    <div style="flex:1; min-width:0;">

        {{-- === VIDEO === --}}
        @if($material->type === 'video')
            @if($isLocked)
                {{-- Lock overlay --}}
                <div style="border-radius:12px; overflow:hidden; background:#0F172A; aspect-ratio:16/9; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:16px; margin-bottom:20px;">
                    <i class="fas fa-lock" style="font-size:48px; color:rgba(255,255,255,0.2);"></i>
                    <div style="text-align:center;">
                        <p style="color:#fff; font-weight:700; font-size:16px; margin-bottom:6px;">Materi Premium</p>
                        <p style="color:rgba(255,255,255,0.6); font-size:13px; margin-bottom:16px;">Upgrade ke Premium untuk mengakses materi ini.</p>
                        <a href="{{ route('upgrade.index') }}" class="btn btn-premium">
                            <i class="fas fa-crown"></i> Upgrade Sekarang
                        </a>
                    </div>
                </div>
            @elseif($material->content_url)
                @php
                    // Deteksi YouTube URL dan konversi ke embed
                    $videoUrl   = $material->content_url;
                    $isYoutube  = preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $videoUrl, $ytMatch);
                    $embedUrl   = $isYoutube
                        ? 'https://www.youtube-nocookie.com/embed/' . $ytMatch[1]
                          . '?rel=0&modestbranding=1&iv_load_policy=3&fs=0'
                          . '&disablekb=1&color=white&controls=1'
                          . '&playsinline=1&enablejsapi=0'
                        : null;
                    $isDirectVideo = !$isYoutube && preg_match('/\.(mp4|webm|ogg)(\?.*)?$/i', $videoUrl);
                @endphp

                <div class="card" style="padding:0; overflow:hidden; margin-bottom:20px;">
                    @if($isYoutube)
                        <div class="video-wrapper youtube-clean-player" oncontextmenu="return false;">
                            <iframe src="{{ $embedUrl }}"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                            <div class="youtube-mask-top" title="Area YouTube disembunyikan"></div>
                            <div class="youtube-mask-corner" title="Tombol share/buka YouTube disembunyikan"></div>
                            <div class="youtube-mask-left-corner" title="Tombol salin link YouTube disembunyikan"></div>
                            <button type="button" class="player-expand-btn" onclick="puwinterTogglePlayerFullscreen(this)" aria-label="Perbesar video">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    @elseif($isDirectVideo)
                        <div class="video-wrapper">
                            <video controls controlslist="nodownload" style="background:#000;">
                                <source src="{{ $videoUrl }}" type="video/mp4">
                                Browser Anda tidak mendukung pemutaran video.
                            </video>
                        </div>
                    @else
                        {{-- URL video tidak dikenali: tampilkan sebagai link --}}
                        <div style="padding:40px; text-align:center;">
                            <i class="fas fa-play-circle" style="font-size:48px; color:var(--primary); opacity:0.5; margin-bottom:12px; display:block;"></i>
                            <p style="font-size:14px; color:var(--text-muted); margin-bottom:16px;">Video tersedia di tautan eksternal.</p>
                            <a href="{{ $videoUrl }}" target="_blank" class="btn btn-primary">
                                <i class="fas fa-external-link-alt"></i> Buka Video
                            </a>
                        </div>
                    @endif
                </div>
            @else
                <div class="card" style="text-align:center; padding:60px 20px; margin-bottom:20px; color:var(--text-muted);">
                    <i class="fas fa-video-slash" style="font-size:40px; opacity:0.2; display:block; margin-bottom:12px;"></i>
                    <p style="font-size:14px; font-weight:600;">Video belum tersedia</p>
                    <p style="font-size:12px; margin-top:4px;">Mentor belum mengunggah video untuk materi ini.</p>
                </div>
            @endif

        {{-- === PDF === --}}
        @elseif($material->type === 'pdf')
            @if($isLocked)
                <div style="border-radius:12px; background:#FEF2F2; border:1px solid #FCA5A5; padding:40px; text-align:center; margin-bottom:20px;">
                    <i class="fas fa-lock" style="font-size:36px; color:#EF4444; opacity:0.5; display:block; margin-bottom:12px;"></i>
                    <p style="font-size:14px; font-weight:600; color:#991B1B; margin-bottom:6px;">Materi Premium</p>
                    <a href="{{ route('upgrade.index') }}" class="btn btn-premium"><i class="fas fa-crown"></i> Upgrade</a>
                </div>
            @elseif($material->content_url)
                <div class="card" style="padding:0; margin-bottom:20px; overflow:hidden;">
                    <iframe src="{{ $material->content_url }}"
                            style="width:100%; height:600px; border:none;"
                            title="{{ $material->title }}">
                        <p>PDF tidak dapat ditampilkan.
                            <a href="{{ $material->content_url }}" target="_blank">Unduh PDF</a>
                        </p>
                    </iframe>
                </div>
            @else
                <div class="card" style="text-align:center; padding:60px 20px; margin-bottom:20px; color:var(--text-muted);">
                    <i class="fas fa-file-pdf" style="font-size:40px; color:#DC2626; opacity:0.3; display:block; margin-bottom:12px;"></i>
                    <p style="font-size:14px; font-weight:600;">PDF belum tersedia</p>
                </div>
            @endif

        {{-- === QUIZ === --}}
        @elseif($material->type === 'quiz')
            <div class="card" style="text-align:center; padding:60px 20px; margin-bottom:20px;">
                <i class="fas fa-question-circle" style="font-size:48px; color:#7C3AED; opacity:0.4; display:block; margin-bottom:16px;"></i>
                <p style="font-size:15px; font-weight:700; margin-bottom:6px;">Quiz</p>
                <p style="font-size:13px; color:var(--text-muted); margin-bottom:20px;">Uji pemahaman kamu dengan quiz ini.</p>
                {{-- Fitur quiz belum tersedia: tombol disembunyikan sementara --}}
                <span class="btn btn-outline" style="cursor:not-allowed; opacity:0.6;">
                    <i class="fas fa-clock"></i> Quiz segera hadir
                </span>
            </div>

        {{-- === LIVE CLASS === --}}
        @elseif($material->type === 'live_class')
            <div class="card" style="text-align:center; padding:60px 20px; margin-bottom:20px;">
                <i class="fas fa-video" style="font-size:48px; color:#059669; opacity:0.4; display:block; margin-bottom:16px;"></i>
                <p style="font-size:15px; font-weight:700; margin-bottom:6px;">Kelas Online</p>
                <p style="font-size:13px; color:var(--text-muted); margin-bottom:20px;">Materi ini terkait dengan sesi kelas online.</p>
                <a href="{{ route('student.live.index') }}" class="btn btn-primary"><i class="fas fa-external-link-alt"></i> Lihat Jadwal Live</a>
            </div>
        @endif

        {{-- Info Materi --}}
        <div class="card" style="margin-bottom:16px;">
            <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:16px; flex-wrap:wrap;">
                <div style="flex:1; min-width:0;">
                    <h1 style="font-size:18px; font-weight:800; margin-bottom:8px;">{{ $material->title }}</h1>
                    <div style="display:flex; align-items:center; gap:12px; font-size:12px; color:var(--text-muted); flex-wrap:wrap;">
                        @php
                            $typeLabel = match($material->type) {
                                'video' => 'Video', 'pdf' => 'PDF', 'quiz' => 'Quiz', 'live_class' => 'Kelas Online', default => 'Materi'
                            };
                        @endphp
                        <span><i class="fas fa-tag" style="margin-right:4px;"></i>{{ $typeLabel }}</span>
                        @if($material->duration_minutes)
                            <span><i class="fas fa-clock" style="margin-right:4px;"></i>{{ $material->duration_minutes }} menit</span>
                        @endif
                        @if($course->mentor)
                            <span><i class="fas fa-user-tie" style="margin-right:4px;"></i>{{ $course->mentor->name }}</span>
                        @endif
                    </div>
                </div>

                {{-- Tombol tandai selesai --}}
                @if(!$isLocked)
                    @php $isDone = in_array($material->id, $completedMaterialIds); @endphp
                    @if($isDone)
                        <div style="display:flex; align-items:center; gap:6px; padding:8px 16px; background:#ECFDF5; color:var(--success); border-radius:8px; font-size:13px; font-weight:600; flex-shrink:0;">
                            <i class="fas fa-check-circle"></i> Sudah Selesai
                        </div>
                    @else
                        <form method="POST" action="{{ route('student.course.material.complete', $material->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary" style="flex-shrink:0;">
                                <i class="fas fa-check"></i> Tandai Selesai
                            </button>
                        </form>
                    @endif
                @endif
            </div>
        </div>

        {{-- Navigasi Prev / Next --}}
        <div style="display:flex; gap:12px; justify-content:space-between;">
            @if($prevMaterial)
                <a href="{{ route('student.course.material.show', [$course->slug, $prevMaterial->id]) }}"
                   class="btn btn-outline" style="flex:1; justify-content:center;">
                    <i class="fas fa-arrow-left"></i> {{ Str::limit($prevMaterial->title, 30) }}
                </a>
            @else
                <div style="flex:1;"></div>
            @endif

            @if($nextMaterial)
                <a href="{{ route('student.course.material.show', [$course->slug, $nextMaterial->id]) }}"
                   class="btn btn-primary" style="flex:1; justify-content:center;">
                    {{ Str::limit($nextMaterial->title, 30) }} <i class="fas fa-arrow-right"></i>
                </a>
            @else
                <a href="{{ route('student.course.show', $course->slug) }}"
                   class="btn btn-outline" style="flex:1; justify-content:center;">
                    <i class="fas fa-list"></i> Kembali ke Daftar Materi
                </a>
            @endif
        </div>
    </div>

    {{-- ===== SIDEBAR DAFTAR MATERI ===== --}}
    <div class="material-sidebar">
        <div class="card" style="padding:16px; position:sticky; top:80px; max-height:calc(100vh - 100px); overflow-y:auto;">
            <div style="font-size:13px; font-weight:700; margin-bottom:12px; padding-bottom:10px; border-bottom:1px solid var(--border);">
                <i class="fas fa-list-ul" style="color:var(--primary); margin-right:6px;"></i>
                Daftar Materi
            </div>

            {{-- Progress mini --}}
            <div style="margin-bottom:14px;">
                <div style="display:flex; justify-content:space-between; font-size:11px; color:var(--text-muted); margin-bottom:5px;">
                    <span>Progress Kelas</span>
                    <span style="font-weight:700; color:var(--primary);">{{ $progressPercentage }}%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-bar-fill" style="width:{{ $progressPercentage }}%;"></div>
                </div>
            </div>

            @foreach($course->modules as $module)
                <div style="margin-bottom:12px;">
                    <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-muted); padding:4px 0 6px; border-bottom:1px solid var(--border); margin-bottom:4px;">
                        {{ $module->title }}
                    </div>

                    @foreach($module->materials as $mat)
                        @php
                            $matDone   = in_array($mat->id, $completedMaterialIds);
                            $matActive = $mat->id === $material->id;
                            $matLocked = ($mat->is_locked || $mat->is_premium) && !auth()->user()->isPremium();
                            $matIcon   = match($mat->type) {
                                'video'      => 'fa-play-circle',
                                'pdf'        => 'fa-file-pdf',
                                'quiz'       => 'fa-question-circle',
                                'live_class' => 'fa-video',
                                default      => 'fa-file',
                            };
                        @endphp

                        @if($matLocked)
                            <div class="material-item" style="cursor:not-allowed; opacity:0.55;">
                                <div class="mat-status" style="border-color:#E2E8F0;">
                                    <i class="fas fa-lock" style="font-size:9px; color:#94A3B8;"></i>
                                </div>
                                <i class="fas {{ $matIcon }}" style="font-size:12px; color:var(--text-muted); width:16px;"></i>
                                <span style="flex:1; overflow:hidden; white-space:nowrap; text-overflow:ellipsis; font-size:12px; color:var(--text-muted);">{{ $mat->title }}</span>
                            </div>
                        @else
                            <a href="{{ route('student.course.material.show', [$course->slug, $mat->id]) }}"
                               class="material-item {{ $matActive ? 'active' : '' }} {{ $matDone ? 'done' : '' }}">
                                <div class="mat-status" style="{{ $matDone ? 'background:var(--success); border-color:var(--success);' : '' }}">
                                    @if($matDone)
                                        <i class="fas fa-check" style="font-size:9px; color:#fff;"></i>
                                    @endif
                                </div>
                                <i class="fas {{ $matIcon }}" style="font-size:12px; color:{{ $matActive ? 'var(--primary)' : 'var(--text-muted)' }}; width:16px;"></i>
                                <span style="flex:1; overflow:hidden; white-space:nowrap; text-overflow:ellipsis; font-size:12px;">{{ $mat->title }}</span>
                                @if($mat->duration_minutes)
                                    <span style="font-size:10px; color:var(--text-muted); flex-shrink:0;">{{ $mat->duration_minutes }}m</span>
                                @endif
                            </a>
                        @endif
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
    window.puwinterTogglePlayerFullscreen = window.puwinterTogglePlayerFullscreen || function(button) {
        const wrapper = button.closest('.video-wrapper');
        if (!wrapper) return;

        if (!document.fullscreenElement) {
            if (wrapper.requestFullscreen) {
                wrapper.requestFullscreen();
            }
        } else if (document.exitFullscreen) {
            document.exitFullscreen();
        }
    };
</script>
@endpush

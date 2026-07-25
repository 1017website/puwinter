@extends('layouts.student')

@section('title', 'Dashboard')

@section('content')

{{-- ======================================================================= --}}
{{-- HERO: Progress Banner                                                     --}}
{{-- ======================================================================= --}}
<div class="student-dashboard-hero" style="background:linear-gradient(135deg, #1E293B 0%, #1D4ED8 100%); border-radius:16px; padding:28px 32px; display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; overflow:hidden; position:relative;">
    <div style="position:absolute; top:-40px; right:200px; width:200px; height:200px; background:rgba(255,255,255,0.05); border-radius:50%;"></div>
    <div style="position:absolute; bottom:-60px; right:100px; width:150px; height:150px; background:rgba(255,255,255,0.04); border-radius:50%;"></div>

    <div style="z-index:1;">
        <div style="font-size:13px; color:rgba(255,255,255,0.7); margin-bottom:4px;">Progress Belajar Kamu</div>
        @php
            $totalProgress = $enrollments->avg('progress_percentage') ?? 0;
        @endphp
        <div style="font-size:48px; font-weight:800; color:#fff; line-height:1;">{{ round($totalProgress) }}%</div>
        <div style="font-size:13px; color:rgba(255,255,255,0.8); margin:8px 0 16px;">
            @if($totalProgress >= 70) 🔥 Mantap! Terus lanjutkan!
            @elseif($totalProgress >= 40) 💪 Bagus, terus semangat!
            @else 🚀 Yuk mulai belajar lebih giat!
            @endif
        </div>
        <div class="student-dashboard-progress" style="width:300px; height:8px; background:rgba(255,255,255,0.2); border-radius:99px; overflow:hidden;">
            <div style="height:100%; width:{{ round($totalProgress) }}%; background:#fff; border-radius:99px;"></div>
        </div>
    </div>

    <div style="z-index:1; opacity:0.9;">
        <img src="{{ asset('images/student-hero.png') }}" alt="" style="height:160px;" onerror="this.style.display='none'">
    </div>
</div>

<div class="student-split" style="display:grid; grid-template-columns:1fr 320px; gap:24px;">

    {{-- ================================================================== --}}
    {{-- LEFT COLUMN                                                          --}}
    {{-- ================================================================== --}}
    <div>

        {{-- Lanjutkan Belajar --}}
        @if($lastProgress)
        <div class="card" style="margin-bottom:20px;">
            <div style="font-size:15px; font-weight:700; margin-bottom:14px;">Lanjutkan Belajar</div>
            <div class="student-inline-card" style="display:flex; gap:16px; align-items:center;">
                <div style="width:120px; height:72px; background:#1E293B; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fas fa-play-circle" style="font-size:28px; color:var(--primary);"></i>
                </div>
                <div style="flex:1;">
                    <div style="font-size:11px; color:var(--primary); font-weight:600; margin-bottom:3px;">
                        {{ $lastProgress->material->module->course->subject->name ?? '' }}
                    </div>
                    <div style="font-size:14px; font-weight:700; margin-bottom:6px;">
                        {{ $lastProgress->material->title ?? 'Materi' }}
                    </div>
                    <div style="font-size:11px; color:var(--text-muted);">
                        {{ $lastProgress->material->module->course->title ?? '' }}
                    </div>
                </div>
                <a href="{{ $lastProgress->material->module->course->slug ?? null
                    ? route('student.course.material.show', [$lastProgress->material->module->course->slug, $lastProgress->material->id])
                    : route('student.course.index') }}"
                   class="btn btn-primary" style="white-space:nowrap;">
                    <i class="fas fa-play"></i> Lanjutkan
                </a>
            </div>
        </div>
        @endif

        {{-- Kelas Saya --}}
        <div class="student-section-heading" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
            <div style="font-size:15px; font-weight:700;">Kelas Saya</div>
            <a href="{{ route('student.course.index') }}" style="font-size:12px; color:var(--primary); font-weight:600; text-decoration:none;">
                Lihat Semua <i class="fas fa-arrow-right" style="font-size:10px;"></i>
            </a>
        </div>

        <div class="student-card-grid student-grid-2" style="display:grid; grid-template-columns:repeat(2, 1fr); gap:14px; margin-bottom:24px;">
            @forelse($enrollments->take(4) as $enrollment)
                <x-course-card :enrollment="$enrollment" />
            @empty
                <div style="grid-column:span 2; text-align:center; padding:40px; color:var(--text-muted);">
                    <i class="fas fa-book-open" style="font-size:32px; margin-bottom:10px; opacity:0.3;"></i>
                    <p>Belum ada kelas. <a href="{{ route('student.course.explore') }}" style="color:var(--primary); font-weight:600;">Jelajahi kelas</a></p>
                </div>
            @endforelse
        </div>

        {{-- Rekomendasi --}}
        <div class="student-section-heading" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
            <div style="font-size:15px; font-weight:700;">Rekomendasi Untuk Kamu</div>
            <a href="{{ route('student.course.index') }}" style="font-size:12px; color:var(--primary); font-weight:600; text-decoration:none;">
                Lihat Semua <i class="fas fa-arrow-right" style="font-size:10px;"></i>
            </a>
        </div>

        @if($recommendedCourses->isEmpty())
            <div class="card" style="text-align:center; padding:30px; color:var(--text-muted);">
                <i class="fas fa-check-circle" style="font-size:28px; color:var(--success); opacity:0.4; display:block; margin-bottom:8px;"></i>
                <p style="font-size:13px; font-weight:600;">Kamu sudah mengikuti semua kelas yang tersedia!</p>
            </div>
        @else
            <div class="student-card-grid student-grid-2" style="display:grid; grid-template-columns:repeat(2, 1fr); gap:12px;">
                @foreach($recommendedCourses as $course)
                @php
                    $colors = ['#2563EB','#7C3AED','#059669','#D97706'];
                    $color  = $colors[$loop->index % 4];
                @endphp
                <a href="{{ route('student.course.show', $course->slug) }}"
                   style="background:{{ $color }}; border-radius:12px; padding:16px; color:#fff; text-decoration:none; display:block; position:relative; overflow:hidden; min-height:120px; transition:opacity 0.2s;"
                   onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                    <div style="position:absolute; top:-15px; right:-15px; width:70px; height:70px; background:rgba(255,255,255,0.1); border-radius:50%;"></div>
                    <div style="font-size:10px; font-weight:700; background:rgba(255,255,255,0.2); padding:2px 8px; border-radius:20px; display:inline-block; margin-bottom:8px;">
                        {{ $course->subject->name ?? 'Kelas' }}
                        @if($course->access_tier === 'paid') · <i class="fas fa-crown" style="font-size:9px;"></i> Premium @endif
                    </div>
                    <div style="font-size:13px; font-weight:700; line-height:1.3; margin-bottom:8px;">{{ Str::limit($course->title, 40) }}</div>
                    <div style="font-size:11px; opacity:0.8;">
                        <i class="fas fa-users" style="font-size:9px; margin-right:3px;"></i>{{ $course->enrollments_count }} siswa
                        @if($course->mentor) · {{ $course->mentor->name }} @endif
                    </div>
                </a>
                @endforeach
            </div>
        @endif

    </div>

    {{-- ================================================================== --}}
    {{-- RIGHT COLUMN                                                         --}}
    {{-- ================================================================== --}}
    <div>

        {{-- Jadwal Kelas Online --}}
        <div class="card" style="margin-bottom:20px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                <div style="font-size:15px; font-weight:700;">Jadwal Kelas Online</div>
                <a href="{{ route('student.live.index') }}" style="font-size:12px; color:var(--primary); font-weight:600; text-decoration:none;">Lihat Semua</a>
            </div>

            @forelse($upcomingLiveClasses as $liveClass)
                <x-schedule-item :liveClass="$liveClass" />
            @empty
                <div style="text-align:center; padding:20px; color:var(--text-muted); font-size:13px;">
                    Tidak ada jadwal mendatang.
                </div>
            @endforelse
        </div>

        {{-- Progress Kelas --}}
        <div class="card" style="margin-bottom:20px;">
            <div style="font-size:15px; font-weight:700; margin-bottom:14px;">Progress Kelas</div>
            @foreach($enrollments->take(4) as $enrollment)
            <div style="margin-bottom:12px;">
                <div style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:5px;">
                    <span style="font-weight:600; color:var(--text-main);">{{ $enrollment->course->subject->name ?? '-' }}</span>
                    <span style="color:var(--text-muted);">{{ $enrollment->progress_percentage }}%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-bar-fill" style="width:{{ $enrollment->progress_percentage }}%;"></div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pencapaian --}}
        @if($recentAchievements->count())
        <div class="card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
                <div style="font-size:15px; font-weight:700;">Pencapaian</div>
                <a href="{{ route('student.achievement.index') }}" style="font-size:12px; color:var(--primary); font-weight:600; text-decoration:none;">Lihat Semua</a>
            </div>
            <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:8px;">
                @foreach($recentAchievements as $ua)
                <div style="text-align:center;">
                    <div style="width:48px; height:48px; background:{{ $ua->achievement->color ?? '#2563EB' }}20; border-radius:12px; display:flex; align-items:center; justify-content:center; margin:0 auto 6px;">
                        <i class="fas fa-trophy" style="font-size:20px; color:{{ $ua->achievement->color ?? '#2563EB' }};"></i>
                    </div>
                    <div style="font-size:9px; font-weight:600; color:var(--text-muted); line-height:1.2;">{{ $ua->achievement->name }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>

@endsection

@extends('layouts.student')

@section('title', 'Dashboard Mentor')
@php $subtitle = 'Kelola kelas, materi, dan kelas online kamu.'; @endphp

@section('content')

{{-- Header --}}
<div style="background:linear-gradient(135deg,#1E293B,#7C3AED); border-radius:16px; padding:28px 32px; margin-bottom:24px; position:relative; overflow:hidden;">
    <div style="position:absolute; top:-30px; right:80px; width:180px; height:180px; background:rgba(255,255,255,0.05); border-radius:50%;"></div>
    <div style="position:relative; z-index:1;">
        <div style="font-size:12px; color:rgba(255,255,255,0.6); margin-bottom:4px; text-transform:uppercase; letter-spacing:1px; font-weight:700;">Mentor Panel</div>
        <h2 style="font-size:24px; font-weight:800; color:#fff; margin-bottom:6px;">Halo, {{ auth()->user()->name }}!</h2>
        <p style="font-size:13px; color:rgba(255,255,255,0.7);">Kelola kelas, materi, dan kelas online kamu dari sini.</p>
    </div>
</div>

{{-- Stats --}}
@php
    $myCourses    = auth()->user()->courses()->withCount('enrollments')->get();
    $myLiveClasses = auth()->user()->liveClasses()->get();
    $upcomingLive  = $myLiveClasses->where('status', 'scheduled')->count();
    $totalStudents = $myCourses->sum('enrollments_count');
@endphp

<div class="stats-row cols-4" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-book-open"></i></div>
        <div>
            <div class="stat-value">{{ $myCourses->count() }}</div>
            <div class="stat-label">Kelas Saya</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-users"></i></div>
        <div>
            <div class="stat-value">{{ $totalStudents }}</div>
            <div class="stat-label">Total Siswa</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-video"></i></div>
        <div>
            <div class="stat-value">{{ $myLiveClasses->count() }}</div>
            <div class="stat-label">Kelas Online</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon yellow"><i class="fas fa-calendar-alt"></i></div>
        <div>
            <div class="stat-value">{{ $upcomingLive }}</div>
            <div class="stat-label">Jadwal Mendatang</div>
        </div>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">

    {{-- Kelas Saya --}}
    <div>
        <div style="font-size:14px; font-weight:700; margin-bottom:14px;">Kelas yang Saya Ampu</div>

        @if($myCourses->isEmpty())
            <div class="card" style="text-align:center; padding:40px; color:var(--text-muted);">
                <i class="fas fa-book-open" style="font-size:32px; opacity:0.2; display:block; margin-bottom:10px;"></i>
                <p style="font-size:13px; font-weight:600;">Belum ada kelas.</p>
                <p style="font-size:12px; margin-top:4px;">Admin akan menugaskan kelas kepadamu.</p>
            </div>
        @else
            <div style="display:flex; flex-direction:column; gap:10px;">
                @foreach($myCourses as $course)
                    <div class="card" style="display:flex; align-items:center; gap:14px; padding:14px 16px;">
                        <div style="width:40px; height:40px; border-radius:9px; background:linear-gradient(135deg,#2563EB,#7C3AED); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="fas fa-book-open" style="color:#fff; font-size:16px;"></i>
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div style="font-size:13px; font-weight:700; margin-bottom:2px; overflow:hidden; white-space:nowrap; text-overflow:ellipsis;">{{ $course->title }}</div>
                            <div style="font-size:11px; color:var(--text-muted);">
                                {{ $course->enrollments_count }} siswa
                                · {{ $course->is_published ? 'Dipublikasikan' : 'Draft' }}
                                @if($course->is_premium) · <span style="color:#D97706;">Premium</span> @endif
                            </div>
                        </div>
                        <span class="badge {{ $course->is_published ? 'badge-success' : 'badge-warning' }}" style="flex-shrink:0; font-size:10px;">
                            {{ $course->is_published ? 'Live' : 'Draft' }}
                        </span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Kelas Online --}}
    <div>
        <div style="font-size:14px; font-weight:700; margin-bottom:14px;">Kelas Online Saya</div>

        @if($myLiveClasses->isEmpty())
            <div class="card" style="text-align:center; padding:40px; color:var(--text-muted);">
                <i class="fas fa-video" style="font-size:32px; opacity:0.2; display:block; margin-bottom:10px;"></i>
                <p style="font-size:13px; font-weight:600;">Belum ada kelas online.</p>
            </div>
        @else
            <div style="display:flex; flex-direction:column; gap:10px;">
                @foreach($myLiveClasses->sortByDesc('scheduled_at')->take(5) as $lc)
                    @php
                        $badge = match($lc->status) {
                            'live'      => ['class'=>'badge-danger',  'label'=>'LIVE'],
                            'scheduled' => ['class'=>'badge-primary', 'label'=>'Terjadwal'],
                            'ended'     => ['class'=>'badge-success', 'label'=>'Selesai'],
                            default     => ['class'=>'badge-warning', 'label'=>'Dibatalkan'],
                        };
                    @endphp
                    <div class="card" style="display:flex; align-items:center; gap:14px; padding:14px 16px;">
                        <div style="text-align:center; min-width:44px; background:var(--primary-light); border-radius:8px; padding:6px;">
                            <div style="font-size:16px; font-weight:800; color:var(--primary); line-height:1;">{{ $lc->scheduled_at->format('d') }}</div>
                            <div style="font-size:9px; font-weight:700; text-transform:uppercase; color:var(--primary);">{{ $lc->scheduled_at->format('M') }}</div>
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div style="font-size:13px; font-weight:700; margin-bottom:2px; overflow:hidden; white-space:nowrap; text-overflow:ellipsis;">{{ $lc->title }}</div>
                            <div style="font-size:11px; color:var(--text-muted);">{{ $lc->scheduled_at->format('H:i') }} WIB · {{ $lc->duration_minutes }} menit</div>
                        </div>
                        <span class="badge {{ $badge['class'] }}" style="flex-shrink:0; font-size:10px;">{{ $badge['label'] }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

{{-- Info box --}}
<div class="card" style="margin-top:20px; background:#FFFBEB; border-color:#FCD34D; display:flex; align-items:center; gap:12px; padding:14px 18px;">
    <i class="fas fa-circle-info" style="color:#F59E0B; font-size:18px; flex-shrink:0;"></i>
    <div style="font-size:13px; color:#92400E; line-height:1.5;">
        Untuk mengelola kelas, modul, materi, atau kelas online — hubungi admin untuk mendapatkan akses panel Admin.
    </div>
</div>

@endsection

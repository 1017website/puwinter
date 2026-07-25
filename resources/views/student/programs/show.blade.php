@extends('layouts.student')

@section('title', $plan->name)
@php $subtitle = 'Detail program & konten belajar.'; @endphp

@section('content')

<div style="margin-bottom:8px;">
    <a href="{{ route('student.program.index') }}" style="font-size:13px; color:var(--text-muted); text-decoration:none;">
        <i class="fas fa-arrow-left"></i> Semua Program
    </a>
</div>

@if(session('success'))
<div class="card" style="border-left:4px solid var(--success); margin-bottom:16px; color:#065F46;">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="card" style="border-left:4px solid #EF4444; margin-bottom:16px; color:#991B1B;">{{ session('error') }}</div>
@endif

{{-- Header program --}}
<div class="card" style="margin-bottom:20px;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap;">
        <div style="flex:1; min-width:240px;">
            <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                <h2 style="font-size:20px; font-weight:800;">{{ $plan->name }}</h2>
                @if($isPaid)
                    <span class="badge" style="background:#D1FAE5; color:#065F46;">Status: Berbayar</span>
                @elseif($isEnrolled)
                    <span class="badge" style="background:#DBEAFE; color:#1E40AF;">Status: Gratis</span>
                @endif
            </div>
            <p style="font-size:13px; color:var(--text-muted); margin-top:8px; line-height:1.7;">
                <span><i class="far fa-clock"></i> Durasi: {{ $plan->duration_months }} bulan</span>
                @if($plan->periodLabel())
                    <br><span><i class="far fa-calendar-alt"></i> {{ $plan->periodLabel() }}</span>
                @endif
                @if($plan->bonus)
                    <br><span><i class="fas fa-gift"></i> {{ $plan->bonus }}</span>
                @endif
            </p>
        </div>
        <div style="text-align:right;">
            <div style="font-size:24px; font-weight:800; color:var(--primary);">Rp {{ number_format($plan->price, 0, ',', '.') }}</div>
            @if(!$isEnrolled)
                <form method="POST" action="{{ route('student.program.enroll', $plan->id) }}" style="margin-top:10px;">
                    @csrf
                    <button class="btn btn-outline"><i class="fas fa-plus"></i> Daftar Gratis</button>
                </form>
            @elseif(!$isPaid)
                @if($plan->isQuotaFull())
                    <div style="margin-top:10px; font-size:13px; font-weight:600; color:#EF4444;"><i class="fas fa-circle-xmark"></i> Kuota peserta penuh</div>
                @else
                    @if(!is_null($plan->quota))
                    <div style="font-size:12px; color:var(--success); font-weight:600; margin-bottom:6px;"><i class="fas fa-user-check"></i> Sisa kuota: {{ $plan->remainingQuota() }}/{{ $plan->quota }}</div>
                    @endif
                    <form method="POST" action="{{ route('upgrade.checkout', $plan->slug) }}" style="margin-top:10px;">
                        @csrf
                        <button class="btn btn-primary"><i class="fas fa-crown"></i> Upgrade Berbayar</button>
                    </form>
                @endif
            @endif
        </div>
    </div>

    @if($isEnrolled && !$isPaid)
    <div style="margin-top:14px; background:#FEF3C7; border:1px solid #FCD34D; border-radius:8px; padding:12px 14px; font-size:13px; color:#92400E;">
        <i class="fas fa-info-circle"></i> Kamu terdaftar gratis. Beberapa konten (ditandai <strong>Berbayar</strong>) hanya bisa diakses setelah upgrade.
    </div>
    @endif
</div>

{{-- Helper badge tier --}}
@php
    $tierBadge = function ($tier) {
        return $tier === 'paid'
            ? '<span class="badge" style="background:#FEE2E2;color:#991B1B;">Berbayar</span>'
            : '<span class="badge" style="background:#D1FAE5;color:#065F46;">Gratis</span>';
    };
@endphp

{{-- KELAS / COURSE --}}
<div class="card" style="margin-bottom:18px;">
    <h3 style="font-size:15px; font-weight:700; margin-bottom:14px;"><i class="fas fa-book-open"></i> Kelas & Materi</h3>
    @forelse($courses as $course)
    <div class="student-program-row" style="display:flex; justify-content:space-between; align-items:center; padding:12px 0; border-bottom:1px solid var(--border); gap:10px;">
        <div>
            <div style="font-weight:600;">{{ $course->title }}</div>
            <div style="font-size:12px; color:var(--text-muted);">{{ $course->modules->count() }} modul • {{ $course->materials()->count() }} materi</div>
        </div>
        <div class="student-program-actions" style="display:flex; align-items:center; gap:10px; flex-shrink:0;">
            {!! $tierBadge($course->access_tier) !!}
            <a href="{{ route('student.course.show', $course->slug) }}" class="btn btn-outline btn-sm">Buka</a>
        </div>
    </div>
    @empty
    <p style="font-size:13px; color:var(--text-muted);">Belum ada kelas di program ini.</p>
    @endforelse
</div>

{{-- TRYOUT --}}
@if($studentTryoutEnabled ?? true)
<div class="card" style="margin-bottom:18px;">
    <h3 style="font-size:15px; font-weight:700; margin-bottom:14px;"><i class="fas fa-bullseye"></i> Tryout</h3>
    @forelse($tryouts as $t)
    <div class="student-program-row" style="display:flex; justify-content:space-between; align-items:center; padding:12px 0; border-bottom:1px solid var(--border); gap:10px;">
        <div>
            <div style="font-weight:600;">{{ $t->title }}</div>
            <div style="font-size:12px; color:var(--text-muted);">{{ $t->total_questions }} soal • {{ $t->duration_minutes }} menit</div>
        </div>
        <div class="student-program-actions" style="display:flex; align-items:center; gap:10px; flex-shrink:0;">
            {!! $tierBadge($t->access_tier) !!}
            <a href="{{ route('student.tryout.start', $t->id) }}" class="btn btn-outline btn-sm">Mulai</a>
        </div>
    </div>
    @empty
    <p style="font-size:13px; color:var(--text-muted);">Belum ada tryout di program ini.</p>
    @endforelse
</div>
@endif

{{-- LIVE CLASS --}}
<div class="card">
    <h3 style="font-size:15px; font-weight:700; margin-bottom:14px;"><i class="fas fa-video"></i> Kelas Online</h3>
    @forelse($liveClasses as $lc)
    <div class="student-program-row" style="display:flex; justify-content:space-between; align-items:center; padding:12px 0; border-bottom:1px solid var(--border); gap:10px;">
        <div>
            <div style="font-weight:600;">{{ $lc->title }}</div>
            <div style="font-size:12px; color:var(--text-muted);">{{ $lc->scheduled_at?->translatedFormat('d M Y • H:i') }} WIB</div>
        </div>
        <div class="student-program-actions" style="display:flex; align-items:center; gap:10px; flex-shrink:0;">
            {!! $tierBadge($lc->access_tier) !!}
            <a href="{{ route('student.live.show', $lc->id) }}" class="btn btn-outline btn-sm">Detail</a>
        </div>
    </div>
    @empty
    <p style="font-size:13px; color:var(--text-muted);">Belum ada kelas online di program ini.</p>
    @endforelse
</div>

@endsection

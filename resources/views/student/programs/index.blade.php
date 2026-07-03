@extends('layouts.student')

@section('title', 'Program')
@php $subtitle = 'Pilih program belajar yang sesuai kebutuhanmu.'; @endphp

@section('content')

<div style="margin-bottom:18px;">
    <h2 style="font-size:22px; font-weight:800;">Program Belajar</h2>
    <p style="font-size:13px; color:var(--text-muted); margin-top:2px; line-height:1.7;">
        Mulai belajar tanpa ragu. Daftar gratis ke program pilihanmu, tonton video gratis sebagai pemanasan, lalu lanjut upgrade saat kamu sudah siap mendapatkan kelas online, materi premium, dan tryout berbayar.
    </p>
</div>

<div class="card" style="margin-bottom:22px; background:linear-gradient(135deg,#EFF6FF,#F5F3FF); border:1px solid #BFDBFE; display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
    <div style="display:flex; gap:14px; align-items:flex-start; flex:1; min-width:240px;">
        <div style="width:44px; height:44px; border-radius:12px; background:#2563EB; color:#fff; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <i class="fas fa-play"></i>
        </div>
        <div>
            <div style="font-size:15px; font-weight:800; color:var(--text-main);">Coba dulu lewat video gratis</div>
            <div style="font-size:13px; color:var(--text-muted); margin-top:4px; line-height:1.6;">
                Pilih program yang paling cocok, akses video pengantar gratis, dan rasakan cara belajar Puwinter sebelum mengambil akses berbayar penuh.
            </div>
        </div>
    </div>
    <div style="background:#D1FAE5; color:#065F46; font-size:12px; font-weight:800; padding:8px 12px; border-radius:999px; white-space:nowrap;">
        <i class="fas fa-gift"></i> Video Gratis Tersedia
    </div>
</div>

@if(session('success'))
<div class="card" style="border-left:4px solid var(--success); margin-bottom:16px; color:#065F46;">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="card" style="border-left:4px solid #EF4444; margin-bottom:16px; color:#991B1B;">{{ session('error') }}</div>
@endif

<div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(300px,1fr)); gap:18px;">
    @forelse($programs as $plan)
    @php
        $enr    = $enrollments[$plan->id] ?? null;
        $isPaid = $enr ? $enr->isPaidActive() : false;
    @endphp
    <div class="card" style="display:flex; flex-direction:column; gap:14px;">
        @if($plan->flyer_image)
        <div style="margin:-4px -4px 0; border-radius:10px; overflow:hidden; cursor:zoom-in;"
             onclick="window.open('{{ asset('storage/'.$plan->flyer_image) }}','_blank')">
            <img src="{{ asset('storage/'.$plan->flyer_image) }}" alt="Pamflet {{ $plan->name }}"
                 style="width:100%; display:block; aspect-ratio:3/4; object-fit:cover;">
        </div>
        @endif
        <div>
            <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:10px;">
                <h3 style="font-size:16px; font-weight:800; line-height:1.3;">{{ $plan->name }}</h3>
                @if($isPaid)
                    <span class="badge" style="background:#D1FAE5; color:#065F46; flex-shrink:0;">Berbayar</span>
                @elseif($enr)
                    <span class="badge" style="background:#DBEAFE; color:#1E40AF; flex-shrink:0;">Terdaftar</span>
                @endif
            </div>
            @if($plan->periodLabel())
            <div style="font-size:12px; color:var(--text-muted); margin-top:4px;"><i class="far fa-calendar"></i> {{ $plan->periodLabel() }}</div>
            @endif
            <div style="font-size:20px; font-weight:800; color:var(--primary); margin-top:6px;">
                Rp {{ number_format($plan->price, 0, ',', '.') }}
            </div>
            <div style="font-size:12px; color:var(--text-muted);">
                {{ $plan->duration_months }} bulan @if($plan->bonus) • {{ $plan->bonus }} @endif
            </div>
            @if(!is_null($plan->quota))
            <div style="font-size:12px; font-weight:600; margin-top:6px; color:{{ $plan->isQuotaFull() ? '#EF4444' : 'var(--success)' }};">
                @if($plan->isQuotaFull())<i class="fas fa-circle-xmark"></i> Kuota penuh
                @else<i class="fas fa-user-check"></i> Sisa kuota: {{ $plan->remainingQuota() }}/{{ $plan->quota }}@endif
            </div>
            @endif
        </div>

        @if(is_array($plan->features) && count($plan->features))
        <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:6px;">
            @foreach(array_slice($plan->features, 0, 4) as $f)
            <li style="font-size:12.5px; color:var(--text-muted); display:flex; gap:8px; align-items:flex-start;">
                <i class="fas fa-check" style="color:var(--success); margin-top:3px;"></i> <span>{{ $f }}</span>
            </li>
            @endforeach
        </ul>
        @endif

        <div style="margin-top:auto; display:flex; gap:8px;">
            @if($enr)
                <a href="{{ route('student.program.show', $plan->id) }}" class="btn btn-primary" style="flex:1; text-align:center;">
                    <i class="fas fa-arrow-right"></i> Buka Program
                </a>
            @else
                <form method="POST" action="{{ route('student.program.enroll', $plan->id) }}" style="flex:1;">
                    @csrf
                    <button type="submit" class="btn btn-outline" style="width:100%;">
                        <i class="fas fa-plus"></i> Daftar Program (Gratis)
                    </button>
                </form>
            @endif
        </div>
    </div>
    @empty
    <div class="card" style="grid-column:1/-1; text-align:center; padding:40px; color:var(--text-muted);">
        Belum ada program tersedia.
    </div>
    @endforelse
</div>

@endsection

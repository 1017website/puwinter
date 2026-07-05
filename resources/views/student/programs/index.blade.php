@extends('layouts.student')

@section('title', 'Program')
@php $subtitle = 'Pilih program belajar yang sesuai kebutuhanmu.'; @endphp

@section('content')

<div style="margin-bottom:24px;">
    <h2 style="font-size:22px; font-weight:800;">Program Belajar</h2>
    <p style="font-size:13px; color:var(--text-muted); margin-top:2px;">
        Daftar gratis ke program mana pun. Untuk akses penuh (kelas online, materi premium, tryout berbayar), upgrade ke berbayar.
    </p>
</div>

<div class="card" style="margin-bottom:20px; border:1px solid #BFDBFE; background:linear-gradient(135deg,#EFF6FF,#F8FAFC);">
    <div style="display:flex; align-items:flex-start; gap:14px;">
        <div style="width:42px; height:42px; border-radius:12px; background:var(--primary); color:#fff; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <i class="fas fa-play"></i>
        </div>
        <div style="flex:1;">
            <div style="font-size:16px; font-weight:800; color:var(--text-main); margin-bottom:4px;">
                Coba dulu video gratis sebelum memilih program berbayar
            </div>
            <p style="font-size:13px; color:var(--text-muted); line-height:1.7; margin:0;">
                Masuk ke program yang kamu minati, tonton materi video gratis, lalu lanjut upgrade ketika sudah yakin dengan metode belajar Puwinter.
                Belajar jadi lebih aman, terarah, dan kamu bisa melihat kualitas materinya sejak awal.
            </p>
        </div>
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
            <div style="font-size:12px; color:var(--text-muted); margin-top:4px;"><i class="fas fa-graduation-cap"></i> {{ $plan->gradeLabel() }}</div>
            @if($plan->periodLabel())
            <div style="font-size:12px; color:var(--text-muted); margin-top:4px;"><i class="far fa-calendar-alt"></i> {{ $plan->periodLabel() }}</div>
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
        Belum ada program tersedia untuk kelasmu saat ini.
    </div>
    @endforelse
</div>

@endsection

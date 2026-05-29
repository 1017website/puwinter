@extends('layouts.student')

@section('title', 'Pencapaian')
@php $subtitle = 'Kumpulkan semua badge dengan rajin belajar!'; @endphp

@section('content')

{{-- Header / progress --}}
<div class="card" style="margin-bottom:20px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:14px;">
    <div>
        <h2 style="font-size:18px; font-weight:800;">Pencapaian Saya</h2>
        <p style="font-size:13px; color:var(--text-muted); margin-top:2px;">
            {{ $totalEarned }} dari {{ $totalAll }} badge berhasil diraih
        </p>
    </div>
    <div style="min-width:200px; flex:1; max-width:320px;">
        @php $pct = $totalAll > 0 ? round($totalEarned / $totalAll * 100) : 0; @endphp
        <div style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:6px;">
            <span style="color:var(--text-muted);">Progress</span>
            <span style="font-weight:700; color:var(--primary);">{{ $pct }}%</span>
        </div>
        <div class="progress-bar">
            <div class="progress-bar-fill" style="width:{{ $pct }}%;"></div>
        </div>
    </div>
</div>

@if($achievements->isEmpty())
    <div class="card" style="text-align:center; padding:60px 20px;">
        <i class="fas fa-trophy" style="font-size:42px; opacity:0.2; display:block; margin-bottom:14px;"></i>
        <p style="font-weight:700; font-size:15px;">Belum ada achievement</p>
        <p style="font-size:13px; color:var(--text-muted); margin-top:4px;">Achievement akan muncul di sini.</p>
    </div>
@else
    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(160px, 1fr)); gap:14px;">
        @foreach($achievements as $ach)
            @php
                $isEarned = $earned->has($ach->id);
                $color    = $ach->color ?? '#2563EB';
            @endphp
            <div class="card" style="text-align:center; padding:22px 14px; position:relative; {{ $isEarned ? '' : 'opacity:0.55;' }}">
                @if($isEarned)
                    <span style="position:absolute; top:10px; right:10px; color:#10B981; font-size:14px;" title="Sudah diraih">
                        <i class="fas fa-circle-check"></i>
                    </span>
                @else
                    <span style="position:absolute; top:10px; right:10px; color:var(--text-muted); font-size:13px;" title="Belum diraih">
                        <i class="fas fa-lock"></i>
                    </span>
                @endif

                <div style="width:64px; height:64px; border-radius:16px; margin:0 auto 12px; display:flex; align-items:center; justify-content:center;
                            background:{{ $isEarned ? $color.'20' : '#F1F5F9' }};">
                    <i class="fas {{ $ach->icon ?: 'fa-trophy' }}"
                       style="font-size:26px; color:{{ $isEarned ? $color : '#94A3B8' }};"></i>
                </div>

                <div style="font-size:13px; font-weight:700; color:var(--text-main); margin-bottom:4px;">{{ $ach->name }}</div>
                <div style="font-size:11.5px; color:var(--text-muted); line-height:1.4;">{{ $ach->description }}</div>

                @if($isEarned && $earned->get($ach->id)->earned_at)
                    <div style="font-size:10.5px; color:#10B981; margin-top:8px; font-weight:600;">
                        Diraih {{ $earned->get($ach->id)->earned_at->translatedFormat('d M Y') }}
                    </div>
                @endif
            </div>
        @endforeach
    </div>
@endif

@endsection

@extends('layouts.student')

@section('title', 'Tryout')
@php $subtitle = 'Simulasi ujian UTBK untuk mengukur kemampuanmu.'; @endphp

@section('content')

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
    <div>
        <h2 style="font-size:22px; font-weight:800;">Tryout</h2>
        <p style="font-size:13px; color:var(--text-muted); margin-top:2px;">Simulasi ujian UTBK untuk mengukur kemampuanmu.</p>
    </div>
</div>

{{-- Stats --}}
<div class="stats-row cols-4" style="margin-bottom:28px;">
    <x-stat-card icon="fa-file-circle-check" color="blue"   :value="number_format($stats['total_soal'])"    label="Total Soal"         suffix=" soal" />
    <x-stat-card icon="fa-bullseye"          color="purple" :value="number_format($stats['total_tryout'])"  label="Paket Tryout"       suffix=" paket" />
    <x-stat-card icon="fa-users"             color="green"  :value="number_format($stats['total_peserta'])" label="Total Peserta"      suffix=" siswa" />
    <x-stat-card icon="fa-pen-to-square"     color="yellow" :value="number_format($stats['soal_dijawab'])"  label="Soal Kamu Kerjakan" suffix=" soal" />
</div>

{{-- Filter --}}
<div style="display:flex; gap:10px; margin-bottom:20px; flex-wrap:wrap;">
    <div style="display:flex; gap:4px; background:#fff; border:1px solid var(--border); border-radius:10px; padding:4px;">
        @foreach(['Semua' => '', 'Gratis' => 'gratis', 'Premium' => 'premium'] as $label => $val)
        <a href="{{ route('student.tryout.index', ['filter' => $val]) }}"
           style="padding:7px 16px; border-radius:7px; font-size:13px; font-weight:600; text-decoration:none;
                  {{ request('filter', '') === $val ? 'background:var(--primary); color:#fff;' : 'color:var(--text-muted);' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>
</div>

{{-- Tryout Grid --}}
@if($tryouts->isEmpty())
    <div style="text-align:center; padding:80px; color:var(--text-muted);">
        <i class="fas fa-bullseye" style="font-size:48px; opacity:0.2; display:block; margin-bottom:16px;"></i>
        <p style="font-size:15px; font-weight:600;">Belum ada tryout tersedia.</p>
    </div>
@else
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px;">
        @foreach($tryouts as $tryout)
        <div class="card" style="padding:0; overflow:hidden; transition:transform 0.2s, box-shadow 0.2s;"
             onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 24px rgba(0,0,0,0.08)'"
             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">

            {{-- Card Header --}}
            <div style="background:linear-gradient(135deg,#1E293B,#2563EB); padding:20px; position:relative; overflow:hidden;">
                <div style="position:absolute; top:-20px; right:-20px; width:100px; height:100px; background:rgba(255,255,255,0.06); border-radius:50%;"></div>
                <div style="position:absolute; bottom:-30px; right:20px; width:70px; height:70px; background:rgba(255,255,255,0.04); border-radius:50%;"></div>

                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px; position:relative;">
                    <span style="background:rgba(255,255,255,0.15); color:#fff; font-size:10px; font-weight:700; padding:3px 8px; border-radius:20px; text-transform:uppercase; letter-spacing:0.5px;">
                        {{ $tryout->subject->name ?? 'Semua Mapel' }}
                    </span>
                    @if($tryout->is_premium)
                        <x-premium-badge />
                    @else
                        <span style="background:rgba(16,185,129,0.2); color:#34D399; font-size:10px; font-weight:700; padding:3px 8px; border-radius:20px;">GRATIS</span>
                    @endif
                </div>

                <h3 style="font-size:15px; font-weight:800; color:#fff; margin-bottom:4px; position:relative;">{{ $tryout->title }}</h3>
                @if($tryout->series)
                    <div style="font-size:12px; color:rgba(255,255,255,0.6); position:relative;">{{ $tryout->series }}</div>
                @endif
            </div>

            {{-- Card Body --}}
            <div style="padding:16px 20px;">
                <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-bottom:16px;">
                    <div style="text-align:center; padding:10px; background:var(--bg); border-radius:8px;">
                        <div style="font-size:16px; font-weight:800; color:var(--text-main);">{{ $tryout->total_questions }}</div>
                        <div style="font-size:10px; color:var(--text-muted); margin-top:2px;">Soal</div>
                    </div>
                    <div style="text-align:center; padding:10px; background:var(--bg); border-radius:8px;">
                        <div style="font-size:16px; font-weight:800; color:var(--text-main);">{{ $tryout->duration_minutes }}</div>
                        <div style="font-size:10px; color:var(--text-muted); margin-top:2px;">Menit</div>
                    </div>
                    <div style="text-align:center; padding:10px; background:var(--bg); border-radius:8px;">
                        <div style="font-size:16px; font-weight:800; color:var(--text-main);">{{ $tryout->attempts->count() }}</div>
                        <div style="font-size:10px; color:var(--text-muted); margin-top:2px;">Peserta</div>
                    </div>
                </div>

                {{-- Riwayat attempt user --}}
                @php
                    $myAttempt = $tryout->attempts->where('user_id', auth()->id())->whereNotNull('submitted_at')->sortByDesc('score')->first();
                @endphp

                @if($myAttempt)
                    <div style="background:#ECFDF5; border:1px solid #6EE7B7; border-radius:8px; padding:10px 12px; margin-bottom:12px; display:flex; justify-content:space-between; align-items:center;">
                        <div style="font-size:12px; color:#065F46; font-weight:600;">
                            <i class="fas fa-check-circle"></i> Skor terbaik: <strong>{{ $myAttempt->score }}</strong>
                        </div>
                        <div style="font-size:11px; color:#6EE7B7; font-weight:600;">Peringkat #{{ $myAttempt->rank_at_submit }}</div>
                    </div>
                @endif

                <div style="display:flex; gap:8px;">
                    @if($tryout->is_premium && !auth()->user()->isPremium())
                        <a href="{{ route('upgrade.index') }}"
                           style="flex:1; padding:10px; background:linear-gradient(135deg,#F59E0B,#EF4444); color:#fff; border-radius:8px; font-size:13px; font-weight:700; text-align:center; text-decoration:none; display:flex; align-items:center; justify-content:center; gap:6px;">
                            <i class="fas fa-crown"></i> Upgrade Premium
                        </a>
                    @else
                        <a href="{{ route('student.tryout.start', $tryout->id) }}"
                           style="flex:1; padding:10px; background:var(--primary); color:#fff; border-radius:8px; font-size:13px; font-weight:700; text-align:center; text-decoration:none; display:flex; align-items:center; justify-content:center; gap:6px; transition:background 0.15s;"
                           onmouseover="this.style.background='#1D4ED8'"
                           onmouseout="this.style.background='var(--primary)'">
                            <i class="fas fa-play"></i> {{ $myAttempt ? 'Ulangi Tryout' : 'Mulai Tryout' }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif

@endsection

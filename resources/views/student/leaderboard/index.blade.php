@extends('layouts.student')

@section('title', 'Leaderboard')
@php $subtitle = 'Lihat posisi kamu dibanding pejuang UTBK lainnya.'; @endphp

@section('content')

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:12px;">
    <div>
        <h2 style="font-size:22px; font-weight:800;">Leaderboard</h2>
        <p style="font-size:13px; color:var(--text-muted); margin-top:2px;">Lihat posisi kamu dibanding pejuang UTBK lainnya.</p>
    </div>
</div>

{{-- Filter --}}
<form method="GET" class="student-filter-form" style="display:flex; gap:10px; margin-bottom:20px; flex-wrap:wrap; align-items:center;">
    {{-- Scope --}}
    <div class="student-filter-scroll" style="display:flex; gap:4px; background:#fff; border:1px solid var(--border); border-radius:10px; padding:4px;">
        @foreach(['global'=>'Global','sekolah'=>'Sekolah','kota'=>'Kota','provinsi'=>'Provinsi'] as $val => $label)
            <button type="submit" name="filter" value="{{ $val }}"
                    style="padding:7px 14px; border-radius:7px; font-size:13px; font-weight:600; border:none; cursor:pointer; font-family:inherit;
                           {{ $filter === $val ? 'background:var(--primary); color:#fff;' : 'background:transparent; color:var(--text-muted);' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Mapel --}}
    <select name="subject_id" onchange="this.form.submit()"
            style="padding:8px 12px; border:1px solid var(--border); border-radius:8px; font-size:13px; font-family:inherit; background:#fff; color:var(--text-main); outline:none;">
        <option value="">Semua Mapel</option>
        @foreach($subjects as $subject)
            <option value="{{ $subject->id }}" {{ $subjectId == $subject->id ? 'selected' : '' }}>
                {{ $subject->name }}
            </option>
        @endforeach
    </select>
</form>

{{-- My Score Card --}}
@if($myScore)
    <div style="background:linear-gradient(135deg,#1E293B,#2563EB); border-radius:14px; padding:20px 24px; margin-bottom:20px; display:flex; align-items:center; gap:20px; flex-wrap:wrap;">
        <div style="width:52px; height:52px; border-radius:50%; background:rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <i class="fas fa-user" style="font-size:22px; color:#fff;"></i>
        </div>
        <div style="flex:1; min-width:180px;">
            <div style="font-size:12px; color:rgba(255,255,255,0.65); margin-bottom:2px;">Posisi Kamu</div>
            <div style="font-size:16px; font-weight:800; color:#fff;">{{ auth()->user()->name }}</div>
        </div>
        <div style="text-align:center; min-width:80px;">
            <div style="font-size:28px; font-weight:800; color:#fff; line-height:1;">{{ number_format($myScore->total_score) }}</div>
            <div style="font-size:11px; color:rgba(255,255,255,0.65); margin-top:2px;">Total Skor</div>
        </div>
        @if($myScore->rank_global)
            <div style="text-align:center; min-width:80px; padding:12px 16px; background:rgba(255,255,255,0.12); border-radius:10px;">
                <div style="font-size:24px; font-weight:800; color:#FCD34D; line-height:1;">#{{ $myScore->rank_global }}</div>
                <div style="font-size:11px; color:rgba(255,255,255,0.65); margin-top:2px;">Peringkat Global</div>
            </div>
        @endif
        @if($myScore->percentile)
            <div style="text-align:center; min-width:80px; padding:12px 16px; background:rgba(255,255,255,0.12); border-radius:10px;">
                <div style="font-size:24px; font-weight:800; color:#86EFAC; line-height:1;">{{ $myScore->percentile }}%</div>
                <div style="font-size:11px; color:rgba(255,255,255,0.65); margin-top:2px;">Persentil</div>
            </div>
        @endif
    </div>
@else
    <div class="card" style="margin-bottom:20px; display:flex; align-items:center; gap:14px; padding:16px 20px;">
        <i class="fas fa-info-circle" style="color:var(--primary); font-size:20px; flex-shrink:0;"></i>
        <div style="font-size:13px; color:var(--text-muted);">
            Kamu belum masuk leaderboard. Selesaikan tryout untuk mendapatkan skor.
        </div>
        @if($studentTryoutEnabled ?? true)
            <a href="{{ route('student.tryout.index') }}" class="btn btn-primary" style="flex-shrink:0; font-size:12px; padding:8px 14px;">
                Mulai Tryout
            </a>
        @endif
    </div>
@endif

{{-- Leaderboard Table --}}
<div class="card student-table-card" style="padding:0; overflow:hidden;">
    @if($leaderboard->isEmpty())
        <div style="text-align:center; padding:60px; color:var(--text-muted);">
            <i class="fas fa-chart-bar" style="font-size:40px; opacity:0.2; display:block; margin-bottom:12px;"></i>
            <p style="font-size:14px; font-weight:600;">Belum ada data leaderboard.</p>
            <p style="font-size:12px; margin-top:4px;">Jadilah yang pertama menyelesaikan tryout!</p>
        </div>
    @else
        <table style="width:100%; border-collapse:collapse; font-size:13px;">
            <thead>
                <tr style="background:var(--bg); border-bottom:1px solid var(--border);">
                    <th style="padding:12px 16px; text-align:center; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-muted); width:60px;">#</th>
                    <th style="padding:12px 16px; text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-muted);">Nama</th>
                    <th style="padding:12px 16px; text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-muted);">Asal</th>
                    <th style="padding:12px 16px; text-align:right; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-muted);">Skor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($leaderboard as $index => $entry)
                    @php $isMe = $entry->user_id === auth()->id(); @endphp
                    <tr style="border-bottom:1px solid var(--border); {{ $isMe ? 'background:#EFF6FF;' : '' }} transition:background 0.1s;"
                        onmouseover="this.style.background='{{ $isMe ? '#DBEAFE' : '#F8FAFC' }}'"
                        onmouseout="this.style.background='{{ $isMe ? '#EFF6FF' : '' }}'">

                        {{-- Rank --}}
                        <td style="padding:14px 16px; text-align:center;">
                            @if($index === 0)
                                <div style="width:30px; height:30px; border-radius:50%; background:linear-gradient(135deg,#F59E0B,#D97706); display:flex; align-items:center; justify-content:center; margin:0 auto;">
                                    <i class="fas fa-crown" style="font-size:13px; color:#fff;"></i>
                                </div>
                            @elseif($index === 1)
                                <div style="width:30px; height:30px; border-radius:50%; background:linear-gradient(135deg,#94A3B8,#64748B); display:flex; align-items:center; justify-content:center; margin:0 auto;">
                                    <span style="font-size:12px; font-weight:800; color:#fff;">2</span>
                                </div>
                            @elseif($index === 2)
                                <div style="width:30px; height:30px; border-radius:50%; background:linear-gradient(135deg,#CD7C2F,#B5651D); display:flex; align-items:center; justify-content:center; margin:0 auto;">
                                    <span style="font-size:12px; font-weight:800; color:#fff;">3</span>
                                </div>
                            @else
                                <span style="font-size:13px; font-weight:700; color:var(--text-muted);">{{ $index + 1 }}</span>
                            @endif
                        </td>

                        {{-- User --}}
                        <td style="padding:14px 16px;">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="width:34px; height:34px; border-radius:50%; background:{{ $isMe ? 'var(--primary)' : '#E2E8F0' }}; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; color:{{ $isMe ? '#fff' : 'var(--text-muted)' }}; flex-shrink:0;">
                                    {{ strtoupper(substr($entry->user->name ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:{{ $isMe ? '800' : '600' }}; color:{{ $isMe ? 'var(--primary)' : 'var(--text-main)' }};">
                                        {{ $entry->user->name ?? '-' }}
                                        @if($isMe) <span style="font-size:11px; background:var(--primary); color:#fff; padding:1px 6px; border-radius:4px; margin-left:4px;">Kamu</span> @endif
                                    </div>
                                    @if($entry->user->grade)
                                        <div style="font-size:11px; color:var(--text-muted);">Kelas {{ $entry->user->grade }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Asal --}}
                        <td style="padding:14px 16px; font-size:12px; color:var(--text-muted);">
                            {{ $entry->user->school ? Str::limit($entry->user->school, 28) : '-' }}
                            @if($entry->user->city)
                                <div>{{ $entry->user->city }}</div>
                            @endif
                        </td>

                        {{-- Skor --}}
                        <td style="padding:14px 16px; text-align:right;">
                            <span style="font-size:16px; font-weight:800; color:{{ $isMe ? 'var(--primary)' : 'var(--text-main)' }};">
                                {{ number_format($entry->total_score) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

@endsection

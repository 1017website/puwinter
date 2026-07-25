@extends('layouts.student')

@section('title', 'Riwayat Belajar')
@php $subtitle = 'Pantau aktivitas dan progress belajarmu.'; @endphp

@section('content')

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:12px;">
    <div>
        <h2 style="font-size:22px; font-weight:800;">Riwayat Belajar</h2>
        <p style="font-size:13px; color:var(--text-muted); margin-top:2px;">Pantau aktivitas dan progress belajarmu.</p>
    </div>

    {{-- Period filter --}}
    <div class="student-filter-scroll" style="display:flex; gap:4px; background:#fff; border:1px solid var(--border); border-radius:10px; padding:4px;">
        @foreach(['7'=>'7 Hari','30'=>'30 Hari','90'=>'90 Hari'] as $val => $label)
            <a href="{{ route('student.history.index', ['period' => $val]) }}"
               style="padding:7px 14px; border-radius:7px; font-size:13px; font-weight:600; text-decoration:none;
                      {{ $period == $val ? 'background:var(--primary); color:#fff;' : 'color:var(--text-muted);' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
</div>

{{-- Stats --}}
<div class="stats-row cols-3" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-clock"></i></div>
        <div>
            <div class="stat-value">
                @php
                    $totalHours = $stats->total_seconds ? floor($stats->total_seconds / 3600) : 0;
                    $totalMins  = $stats->total_seconds ? floor(($stats->total_seconds % 3600) / 60) : 0;
                @endphp
                {{ $totalHours > 0 ? $totalHours . 'j ' . $totalMins . 'm' : $totalMins . ' mnt' }}
            </div>
            <div class="stat-label">Total Belajar</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-list-check"></i></div>
        <div>
            <div class="stat-value">{{ $stats->total_activities ?? 0 }}</div>
            <div class="stat-label">Total Aktivitas</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon yellow"><i class="fas fa-star"></i></div>
        <div>
            <div class="stat-value">{{ $stats->avg_score ? number_format($stats->avg_score, 0) : '-' }}</div>
            <div class="stat-label">Rata-rata Skor</div>
        </div>
    </div>
</div>

{{-- Grafik waktu belajar --}}
@if($chartData->isNotEmpty())
    <div class="card" style="margin-bottom:24px;">
        <div style="font-size:14px; font-weight:700; margin-bottom:16px;">Waktu Belajar per Hari</div>
        <div style="display:flex; align-items:flex-end; gap:6px; height:80px; overflow-x:auto; padding-bottom:4px;">
            @php $maxHours = $chartData->max('hours') ?: 1; @endphp
            @foreach($chartData as $day)
                <div style="display:flex; flex-direction:column; align-items:center; gap:4px; flex:1; min-width:28px;">
                    <div style="font-size:9px; color:var(--text-muted); white-space:nowrap;">
                        {{ round($day->hours, 1) }}j
                    </div>
                    <div style="width:100%; background:var(--primary); border-radius:4px 4px 0 0; min-height:4px;
                                height:{{ max(4, ($day->hours / $maxHours) * 56) }}px;
                                opacity:0.85; transition:opacity 0.15s;"
                         title="{{ $day->date }}: {{ round($day->hours, 1) }} jam"
                         onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.85'">
                    </div>
                    <div style="font-size:9px; color:var(--text-muted); white-space:nowrap;">
                        {{ \Carbon\Carbon::parse($day->date)->format('d/m') }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<div class="student-split" style="--student-aside:260px; display:grid; grid-template-columns:1fr 260px; gap:20px; align-items:flex-start;">

    {{-- Daftar Aktivitas --}}
    <div>
        <div style="font-size:14px; font-weight:700; margin-bottom:14px;">Aktivitas Terbaru</div>

        @if($activities->isEmpty())
            <div class="card" style="text-align:center; padding:60px; color:var(--text-muted);">
                <i class="fas fa-clock-rotate-left" style="font-size:40px; opacity:0.2; display:block; margin-bottom:12px;"></i>
                <p style="font-size:14px; font-weight:600;">Belum ada aktivitas.</p>
                <p style="font-size:12px; margin-top:4px;">Mulai belajar untuk melihat riwayat di sini.</p>
            </div>
        @else
            <div style="display:flex; flex-direction:column; gap:8px;">
                @foreach($activities as $activity)
                    @php
                        $typeConfig = match($activity->activity_type) {
                            'material'   => ['icon'=>'fa-play-circle',       'color'=>'#2563EB', 'bg'=>'#EFF6FF', 'label'=>'Video/Materi'],
                            'tryout'     => ['icon'=>'fa-bullseye',          'color'=>'#7C3AED', 'bg'=>'#F5F3FF', 'label'=>'Tryout'],
                            'live_class' => ['icon'=>'fa-video',             'color'=>'#059669', 'bg'=>'#ECFDF5', 'label'=>'Kelas Online'],
                            'pdf'        => ['icon'=>'fa-file-pdf',          'color'=>'#DC2626', 'bg'=>'#FEF2F2', 'label'=>'PDF'],
                            'pembahasan' => ['icon'=>'fa-lightbulb',         'color'=>'#D97706', 'bg'=>'#FFFBEB', 'label'=>'Pembahasan'],
                            default      => ['icon'=>'fa-book',              'color'=>'#64748B', 'bg'=>'#F1F5F9', 'label'=>'Aktivitas'],
                        };
                    @endphp
                    <div class="card" style="padding:14px 16px; display:flex; align-items:center; gap:14px;">
                        <div style="width:38px; height:38px; border-radius:9px; background:{{ $typeConfig['bg'] }}; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="fas {{ $typeConfig['icon'] }}" style="color:{{ $typeConfig['color'] }}; font-size:16px;"></i>
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div style="font-size:13px; font-weight:600; margin-bottom:3px;">
                                <span class="badge" style="background:{{ $typeConfig['bg'] }}; color:{{ $typeConfig['color'] }}; font-size:10px; margin-right:6px;">{{ $typeConfig['label'] }}</span>
                                @if($activity->notes)
                                    {{ $activity->notes }}
                                @else
                                    Aktivitas {{ $typeConfig['label'] }}
                                @endif
                            </div>
                            <div style="font-size:11px; color:var(--text-muted); display:flex; gap:12px; flex-wrap:wrap;">
                                <span><i class="fas fa-clock" style="margin-right:3px;"></i>{{ $activity->durationFormatted() }}</span>
                                @if($activity->score)
                                    <span><i class="fas fa-star" style="margin-right:3px; color:#F59E0B;"></i>Skor: {{ number_format($activity->score) }}</span>
                                @endif
                                <span>{{ $activity->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <div style="font-size:11px; color:var(--text-muted); white-space:nowrap; flex-shrink:0;">
                            {{ $activity->created_at->format('d M Y') }}<br>
                            <span style="font-weight:600; color:var(--text-main);">{{ $activity->created_at->format('H:i') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($activities->hasPages())
                <div style="margin-top:16px;">{{ $activities->appends(request()->query())->links() }}</div>
            @endif
        @endif
    </div>

    {{-- Sidebar: Distribusi Mapel --}}
    <div style="position:sticky; top:80px;">
        <div class="card">
            <div style="font-size:13px; font-weight:700; margin-bottom:14px; padding-bottom:10px; border-bottom:1px solid var(--border);">
                <i class="fas fa-chart-pie" style="color:var(--primary); margin-right:6px;"></i>
                Per Mata Pelajaran
            </div>

            @if($subjectDistribution->isEmpty())
                <div style="text-align:center; padding:20px; color:var(--text-muted); font-size:13px;">
                    Belum ada data.
                </div>
            @else
                @php
                    $totalSecs = $subjectDistribution->sum();
                    $colors    = ['#2563EB','#7C3AED','#059669','#D97706','#DC2626','#0891B2'];
                @endphp
                @foreach($subjectDistribution->sortDesc()->take(6) as $subjectName => $secs)
                    @php
                        $pct   = $totalSecs > 0 ? round(($secs / $totalSecs) * 100) : 0;
                        $color = $colors[$loop->index % count($colors)];
                        $mins  = round($secs / 60);
                    @endphp
                    <div style="margin-bottom:12px;">
                        <div style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:5px;">
                            <span style="font-weight:600; color:var(--text-main);">{{ $subjectName }}</span>
                            <span style="color:var(--text-muted);">{{ $pct }}%</span>
                        </div>
                        <div style="height:5px; background:#F1F5F9; border-radius:99px; overflow:hidden;">
                            <div style="height:100%; width:{{ $pct }}%; background:{{ $color }}; border-radius:99px;"></div>
                        </div>
                        <div style="font-size:10px; color:var(--text-muted); margin-top:3px;">{{ $mins }} menit</div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

</div>

@endsection

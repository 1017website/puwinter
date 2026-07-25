@extends('layouts.student')

@section('title', 'Riwayat Tryout')
@php $subtitle = 'Semua hasil tryout yang pernah kamu kerjakan.'; @endphp

@section('content')

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:12px;">
    <div>
        <h2 style="font-size:22px; font-weight:800;">Riwayat Tryout</h2>
        <p style="font-size:13px; color:var(--text-muted); margin-top:2px;">Semua hasil tryout yang pernah kamu kerjakan.</p>
    </div>
    <a href="{{ route('student.tryout.index') }}" class="btn btn-primary">
        <i class="fas fa-bullseye"></i> Kerjakan Tryout
    </a>
</div>

{{-- Statistik ringkas --}}
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(160px,1fr)); gap:14px; margin-bottom:24px;">
    @php
        $cards = [
            ['Total Pengerjaan', $stats['total_attempt'], 'fa-list-check', '#4F46E5'],
            ['Rata-rata Skor', $stats['avg_score'], 'fa-chart-line', '#0EA5E9'],
            ['Skor Terbaik', $stats['best_score'], 'fa-star', '#F59E0B'],
            ['Peringkat Terbaik', $stats['best_rank'] ? '#'.$stats['best_rank'] : '-', 'fa-medal', '#10B981'],
        ];
    @endphp
    @foreach($cards as [$label, $val, $icon, $color])
    <div class="card" style="padding:16px 18px;">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:42px; height:42px; border-radius:10px; background:{{ $color }}1A; color:{{ $color }}; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <i class="fas {{ $icon }}"></i>
            </div>
            <div>
                <div style="font-size:20px; font-weight:800; line-height:1;">{{ $val }}</div>
                <div style="font-size:12px; color:var(--text-muted); margin-top:4px;">{{ $label }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Daftar riwayat --}}
<div class="card">
    @if($attempts->count() > 0)
    <div class="student-table-scroll" style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:13.5px;">
            <thead>
                <tr style="text-align:left; color:var(--text-muted); border-bottom:1.5px solid var(--border);">
                    <th style="padding:12px 10px; font-weight:600;">Tryout</th>
                    <th style="padding:12px 10px; font-weight:600;">Mapel</th>
                    <th style="padding:12px 10px; font-weight:600; text-align:center;">Skor</th>
                    <th style="padding:12px 10px; font-weight:600; text-align:center;">B / S / K</th>
                    <th style="padding:12px 10px; font-weight:600; text-align:center;">Peringkat</th>
                    <th style="padding:12px 10px; font-weight:600;">Tanggal</th>
                    <th style="padding:12px 10px; font-weight:600; text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attempts as $a)
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:12px 10px; font-weight:600; color:var(--text-main);">
                        {{ $a->tryout->title ?? 'Tryout dihapus' }}
                    </td>
                    <td style="padding:12px 10px; color:var(--text-muted);">
                        {{ $a->tryout->subject->name ?? '-' }}
                    </td>
                    <td style="padding:12px 10px; text-align:center;">
                        <span style="font-weight:800; color:var(--primary);">{{ rtrim(rtrim(number_format($a->score, 2, '.', ''), '0'), '.') }}</span>
                        @if(!is_null($a->weighted_score))
                            <div style="font-size:11px; color:var(--text-muted);">bobot: {{ $a->weighted_score }}</div>
                        @endif
                    </td>
                    <td style="padding:12px 10px; text-align:center; color:var(--text-muted);">
                        <span style="color:#10B981; font-weight:700;">{{ $a->correct_count }}</span> /
                        <span style="color:#EF4444; font-weight:700;">{{ $a->wrong_count }}</span> /
                        <span>{{ $a->empty_count }}</span>
                    </td>
                    <td style="padding:12px 10px; text-align:center;">
                        @if($a->rank_at_submit)
                            <span class="badge badge-warning">#{{ $a->rank_at_submit }}</span>
                        @else
                            <span style="color:var(--text-muted);">-</span>
                        @endif
                    </td>
                    <td style="padding:12px 10px; color:var(--text-muted);">
                        {{ $a->submitted_at?->translatedFormat('d M Y • H:i') }}
                    </td>
                    <td style="padding:12px 10px; text-align:right;">
                        <a href="{{ route('student.tryout.result', $a->id) }}" class="btn btn-outline btn-sm">
                            <i class="fas fa-eye"></i> Hasil
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top:18px;">
        {{ $attempts->links() }}
    </div>
    @else
    <div style="text-align:center; padding:50px 20px; color:var(--text-muted);">
        <i class="fas fa-clipboard-list" style="font-size:42px; opacity:0.3; margin-bottom:14px; display:block;"></i>
        <p style="font-weight:600; margin-bottom:6px;">Belum ada riwayat tryout</p>
        <p style="font-size:13px;">Yuk kerjakan tryout pertamamu untuk melihat hasilnya di sini.</p>
        <a href="{{ route('student.tryout.index') }}" class="btn btn-primary" style="margin-top:16px;">
            <i class="fas fa-bullseye"></i> Mulai Tryout
        </a>
    </div>
    @endif
</div>

@endsection

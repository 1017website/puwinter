@extends('admin.layouts.app')
@section('title', 'Hasil Tryout Siswa')

@section('content')

<div class="page-header">
    <div>
        <h2>Hasil Tryout Siswa</h2>
        <p>Pantau skor, ranking, mode IRT, dan catatan integritas pengerjaan tryout.</p>
    </div>
    <a href="{{ route('admin.tryouts.index') }}" class="btn btn-outline">
        <i class="fas fa-bullseye"></i> Kelola Tryout
    </a>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:#EFF6FF; color:#2563EB;"><i class="fas fa-clipboard-check"></i></div>
        <div>
            <div class="stat-value">{{ number_format($stats['total_attempts']) }}</div>
            <div class="stat-label">Attempt selesai</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#ECFDF5; color:#059669;"><i class="fas fa-user-graduate"></i></div>
        <div>
            <div class="stat-value">{{ number_format($stats['total_students']) }}</div>
            <div class="stat-label">Siswa unik</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#F3E8FF; color:#7C3AED;"><i class="fas fa-chart-line"></i></div>
        <div>
            <div class="stat-value">{{ number_format($stats['avg_score'], 1) }}</div>
            <div class="stat-label">Rata-rata regular</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#FFFBEB; color:#D97706;"><i class="fas fa-triangle-exclamation"></i></div>
        <div>
            <div class="stat-value">{{ number_format($stats['flagged_attempts']) }}</div>
            <div class="stat-label">Ada pindah tab</div>
        </div>
    </div>
</div>

@if($topAttempt)
<div class="card" style="margin-bottom:20px; border-left:4px solid var(--primary);">
    <div style="display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
        <div>
            <div style="font-size:12px; color:var(--muted); font-weight:700; text-transform:uppercase; letter-spacing:.6px; margin-bottom:4px;">Skor tertinggi pada filter ini</div>
            <div style="font-size:15px; font-weight:800; color:var(--text);">{{ $topAttempt->user->name ?? '-' }}</div>
            <div style="font-size:12px; color:var(--muted); margin-top:2px;">{{ $topAttempt->tryout->title ?? '-' }}</div>
        </div>
        <div style="display:flex; align-items:center; gap:10px;">
            <div style="text-align:right;">
                <div style="font-size:26px; font-weight:800; color:var(--primary); line-height:1;">{{ number_format($topAttempt->irt_score ?? $topAttempt->score ?? 0, 1) }}</div>
                <div style="font-size:11px; color:var(--muted); margin-top:3px;">{{ !is_null($topAttempt->irt_score) ? 'Skor IRT' : 'Skor regular' }}</div>
            </div>
            <a href="{{ route('admin.tryout-results.show', $topAttempt) }}" class="btn btn-primary btn-sm">
                Detail <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>
@endif

<form method="GET" class="card" style="margin-bottom:20px; display:grid; grid-template-columns:1.2fr 1fr 1fr .8fr .8fr .8fr auto; gap:10px; align-items:end; padding:16px;">
    <div>
        <label style="display:block; font-size:12px; font-weight:700; margin-bottom:6px; color:var(--muted);">Cari</label>
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Nama/email siswa atau judul tryout">
    </div>
    <div>
        <label style="display:block; font-size:12px; font-weight:700; margin-bottom:6px; color:var(--muted);">Tryout</label>
        <select name="tryout_id" class="form-control">
            <option value="">Semua Tryout</option>
            @foreach($tryouts as $tryout)
                <option value="{{ $tryout->id }}" {{ (string) request('tryout_id') === (string) $tryout->id ? 'selected' : '' }}>
                    {{ $tryout->title }}
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <label style="display:block; font-size:12px; font-weight:700; margin-bottom:6px; color:var(--muted);">Mapel</label>
        <select name="subject_id" class="form-control">
            <option value="">Semua Mapel</option>
            @foreach($subjects as $subject)
                <option value="{{ $subject->id }}" {{ (string) request('subject_id') === (string) $subject->id ? 'selected' : '' }}>
                    {{ $subject->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <label style="display:block; font-size:12px; font-weight:700; margin-bottom:6px; color:var(--muted);">Mode</label>
        <select name="scoring_mode" class="form-control">
            <option value="">Semua</option>
            <option value="regular" {{ request('scoring_mode') === 'regular' ? 'selected' : '' }}>Regular</option>
            <option value="irt" {{ request('scoring_mode') === 'irt' ? 'selected' : '' }}>IRT</option>
        </select>
    </div>
    <div>
        <label style="display:block; font-size:12px; font-weight:700; margin-bottom:6px; color:var(--muted);">Integritas</label>
        <select name="integrity" class="form-control">
            <option value="">Semua</option>
            <option value="clean" {{ request('integrity') === 'clean' ? 'selected' : '' }}>Aman</option>
            <option value="flagged" {{ request('integrity') === 'flagged' ? 'selected' : '' }}>Ada pindah tab</option>
        </select>
    </div>
    <div>
        <label style="display:block; font-size:12px; font-weight:700; margin-bottom:6px; color:var(--muted);">Urutkan</label>
        <select name="sort" class="form-control">
            <option value="submitted_desc" {{ $sort === 'submitted_desc' ? 'selected' : '' }}>Terbaru</option>
            <option value="score_desc" {{ $sort === 'score_desc' ? 'selected' : '' }}>Skor tertinggi</option>
            <option value="score_asc" {{ $sort === 'score_asc' ? 'selected' : '' }}>Skor terendah</option>
            <option value="rank_asc" {{ $sort === 'rank_asc' ? 'selected' : '' }}>Ranking terbaik</option>
        </select>
    </div>
    <div style="display:flex; gap:8px;">
        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
        @if(request()->query())
            <a href="{{ route('admin.tryout-results.index') }}" class="btn btn-outline">Reset</a>
        @endif
    </div>
</form>

<div class="card" style="padding:0;">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Siswa</th>
                    <th>Tryout</th>
                    <th>Mode</th>
                    <th>Skor</th>
                    <th>IRT</th>
                    <th>Benar/Salah/Kosong</th>
                    <th>Rank</th>
                    <th>Integritas</th>
                    <th>Submit</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attempts as $attempt)
                @php
                    $isIrt = ($attempt->tryout->scoring_mode ?? 'regular') === 'irt';
                    $isCalibrated = (bool) ($attempt->tryout->irt_calibrated ?? false);
                @endphp
                <tr>
                    <td>
                        <div style="font-weight:700;">{{ $attempt->user->name ?? '-' }}</div>
                        <div style="font-size:11px; color:var(--muted);">{{ $attempt->user->email ?? '-' }}</div>
                        @php $studentGrade = ($attempt->user?->relationLoaded('grade') ? $attempt->user->getRelation('grade') : null); @endphp
                        @if($studentGrade)
                            <div style="font-size:11px; color:var(--muted); margin-top:2px;">{{ $studentGrade->name }}</div>
                        @elseif($attempt->user?->getAttribute('grade'))
                            <div style="font-size:11px; color:var(--muted); margin-top:2px;">Kelas {{ $attempt->user->getAttribute('grade') }}</div>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight:700; max-width:280px;">{{ $attempt->tryout->title ?? '-' }}</div>
                        <div style="font-size:11px; color:var(--muted);">{{ $attempt->tryout->subject->name ?? 'Semua mapel' }}</div>
                    </td>
                    <td>
                        <span class="badge {{ $isIrt ? 'badge-primary' : 'badge-gray' }}">{{ $isIrt ? 'IRT' : 'Regular' }}</span>
                        @if($isIrt)
                            <div style="font-size:10px; color:{{ $isCalibrated ? '#059669' : '#D97706' }}; font-weight:700; margin-top:4px;">
                                {{ $isCalibrated ? 'Terkalibrasi' : 'Belum kalibrasi' }}
                            </div>
                        @endif
                    </td>
                    <td style="font-weight:800; font-size:16px;">{{ number_format($attempt->score ?? 0, 1) }}</td>
                    <td>
                        @if(!is_null($attempt->irt_score))
                            <span style="font-weight:800; color:#0369A1;">{{ number_format($attempt->irt_score, 1) }}</span>
                        @else
                            <span style="color:var(--muted);">-</span>
                        @endif
                    </td>
                    <td>
                        <span style="color:#059669; font-weight:800;">{{ $attempt->correct_count }}</span>
                        <span style="color:var(--muted);">/</span>
                        <span style="color:#DC2626; font-weight:800;">{{ $attempt->wrong_count }}</span>
                        <span style="color:var(--muted);">/</span>
                        <span style="color:#64748B; font-weight:800;">{{ $attempt->empty_count }}</span>
                    </td>
                    <td style="font-weight:800;">{{ $attempt->rank_at_submit ? '#' . $attempt->rank_at_submit : '-' }}</td>
                    <td>
                        @if(($attempt->tab_switch_count ?? 0) > 0)
                            <span class="badge badge-warning">{{ $attempt->tab_switch_count }}x pindah tab</span>
                        @else
                            <span class="badge badge-success">Aman</span>
                        @endif
                    </td>
                    <td style="font-size:12px; color:var(--muted); white-space:nowrap;">
                        {{ $attempt->submitted_at?->format('d M Y H:i') }}
                    </td>
                    <td>
                        <a href="{{ route('admin.tryout-results.show', $attempt) }}" class="btn btn-outline btn-sm">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" style="text-align:center; padding:42px; color:var(--muted);">
                        <i class="fas fa-inbox" style="font-size:32px; opacity:.25; display:block; margin-bottom:10px;"></i>
                        Belum ada hasil tryout sesuai filter.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:12px 20px;">{{ $attempts->links() }}</div>
</div>

<style>
@media (max-width: 1180px) {
    form.card { grid-template-columns: repeat(2, 1fr) !important; }
}
@media (max-width: 640px) {
    form.card { grid-template-columns: 1fr !important; }
}
</style>

@endsection

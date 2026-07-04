@extends('admin.layouts.app')
@section('title', 'Detail Hasil Tryout')

@section('content')

@php
    $studentGrade = ($attempt->user?->relationLoaded('grade') ? $attempt->user->getRelation('grade') : null);
    $isIrt = $tryout->isIrt();
    $isCalibrated = (bool) $tryout->irt_calibrated;
    $officialLabel = $scoreColumn === 'irt_score' ? 'Skor IRT' : 'Skor Regular';
    $accuracy = $questions->count() > 0 ? round(($attempt->correct_count / $questions->count()) * 100) : 0;
    $percentile = $totalParticipants > 0 ? max(0, round((1 - ($currentRank / $totalParticipants)) * 100, 1)) : 0;
@endphp

<div class="page-header">
    <div>
        <a href="{{ route('admin.tryout-results.index', request()->query()) }}" style="font-size:13px; color:var(--muted); text-decoration:none; display:inline-flex; align-items:center; gap:6px; margin-bottom:8px;">
            <i class="fas fa-arrow-left"></i> Kembali ke Hasil Tryout
        </a>
        <h2>Detail Hasil Tryout</h2>
        <p>{{ $attempt->user->name ?? '-' }} — {{ $tryout->title }}</p>
    </div>
    <div style="display:flex; gap:8px; flex-wrap:wrap;">
        <a href="{{ route('admin.tryouts.show', $tryout) }}" class="btn btn-outline">
            <i class="fas fa-list"></i> Lihat Soal
        </a>
        @if($isIrt)
            <form method="POST" action="{{ route('admin.tryouts.calibrate-irt', $tryout) }}" onsubmit="return confirm('Kalibrasi ulang IRT untuk tryout ini? Ranking dan skor IRT seluruh peserta akan diperbarui.')">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-scale-balanced"></i> {{ $isCalibrated ? 'Kalibrasi Ulang IRT' : 'Kalibrasi IRT' }}
                </button>
            </form>
        @endif
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:#EFF6FF; color:#2563EB;"><i class="fas fa-star"></i></div>
        <div>
            <div class="stat-value">{{ number_format($scoreValue, 1) }}</div>
            <div class="stat-label">{{ $officialLabel }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#ECFDF5; color:#059669;"><i class="fas fa-check"></i></div>
        <div>
            <div class="stat-value">{{ $accuracy }}%</div>
            <div class="stat-label">Akurasi benar penuh</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#F3E8FF; color:#7C3AED;"><i class="fas fa-ranking-star"></i></div>
        <div>
            <div class="stat-value">#{{ $currentRank }}</div>
            <div class="stat-label">dari {{ number_format($totalParticipants) }} peserta</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#FFFBEB; color:#D97706;"><i class="fas fa-clock"></i></div>
        <div>
            <div class="stat-value">{{ $attempt->duration() }}</div>
            <div class="stat-label">menit pengerjaan</div>
        </div>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 340px; gap:20px; align-items:start;">
    <div>
        <div class="card" style="margin-bottom:20px; background:linear-gradient(135deg,#1E293B,#1D4ED8); color:#fff; border:none; overflow:hidden; position:relative;">
            <div style="position:absolute; width:220px; height:220px; border-radius:999px; background:rgba(255,255,255,.06); right:-70px; top:-70px;"></div>
            <div style="display:grid; grid-template-columns:180px 1fr; gap:24px; position:relative; align-items:center;">
                <div style="text-align:center;">
                    <div style="font-size:54px; font-weight:800; line-height:1;">{{ number_format($scoreValue, 1) }}</div>
                    <div style="font-size:12px; opacity:.7; margin-top:5px;">{{ $officialLabel }}</div>
                    <div style="display:inline-block; margin-top:10px; padding:5px 12px; background:rgba(255,255,255,.12); border-radius:20px; font-size:11px; font-weight:800;">
                        Persentil {{ $percentile }}%
                    </div>
                </div>
                <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:10px;">
                    <div style="background:rgba(255,255,255,.1); border-radius:10px; padding:14px; text-align:center;">
                        <div style="font-size:22px; font-weight:800; color:#34D399;">{{ $attempt->correct_count }}</div>
                        <div style="font-size:11px; opacity:.65;">Benar</div>
                    </div>
                    <div style="background:rgba(255,255,255,.1); border-radius:10px; padding:14px; text-align:center;">
                        <div style="font-size:22px; font-weight:800; color:#F87171;">{{ $attempt->wrong_count }}</div>
                        <div style="font-size:11px; opacity:.65;">Salah</div>
                    </div>
                    <div style="background:rgba(255,255,255,.1); border-radius:10px; padding:14px; text-align:center;">
                        <div style="font-size:22px; font-weight:800; color:#CBD5E1;">{{ $attempt->empty_count }}</div>
                        <div style="font-size:11px; opacity:.65;">Kosong</div>
                    </div>
                    <div style="background:rgba(255,255,255,.1); border-radius:10px; padding:14px; text-align:center;">
                        <div style="font-size:22px; font-weight:800; color:#FBBF24;">{{ $attempt->tab_switch_count ?? 0 }}</div>
                        <div style="font-size:11px; opacity:.65;">Pindah Tab</div>
                    </div>
                </div>
            </div>
        </div>

        @include('student.tryout._score-comparison', ['attempt' => $attempt])

        <div class="card" style="margin-bottom:20px;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; gap:12px;">
                <div style="font-size:15px; font-weight:800;"><i class="fas fa-chart-simple" style="color:#7C3AED; margin-right:6px;"></i> Performa Per Mata Pelajaran</div>
                <span class="badge badge-primary">Weighted Rank #{{ $weightedRank }}</span>
            </div>
            @foreach($subjectStats as $subject => $stat)
                @php
                    $pct = $stat['total'] > 0 ? round(($stat['correct'] / $stat['total']) * 100) : 0;
                @endphp
                <div style="margin-bottom:14px;">
                    <div style="display:flex; justify-content:space-between; gap:8px; font-size:13px; margin-bottom:7px;">
                        <strong>{{ $subject }}</strong>
                        <span style="color:var(--muted);">{{ $stat['correct'] }}/{{ $stat['total'] }} benar · {{ $stat['partial'] ?? 0 }} partial · {{ $stat['wrong'] ?? 0 }} salah · {{ $stat['empty'] ?? 0 }} kosong</span>
                    </div>
                    <div style="height:8px; background:#F1F5F9; border-radius:99px; overflow:hidden;">
                        <div style="height:100%; width:{{ $pct }}%; background:{{ $pct >= 70 ? '#10B981' : ($pct >= 50 ? '#F59E0B' : '#EF4444') }};"></div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; gap:12px;">
                <div style="font-size:15px; font-weight:800;"><i class="fas fa-list-check" style="color:#2563EB; margin-right:6px;"></i> Review Jawaban Siswa</div>
                <div style="font-size:12px; color:var(--muted);">Klik setiap nomor untuk membuka soal dan pembahasan.</div>
            </div>

            @foreach($answerRows as $row)
                @php
                    $q = $row['question'];
                    $status = $row['status'];
                    $picked = $row['picked'];
                    $keys = $row['keys'];
                    $statusMeta = match($status) {
                        'correct' => ['label' => 'Benar', 'bg' => '#ECFDF5', 'color' => '#059669', 'icon' => 'fa-check-circle'],
                        'partial' => ['label' => 'Partial', 'bg' => '#FFFBEB', 'color' => '#D97706', 'icon' => 'fa-adjust'],
                        'wrong' => ['label' => 'Salah', 'bg' => '#FEF2F2', 'color' => '#DC2626', 'icon' => 'fa-times-circle'],
                        default => ['label' => 'Kosong', 'bg' => '#F1F5F9', 'color' => '#64748B', 'icon' => 'fa-minus-circle'],
                    };
                @endphp
                <details style="border:1px solid var(--border); border-radius:10px; margin-bottom:10px; overflow:hidden; background:#fff;">
                    <summary style="list-style:none; cursor:pointer; padding:12px 14px; display:flex; align-items:center; justify-content:space-between; gap:14px; background:{{ $statusMeta['bg'] }};">
                        <div style="display:flex; align-items:center; gap:10px; min-width:0;">
                            <span style="width:28px; height:28px; border-radius:7px; background:{{ $statusMeta['color'] }}; color:#fff; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; flex-shrink:0;">{{ $row['number'] }}</span>
                            <div style="min-width:0;">
                                <div style="font-size:13px; font-weight:800; color:{{ $statusMeta['color'] }};"><i class="fas {{ $statusMeta['icon'] }}"></i> {{ $statusMeta['label'] }}</div>
                                <div style="font-size:12px; color:#64748B; margin-top:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:520px;">{{ Str::limit($q->question_text, 95) }}</div>
                            </div>
                        </div>
                        <div style="font-size:12px; color:#475569; text-align:right; flex-shrink:0;">
                            Jawab: <strong>{{ count($picked) ? strtoupper(implode(', ', $picked)) : '—' }}</strong>
                            · Kunci: <strong style="color:#059669;">{{ strtoupper(implode(', ', $keys)) }}</strong>
                            <div style="margin-top:4px; display:flex; gap:5px; justify-content:flex-end; flex-wrap:wrap;">
                                <span class="badge badge-gray">{{ ucfirst($q->difficulty) }}</span>
                                @if(!is_null($q->irt_weight))
                                    <span class="badge badge-primary">IRT {{ number_format($q->irt_weight, 2) }}</span>
                                @endif
                                @if(!is_null($q->correct_rate))
                                    <span class="badge badge-gray">{{ number_format($q->correct_rate, 1) }}% benar</span>
                                @endif
                            </div>
                        </div>
                    </summary>

                    <div style="padding:16px; border-top:1px solid var(--border);">
                        <div style="font-size:14px; color:var(--text); line-height:1.7; margin-bottom:14px; white-space:pre-wrap;">{{ $q->question_text }}</div>

                        @foreach($q->options() as $key => $text)
                            @php
                                $isKey = in_array($key, $keys, true);
                                $isPicked = in_array($key, $picked, true);
                                $isWrongPick = $isPicked && !$isKey;
                            @endphp
                            <div style="display:flex; gap:10px; align-items:flex-start; padding:10px 12px; border-radius:8px; margin-bottom:7px; background:{{ $isKey ? '#ECFDF5' : ($isWrongPick ? '#FEF2F2' : '#F8FAFC') }}; border:1px solid {{ $isKey ? '#6EE7B7' : ($isWrongPick ? '#FECACA' : '#E2E8F0') }};">
                                <span style="width:24px; height:24px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:800; flex-shrink:0; background:{{ $isKey ? '#10B981' : ($isPicked ? '#EF4444' : '#E2E8F0') }}; color:{{ ($isKey || $isPicked) ? '#fff' : '#64748B' }};">{{ strtoupper($key) }}</span>
                                <div style="font-size:13px; line-height:1.6; color:var(--text); white-space:pre-wrap; flex:1;">{{ $text }}</div>
                                @if($isKey)
                                    <i class="fas fa-check-circle" style="color:#059669; margin-top:4px;"></i>
                                @elseif($isWrongPick)
                                    <i class="fas fa-times-circle" style="color:#DC2626; margin-top:4px;"></i>
                                @endif
                            </div>
                        @endforeach

                        @if($q->explanation)
                            <div style="margin-top:14px; padding:14px; background:#EFF6FF; border-left:3px solid #2563EB; border-radius:8px;">
                                <div style="font-size:12px; font-weight:800; color:#2563EB; margin-bottom:6px;"><i class="fas fa-lightbulb"></i> Pembahasan</div>
                                <div style="font-size:13px; line-height:1.7; color:#1E293B; white-space:pre-wrap;">{{ $q->explanation }}</div>
                            </div>
                        @endif
                    </div>
                </details>
            @endforeach
        </div>
    </div>

    <div>
        <div class="card" style="margin-bottom:16px;">
            <div style="font-size:13px; font-weight:800; margin-bottom:14px;">Data Siswa</div>
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:14px;">
                <div style="width:42px; height:42px; border-radius:50%; background:var(--primary); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:800;">{{ strtoupper(substr($attempt->user->name ?? 'S', 0, 1)) }}</div>
                <div>
                    <div style="font-weight:800;">{{ $attempt->user->name ?? '-' }}</div>
                    <div style="font-size:12px; color:var(--muted);">{{ $attempt->user->email ?? '-' }}</div>
                </div>
            </div>
            <div style="display:grid; gap:9px; font-size:13px;">
                <div style="display:flex; justify-content:space-between; gap:12px;"><span style="color:var(--muted);">Kelas</span><strong>{{ $studentGrade->name ?? ($attempt->user?->getAttribute('grade') ? 'Kelas ' . $attempt->user->getAttribute('grade') : '-') }}</strong></div>
                <div style="display:flex; justify-content:space-between; gap:12px;"><span style="color:var(--muted);">Sekolah</span><strong style="text-align:right;">{{ $attempt->user->school ?? '-' }}</strong></div>
                <div style="display:flex; justify-content:space-between; gap:12px;"><span style="color:var(--muted);">Kota</span><strong>{{ $attempt->user->city ?? '-' }}</strong></div>
            </div>
        </div>

        <div class="card" style="margin-bottom:16px;">
            <div style="font-size:13px; font-weight:800; margin-bottom:14px;">Info Tryout</div>
            <div style="display:grid; gap:9px; font-size:13px;">
                <div style="display:flex; justify-content:space-between; gap:12px;"><span style="color:var(--muted);">Mapel</span><strong>{{ $tryout->subject->name ?? 'Semua' }}</strong></div>
                <div style="display:flex; justify-content:space-between; gap:12px;"><span style="color:var(--muted);">Mode</span><strong>{{ $isIrt ? 'IRT' : 'Regular' }}</strong></div>
                <div style="display:flex; justify-content:space-between; gap:12px;"><span style="color:var(--muted);">Kalibrasi</span><strong style="color:{{ $isIrt && $isCalibrated ? '#059669' : '#D97706' }};">{{ $isIrt ? ($isCalibrated ? 'Sudah' : 'Belum') : '-' }}</strong></div>
                <div style="display:flex; justify-content:space-between; gap:12px;"><span style="color:var(--muted);">Total Soal</span><strong>{{ $questions->count() }}</strong></div>
                <div style="display:flex; justify-content:space-between; gap:12px;"><span style="color:var(--muted);">Mulai</span><strong>{{ $attempt->started_at?->format('d M Y H:i') }}</strong></div>
                <div style="display:flex; justify-content:space-between; gap:12px;"><span style="color:var(--muted);">Submit</span><strong>{{ $attempt->submitted_at?->format('d M Y H:i') }}</strong></div>
            </div>
        </div>

        <div class="card" style="margin-bottom:16px;">
            <div style="font-size:13px; font-weight:800; margin-bottom:14px;">Perbandingan Skor</div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; text-align:center;">
                <div style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px; padding:12px;">
                    <div style="font-size:24px; font-weight:800; color:#334155;">{{ number_format($attempt->score ?? 0, 1) }}</div>
                    <div style="font-size:11px; color:var(--muted);">Regular</div>
                </div>
                <div style="background:#EFF6FF; border:1px solid #BFDBFE; border-radius:10px; padding:12px;">
                    <div style="font-size:24px; font-weight:800; color:#2563EB;">{{ is_null($attempt->irt_score) ? '-' : number_format($attempt->irt_score, 1) }}</div>
                    <div style="font-size:11px; color:var(--muted);">IRT</div>
                </div>
            </div>
            <div style="margin-top:10px; font-size:12px; color:var(--muted); line-height:1.6;">
                Skor resmi mengikuti mode tryout. Jika mode IRT belum dikalibrasi, sistem masih memakai skor regular sebagai skor sementara.
            </div>
        </div>

        @if(($attempt->tab_switch_count ?? 0) > 0)
            <div class="card" style="border-left:4px solid #F59E0B; background:#FFFBEB;">
                <div style="font-size:13px; font-weight:800; color:#92400E; margin-bottom:6px;"><i class="fas fa-triangle-exclamation"></i> Catatan Integritas</div>
                <div style="font-size:13px; color:#B45309; line-height:1.6;">
                    Siswa tercatat meninggalkan jendela tryout sebanyak <strong>{{ $attempt->tab_switch_count }}x</strong>. Data ini indikator awal, bukan bukti mutlak kecurangan.
                </div>
            </div>
        @endif
    </div>
</div>

<style>
@media (max-width: 1100px) {
    div[style*="grid-template-columns:1fr 340px"] { grid-template-columns:1fr !important; }
    div[style*="grid-template-columns:180px 1fr"] { grid-template-columns:1fr !important; }
}
@media (max-width: 720px) {
    div[style*="grid-template-columns:repeat(4, 1fr)"] { grid-template-columns:repeat(2, 1fr) !important; }
}
</style>

@endsection

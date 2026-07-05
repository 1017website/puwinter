@extends('layouts.student')

@section('title', 'Hasil Tryout')
@php $subtitle = 'Lihat hasil dan pembahasan tryout kamu.'; @endphp

@section('content')

@php
    $tryout    = $attempt->tryout;
    $questions = $tryout->questions;
    $answers   = $attempt->answers ?? [];

    // Hitung per subject berbasis bobot nilai per soal.
    $subjectStats = [];
    foreach ($questions as $q) {
        $subjectName = $q->subject->name ?? 'Lainnya';
        if (!isset($subjectStats[$subjectName])) {
            $subjectStats[$subjectName] = ['total' => 0.0, 'earned' => 0.0, 'correct' => 0];
        }
        $subjectStats[$subjectName]['total'] += $q->scoreWeight();
        $userAnswer = $answers[$q->id] ?? null;
        $gradeRes = $q->grade($userAnswer, $q->scoreWeight());
        $subjectStats[$subjectName]['earned'] += (float) ($gradeRes['earned'] ?? 0);
        if (($gradeRes['status'] ?? '') === 'correct') {
            $subjectStats[$subjectName]['correct']++;
        }
    }

    // Persentil simulasi
    $percentile = $totalParticipants > 0
        ? round((1 - ($attempt->rank_at_submit / $totalParticipants)) * 100, 1)
        : 0;

    $scoreValue   = (float) ($attempt->score ?? 0);
    $scoreMax     = max(1, $questions->sum(fn($q) => $q->scoreWeight()));
    $scorePercent = round(($scoreValue / $scoreMax) * 100);
    $scoreDisplay = rtrim(rtrim(number_format($scoreValue, 2, '.', ''), '0'), '.');

    // Grade berbasis persentase dari total bobot nilai.
    $grade = match(true) {
        $scorePercent >= 85 => ['label' => 'Sangat Baik', 'color' => '#10B981', 'bg' => '#ECFDF5'],
        $scorePercent >= 70 => ['label' => 'Baik',        'color' => '#2563EB', 'bg' => '#EFF6FF'],
        $scorePercent >= 50 => ['label' => 'Cukup',       'color' => '#F59E0B', 'bg' => '#FFFBEB'],
        default             => ['label' => 'Perlu Belajar','color' => '#EF4444', 'bg' => '#FEF2F2'],
    };
@endphp

{{-- Header --}}
<div style="margin-bottom:28px;">
    <div style="display:flex; align-items:center; gap:10px; margin-bottom:4px;">
        <a href="{{ route('student.tryout.index') }}"
           style="font-size:13px; color:var(--text-muted); text-decoration:none; display:flex; align-items:center; gap:6px;">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Tryout
        </a>
    </div>
    <h2 style="font-size:22px; font-weight:800;">Hasil Tryout</h2>
    <p style="font-size:13px; color:var(--text-muted); margin-top:2px;">{{ $tryout->title }}</p>
</div>

<div style="display:grid; grid-template-columns:1fr 320px; gap:20px; align-items:start;">

    {{-- LEFT --}}
    <div>

        {{-- Score card --}}
        <div style="background:linear-gradient(135deg,#1E293B,#1D4ED8); border-radius:16px; padding:32px; margin-bottom:20px; position:relative; overflow:hidden;">
            <div style="position:absolute; top:-40px; right:-40px; width:200px; height:200px; background:rgba(255,255,255,0.05); border-radius:50%;"></div>
            <div style="position:absolute; bottom:-60px; right:60px; width:140px; height:140px; background:rgba(255,255,255,0.03); border-radius:50%;"></div>

            <div style="display:grid; grid-template-columns:auto 1fr; gap:32px; align-items:center; position:relative;">
                {{-- Big score --}}
                <div style="text-align:center;">
                    <div style="font-size:64px; font-weight:800; color:#fff; line-height:1;">
                        {{ $scoreDisplay }}
                    </div>
                    <div style="font-size:13px; color:rgba(255,255,255,0.6); margin-top:4px;">Skor Total dari {{ rtrim(rtrim(number_format($scoreMax, 2, ',', '.'), '0'), ',') }} poin</div>
                    <div style="display:inline-block; margin-top:10px; padding:5px 14px; background:{{ $grade['bg'] }}; color:{{ $grade['color'] }}; border-radius:20px; font-size:12px; font-weight:700;">
                        {{ $grade['label'] }}
                    </div>
                </div>

                {{-- Stats --}}
                <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:14px;">
                    <div style="background:rgba(255,255,255,0.08); border-radius:10px; padding:14px; text-align:center;">
                        <div style="font-size:22px; font-weight:800; color:#34D399;">{{ $attempt->correct_count }}</div>
                        <div style="font-size:11px; color:rgba(255,255,255,0.5); margin-top:3px;">Benar</div>
                    </div>
                    <div style="background:rgba(255,255,255,0.08); border-radius:10px; padding:14px; text-align:center;">
                        <div style="font-size:22px; font-weight:800; color:#F87171;">{{ $attempt->wrong_count }}</div>
                        <div style="font-size:11px; color:rgba(255,255,255,0.5); margin-top:3px;">Salah</div>
                    </div>
                    <div style="background:rgba(255,255,255,0.08); border-radius:10px; padding:14px; text-align:center;">
                        <div style="font-size:22px; font-weight:800; color:#94A3B8;">{{ $attempt->empty_count }}</div>
                        <div style="font-size:11px; color:rgba(255,255,255,0.5); margin-top:3px;">Kosong</div>
                    </div>
                    <div style="background:rgba(255,255,255,0.08); border-radius:10px; padding:14px; text-align:center;">
                        <div style="font-size:22px; font-weight:800; color:#FBBF24;">{{ $attempt->duration() }}</div>
                        <div style="font-size:11px; color:rgba(255,255,255,0.5); margin-top:3px;">Menit</div>
                    </div>
                </div>
            </div>
        </div>

        @include('student.tryout._score-comparison', ['attempt' => $attempt])

        @if(($attempt->tab_switch_count ?? 0) > 0)
        {{-- Catatan integritas: siswa meninggalkan jendela saat tryout --}}
        <div class="card" style="margin-bottom:20px; border-left:3px solid #F59E0B; background:#FFFBEB;">
            <div style="display:flex; align-items:center; gap:10px;">
                <i class="fas fa-triangle-exclamation" style="color:#D97706; font-size:18px;"></i>
                <div>
                    <div style="font-size:14px; font-weight:700; color:#92400E;">Catatan Integritas</div>
                    <div style="font-size:13px; color:#B45309; margin-top:2px;">
                        Kamu tercatat meninggalkan jendela tryout sebanyak
                        <strong>{{ $attempt->tab_switch_count }}x</strong> selama mengerjakan.
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Skor Berbobot Kesulitan --}}
        <div class="card" style="margin-bottom:20px;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                <div style="font-size:15px; font-weight:700;">
                    <i class="fas fa-scale-balanced" style="color:#7C3AED; margin-right:6px;"></i>
                    Skor Berbobot Kesulitan
                </div>
                <span style="font-size:11px; background:#F3E8FF; color:#7C3AED; padding:3px 10px; border-radius:20px; font-weight:700;">Info Tambahan</span>
            </div>
            <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:14px;">
                <div style="background:#FAF5FF; border:1px solid #E9D5FF; border-radius:10px; padding:16px; text-align:center;">
                    <div style="font-size:30px; font-weight:800; color:#7C3AED; line-height:1;">{{ number_format($attempt->weighted_score ?? 0, 1) }}</div>
                    <div style="font-size:11px; color:#9333EA; margin-top:4px;">dari 100 (bobot kesulitan)</div>
                </div>
                <div style="background:#FAF5FF; border:1px solid #E9D5FF; border-radius:10px; padding:16px; text-align:center;">
                    <div style="font-size:30px; font-weight:800; color:#7C3AED; line-height:1;">#{{ $weightedRank ?? '-' }}</div>
                    <div style="font-size:11px; color:#9333EA; margin-top:4px;">peringkat versi bobot</div>
                </div>
            </div>
            <p style="font-size:12px; color:var(--text-muted); margin-top:12px; line-height:1.6;">
                Skor ini memberi nilai lebih besar untuk soal yang sulit (sedikit peserta yang menjawab benar).
                Dua siswa dengan jumlah benar sama bisa berbeda skor di sini.
                <strong>Peringkat resmi tetap memakai skor total.</strong>
            </p>
        </div>

        {{-- Per subject stats --}}
        <div class="card" style="margin-bottom:20px;">
            <div style="font-size:15px; font-weight:700; margin-bottom:16px;">Performa Per Mata Pelajaran</div>
            @foreach($subjectStats as $subject => $stat)
            @php $pct = $stat['total'] > 0 ? round($stat['earned'] / $stat['total'] * 100) : 0; @endphp
            <div style="margin-bottom:14px;">
                <div style="display:flex; justify-content:space-between; font-size:13px; margin-bottom:6px;">
                    <span style="font-weight:600;">{{ $subject }}</span>
                    <span style="color:var(--text-muted);">{{ rtrim(rtrim(number_format($stat['earned'], 2, ',', '.'), '0'), ',') }}/{{ rtrim(rtrim(number_format($stat['total'], 2, ',', '.'), '0'), ',') }} poin ({{ $pct }}%)</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-bar-fill" style="width:{{ $pct }}%; background:{{ $pct >= 70 ? '#10B981' : ($pct >= 50 ? '#F59E0B' : '#EF4444') }};"></div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pembahasan Soal --}}
        <div class="card">
            <div style="font-size:15px; font-weight:700; margin-bottom:16px;">Pembahasan Soal</div>

            @foreach($questions as $index => $question)
            @php
                $userAnswer = $answers[$question->id] ?? null;

                // Normalisasi: jawaban bisa string (single), array numerik (multiple), atau array assoc row=>kategori (matrix).
                $pickedArr = [];
                if ($question->isMatrix()) {
                    $pickedArr = is_array($userAnswer)
                        ? array_filter($userAnswer, fn($v) => $v !== null && $v !== '')
                        : [];
                } else {
                    $pickedArr = is_array($userAnswer)
                        ? array_map(fn($v) => strtolower((string) $v), $userAnswer)
                        : ($userAnswer !== null && $userAnswer !== '' ? [strtolower((string) $userAnswer)] : []);
                }

                $gradeRes   = count($pickedArr) ? $question->grade($userAnswer, $question->scoreWeight()) : ['status' => 'empty', 'earned' => 0, 'max' => $question->scoreWeight()];
                $isCorrect  = ($gradeRes['status'] ?? '') === 'correct';
                $isPartial  = ($gradeRes['status'] ?? '') === 'partial';
                $isEmpty    = count($pickedArr) === 0;

                $keysArr     = $question->correctKeys();
                $pickedLabel = $question->answerLabel($userAnswer);
                $kunciLabel  = $question->correctAnswerLabel();
            @endphp
            <div style="border:1px solid var(--border); border-radius:10px; margin-bottom:12px; overflow:hidden;">
                {{-- Question header --}}
                <div style="padding:12px 16px; background:{{ $isCorrect ? '#ECFDF5' : ($isPartial ? '#FFFBEB' : ($isEmpty ? '#F8FAFC' : '#FEF2F2')) }}; display:flex; align-items:center; justify-content:space-between;">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <span style="width:28px; height:28px; border-radius:6px; background:{{ $isCorrect ? '#10B981' : ($isPartial ? '#F59E0B' : ($isEmpty ? '#94A3B8' : '#EF4444')) }}; color:#fff; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; flex-shrink:0;">
                            {{ $index + 1 }}
                        </span>
                        <span style="font-size:13px; font-weight:600; color:{{ $isCorrect ? '#065F46' : ($isPartial ? '#92400E' : ($isEmpty ? '#64748B' : '#991B1B')) }};">
                            @if($isCorrect) <i class="fas fa-check-circle"></i> Benar
                            @elseif($isPartial) <i class="fas fa-adjust"></i> Partial
                            @elseif($isEmpty) <i class="fas fa-minus-circle"></i> Tidak Dijawab
                            @else <i class="fas fa-times-circle"></i> Salah
                            @endif
                            <small style="color:var(--text-muted); margin-left:6px;">({{ rtrim(rtrim(number_format($gradeRes['earned'] ?? 0, 2, ',', '.'), '0'), ',') }}/{{ rtrim(rtrim(number_format($gradeRes['max'] ?? $question->scoreWeight(), 2, ',', '.'), '0'), ',') }} poin)</small>
                        </span>
                    </div>
                    <div style="font-size:12px; color:var(--text-muted); text-align:right;">
                        <div>
                            Jawaban kamu: <strong>{{ $pickedLabel }}</strong>
                            &nbsp;·&nbsp;
                            Kunci: <strong style="color:#10B981;">{{ $kunciLabel }}</strong>
                        </div>
                        <div style="margin-top:4px; display:flex; gap:6px; justify-content:flex-end; flex-wrap:wrap;">
                            @php
                                $diffColor = match($question->difficulty) {
                                    'sulit'  => ['#FEF2F2','#DC2626'],
                                    'sedang' => ['#FFFBEB','#D97706'],
                                    default  => ['#ECFDF5','#059669'],
                                };
                            @endphp
                            <span style="background:{{ $diffColor[0] }}; color:{{ $diffColor[1] }}; padding:2px 8px; border-radius:6px; font-weight:700; font-size:11px;">
                                {{ ucfirst($question->difficulty) }}
                            </span>
                            <span style="background:#EEF2FF; color:#4F46E5; padding:2px 8px; border-radius:6px; font-weight:700; font-size:11px;">Bobot nilai {{ rtrim(rtrim(number_format($question->scoreWeight(), 2, ',', '.'), '0'), ',') }}</span>
                            @if($question->answered_count > 0)
                                <span style="background:#F1F5F9; color:#475569; padding:2px 8px; border-radius:6px; font-weight:700; font-size:11px;"
                                      title="Persentase peserta yang menjawab benar">
                                    {{ number_format($question->correct_rate, 0) }}% benar
                                </span>
                                <span style="background:#F3E8FF; color:#7C3AED; padding:2px 8px; border-radius:6px; font-weight:700; font-size:11px;"
                                      title="Bobot kesulitan soal">
                                    bobot {{ number_format($question->difficultyWeight(), 2) }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Collapsible content --}}
                <div x-data="{ open: false }">
                    <button @click="open = !open"
                        style="width:100%; padding:10px 16px; background:transparent; border:none; cursor:pointer; text-align:left; font-size:13px; color:var(--text-muted); font-family:inherit; display:flex; justify-content:space-between; align-items:center;">
                        <span>{{ Str::limit($question->question_text, 80) }}</span>
                        <i class="fas fa-chevron-down" :style="open ? 'transform:rotate(180deg)' : ''" style="transition:transform 0.2s; font-size:11px;"></i>
                    </button>

                    <div x-show="open" style="padding:16px; border-top:1px solid var(--border);">
                        @if($question->passage)
                            <div style="background:#EFF6FF; border:1px solid #BFDBFE; border-radius:10px; padding:14px; margin-bottom:14px;">
                                <div style="font-size:12px; font-weight:800; color:#1D4ED8; margin-bottom:8px;"><i class="fas fa-book-open"></i> Soal Cerita / Stimulus</div>
                                @if($question->passage->title)
                                    <div style="font-size:15px; font-weight:800; text-align:center; margin-bottom:10px; color:#0F172A;">{{ $question->passage->title }}</div>
                                @endif
                                @if($question->passage->passage_image)
                                    <img src="{{ asset($question->passage->passage_image) }}" alt="Gambar stimulus" style="max-width:100%; max-height:420px; object-fit:contain; display:block; margin:10px auto; border:1px solid #E2E8F0; border-radius:10px; background:#fff;">
                                @endif
                                @if($question->passage->passage_text)
                                    <div style="font-size:13.5px; color:#334155; line-height:1.75; white-space:pre-wrap;">{{ $question->passage->passage_text }}</div>
                                @endif
                                @if($question->passage->source)
                                    <div style="font-size:12px; color:#64748B; margin-top:8px;">Sumber: {{ $question->passage->source }}</div>
                                @endif
                            </div>
                        @endif
                        @if($question->question_image)
                            <img src="{{ asset($question->question_image) }}" alt="Gambar soal" style="max-width:100%; max-height:420px; object-fit:contain; display:block; margin:0 auto 14px; border:1px solid #E2E8F0; border-radius:10px; background:#fff;">
                        @endif
                        <p style="font-size:14px; color:var(--text-main); margin-bottom:14px; line-height:1.7; white-space:pre-wrap;">{{ $question->question_text }}</p>

                        {{-- Options --}}
                        @if($question->isMatrix())
                            @php
                                $columns = $question->matrixColumns();
                                $matrixKeys = $question->matrixCorrectAnswers();
                                $pickedMap = is_array($userAnswer) ? $userAnswer : [];
                            @endphp
                            <table style="width:100%; border-collapse:collapse; margin-bottom:10px; font-size:13px;">
                                <thead>
                                    <tr>
                                        <th style="background:#DBEAFE; color:#1E3A8A; padding:10px; border:1px solid #93C5FD; text-align:left;">Pernyataan / Teknik</th>
                                        @foreach($columns as $columnLabel)
                                            <th style="background:#DBEAFE; color:#1E3A8A; padding:10px; border:1px solid #93C5FD; text-align:center;">{{ $columnLabel }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($question->options() as $rowKey => $rowText)
                                        <tr>
                                            <td style="padding:10px; border:1px solid #E2E8F0; color:#334155;">{{ $rowText }}</td>
                                            @foreach($columns as $columnKey => $columnLabel)
                                                @php
                                                    $isKey = ($matrixKeys[$rowKey] ?? null) === $columnKey;
                                                    $isPicked = ($pickedMap[$rowKey] ?? null) === $columnKey;
                                                    $wrongPick = $isPicked && !$isKey;
                                                @endphp
                                                <td style="padding:10px; border:1px solid #E2E8F0; text-align:center; background:{{ $isKey ? '#ECFDF5' : ($wrongPick ? '#FEF2F2' : '#fff') }};">
                                                    @if($isKey)
                                                        <i class="fas fa-check-circle" style="color:#10B981;"></i>
                                                    @elseif($wrongPick)
                                                        <i class="fas fa-times-circle" style="color:#EF4444;"></i>
                                                    @elseif($isPicked)
                                                        <i class="fas fa-circle" style="color:#2563EB;"></i>
                                                    @else
                                                        <span style="color:#CBD5E1;">○</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            @foreach($question->options() as $key => $text)
                            @php
                                $isKey    = in_array($key, $keysArr, true);
                                $isPicked = in_array($key, $pickedArr, true);
                                $wrongPick = $isPicked && !$isKey;
                            @endphp
                            <div style="padding:10px 14px; border-radius:8px; margin-bottom:6px; display:flex; align-items:flex-start; gap:10px;
                                background:{{ $isKey ? '#ECFDF5' : ($wrongPick ? '#FEF2F2' : '#F8FAFC') }};
                                border:1px solid {{ $isKey ? '#6EE7B7' : ($wrongPick ? '#FECACA' : '#E2E8F0') }};">
                                <span style="width:24px; height:24px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; flex-shrink:0;
                                    background:{{ $isKey ? '#10B981' : ($isPicked ? '#EF4444' : '#E2E8F0') }};
                                    color:{{ ($isKey || $isPicked) ? '#fff' : '#64748B' }};">
                                    {{ strtoupper($key) }}
                                </span>
                                <span style="font-size:13px; color:var(--text-main); padding-top:3px;">{{ $text }}</span>
                            </div>
                            @endforeach
                        @endif

                        {{-- Explanation --}}
                        @if($question->explanation)
                        <div style="margin-top:14px; padding:14px; background:#EFF6FF; border-radius:8px; border-left:3px solid #2563EB;">
                            <div style="font-size:12px; font-weight:700; color:#2563EB; margin-bottom:6px;"><i class="fas fa-lightbulb"></i> Pembahasan</div>
                            <p style="font-size:13.5px; color:#1E293B; line-height:1.7;">{{ $question->explanation }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

    </div>

    {{-- RIGHT --}}
    <div>
        {{-- Ranking --}}
        <div class="card" style="margin-bottom:16px; text-align:center;">
            <div style="font-size:13px; color:var(--text-muted); margin-bottom:8px;">Peringkat Kamu</div>
            <div style="font-size:48px; font-weight:800; color:var(--text-main); line-height:1;">
                #{{ $attempt->rank_at_submit ?? '-' }}
            </div>
            <div style="font-size:13px; color:var(--text-muted); margin-top:4px;">dari {{ number_format($totalParticipants) }} peserta</div>
            <div style="margin-top:12px; padding:8px; background:#EFF6FF; border-radius:8px;">
                <div style="font-size:13px; font-weight:700; color:#2563EB;">Persentil {{ $percentile }}%</div>
                <div style="font-size:11px; color:#64748B; margin-top:2px;">Lebih baik dari {{ $percentile }}% peserta</div>
            </div>
        </div>

        {{-- Akurasi --}}
        <div class="card" style="margin-bottom:16px;">
            <div style="font-size:13px; font-weight:700; margin-bottom:12px;">Ringkasan Hasil</div>
            @php $accuracy = $scorePercent; @endphp
            <div style="text-align:center; margin-bottom:14px;">
                <div style="font-size:32px; font-weight:800; color:{{ $accuracy >= 70 ? '#10B981' : ($accuracy >= 50 ? '#F59E0B' : '#EF4444') }};">{{ $accuracy }}%</div>
                <div style="font-size:12px; color:var(--text-muted);">Pencapaian Skor</div>
            </div>
            <div class="progress-bar" style="margin-bottom:16px;">
                <div class="progress-bar-fill" style="width:{{ $accuracy }}%; background:{{ $accuracy >= 70 ? '#10B981' : ($accuracy >= 50 ? '#F59E0B' : '#EF4444') }};"></div>
            </div>
            <div style="display:flex; justify-content:space-around; font-size:12px; text-align:center;">
                <div>
                    <div style="font-size:18px; font-weight:800; color:#10B981;">{{ $attempt->correct_count }}</div>
                    <div style="color:var(--text-muted);">Benar</div>
                </div>
                <div>
                    <div style="font-size:18px; font-weight:800; color:#EF4444;">{{ $attempt->wrong_count }}</div>
                    <div style="color:var(--text-muted);">Salah</div>
                </div>
                <div>
                    <div style="font-size:18px; font-weight:800; color:#94A3B8;">{{ $attempt->empty_count }}</div>
                    <div style="color:var(--text-muted);">Kosong</div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div style="display:flex; flex-direction:column; gap:10px;">
            <a href="{{ route('student.tryout.start', $tryout->id) }}"
               class="btn btn-primary" style="justify-content:center; width:100%;">
                <i class="fas fa-redo"></i> Ulangi Tryout
            </a>
            <a href="{{ route('student.tryout.index') }}"
               class="btn btn-outline" style="justify-content:center; width:100%;">
                <i class="fas fa-list"></i> Tryout Lainnya
            </a>
        </div>
    </div>

</div>

@endsection

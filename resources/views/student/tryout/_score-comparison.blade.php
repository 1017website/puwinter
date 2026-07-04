{{--
    Partial: Perbandingan Skor Regular vs IRT + rincian perhitungan.
    Cara pakai (di student/tryout/result.blade.php, dalam kolom LEFT):
        @include('student.tryout._score-comparison', ['attempt' => $attempt])

    Kartu hanya tampil bila tryout memakai mode 'irt'.
--}}
@php
    $t          = $attempt->tryout;
    $isIrt      = ($t->scoring_mode ?? 'regular') === 'irt';
    $calibrated = (bool) ($t->irt_calibrated ?? false);
    $regScore   = (float) $attempt->score;
    $irtScore   = $attempt->irt_score;
    $answers    = $attempt->answers ?? [];
@endphp

@if($isIrt)
<div class="card" style="margin-bottom:20px;">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
        <div style="font-size:15px; font-weight:700;">
            <i class="fas fa-scale-balanced" style="color:#0EA5E9; margin-right:6px;"></i>
            Perbandingan Skor: Regular vs IRT
        </div>
        <span style="font-size:11px; background:#E0F2FE; color:#0369A1; padding:3px 10px; border-radius:20px; font-weight:700;">
            Mode IRT
        </span>
    </div>

    @if(!$calibrated || is_null($irtScore))
        {{-- Belum dikalibrasi: skor IRT belum tersedia --}}
        <div style="background:#FFFBEB; border:1px solid #FDE68A; border-radius:10px; padding:14px; font-size:13px; color:#92400E;">
            <i class="fas fa-hourglass-half" style="margin-right:6px;"></i>
            Skor IRT belum tersedia. Bobot kesulitan dihitung setelah seluruh
            peserta selesai dan admin menjalankan kalibrasi. Sementara ini skor
            memakai perhitungan <strong>Regular</strong>.
        </div>
    @else
        {{-- Dua kartu skor bersanding --}}
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:8px;">
            <div style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:12px; padding:16px; text-align:center;">
                <div style="font-size:11px; color:#64748B; font-weight:600; text-transform:uppercase; letter-spacing:.5px;">Regular</div>
                <div style="font-size:32px; font-weight:800; color:#334155; line-height:1.1; margin-top:6px;">
                    {{ rtrim(rtrim(number_format($regScore, 2, ',', '.'), '0'), ',') }}
                </div>
                <div style="font-size:11px; color:#94A3B8; margin-top:4px;">mengikuti bobot nilai per soal; multiple partial</div>
            </div>
            <div style="background:linear-gradient(135deg,#0EA5E9,#2563EB); border-radius:12px; padding:16px; text-align:center;">
                <div style="font-size:11px; color:rgba(255,255,255,.75); font-weight:600; text-transform:uppercase; letter-spacing:.5px;">IRT (dipakai)</div>
                <div style="font-size:32px; font-weight:800; color:#fff; line-height:1.1; margin-top:6px;">
                    {{ number_format($irtScore, 1) }}
                </div>
                <div style="font-size:11px; color:rgba(255,255,255,.75); margin-top:4px;">skala 0&ndash;100</div>
            </div>
        </div>

        {{-- Toggle rincian perhitungan --}}
        <details style="margin-top:12px;">
            <summary style="cursor:pointer; font-size:13px; font-weight:600; color:#0369A1; user-select:none;">
                <i class="fas fa-calculator" style="margin-right:6px;"></i>Lihat cara perhitungan IRT
            </summary>

            <div style="margin-top:12px; font-size:13px; color:#475569; line-height:1.6;">
                <p style="margin-bottom:10px;">
                    IRT menilai dari <strong>bobot kesulitan tiap soal</strong>, bukan
                    sekadar jumlah benar. Soal yang sedikit dijawab benar = lebih sulit =
                    bobot lebih besar. Salah dan kosong sama-sama bernilai 0 (tanpa penalti).
                </p>

                <div style="background:#F1F5F9; border-radius:10px; padding:12px; font-family:monospace; font-size:12px; color:#334155; margin-bottom:12px;">
                    p&nbsp;&nbsp;= benar &divide; total peserta<br>
                    b&nbsp;&nbsp;= ln((1 &minus; p) &divide; p)&nbsp;&nbsp;<span style="color:#94A3B8;">// tingkat kesulitan</span><br>
                    bobot = (1 &minus; p) &divide; &Sigma;(1 &minus; p) &times; 100<br>
                    Skor IRT = &Sigma; bobot soal yang <strong>benar</strong>
                </div>

                {{-- Tabel rincian per soal --}}
                <div style="overflow-x:auto;">
                    <table style="width:100%; border-collapse:collapse; font-size:12px;">
                        <thead>
                            <tr style="background:#F8FAFC; text-align:left;">
                                <th style="padding:8px; border-bottom:1px solid #E2E8F0;">No</th>
                                <th style="padding:8px; border-bottom:1px solid #E2E8F0; text-align:center;">p (benar)</th>
                                <th style="padding:8px; border-bottom:1px solid #E2E8F0; text-align:center;">b</th>
                                <th style="padding:8px; border-bottom:1px solid #E2E8F0; text-align:center;">Bobot</th>
                                <th style="padding:8px; border-bottom:1px solid #E2E8F0; text-align:center;">Jawaban</th>
                                <th style="padding:8px; border-bottom:1px solid #E2E8F0; text-align:right;">Poin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($t->questions as $i => $q)
                                @php
                                    $ua      = $answers[$q->id] ?? null;
                                    $res     = $ua !== null ? $q->grade($ua, $q->scoreWeight()) : ['status' => 'empty'];
                                    $benar   = ($res['status'] ?? '') === 'correct';
                                    $poin    = $benar ? (float) ($q->irt_weight ?? 0) : 0.0;
                                    $pPersen = is_null($q->correct_rate) ? null : (float) $q->correct_rate;
                                    $badge   = $benar
                                        ? ['t' => 'Benar',  'c' => '#059669', 'bg' => '#ECFDF5']
                                        : (($res['status'] ?? '') === 'empty'
                                            ? ['t' => 'Kosong', 'c' => '#64748B', 'bg' => '#F1F5F9']
                                            : ['t' => 'Salah',  'c' => '#DC2626', 'bg' => '#FEF2F2']);
                                @endphp
                                <tr>
                                    <td style="padding:8px; border-bottom:1px solid #F1F5F9;">{{ $i + 1 }}</td>
                                    <td style="padding:8px; border-bottom:1px solid #F1F5F9; text-align:center;">
                                        {{ is_null($pPersen) ? '-' : number_format($pPersen, 1) . '%' }}
                                    </td>
                                    <td style="padding:8px; border-bottom:1px solid #F1F5F9; text-align:center;">
                                        {{ is_null($q->irt_b) ? '-' : number_format($q->irt_b, 2) }}
                                    </td>
                                    <td style="padding:8px; border-bottom:1px solid #F1F5F9; text-align:center; font-weight:600;">
                                        {{ is_null($q->irt_weight) ? '-' : number_format($q->irt_weight, 2) }}
                                    </td>
                                    <td style="padding:8px; border-bottom:1px solid #F1F5F9; text-align:center;">
                                        <span style="font-size:11px; padding:2px 8px; border-radius:12px; font-weight:600; background:{{ $badge['bg'] }}; color:{{ $badge['c'] }};">
                                            {{ $badge['t'] }}
                                        </span>
                                    </td>
                                    <td style="padding:8px; border-bottom:1px solid #F1F5F9; text-align:right; font-weight:700; color:{{ $benar ? '#0369A1' : '#94A3B8' }};">
                                        {{ number_format($poin, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" style="padding:10px 8px; text-align:right; font-weight:700; color:#334155;">Total Skor IRT</td>
                                <td style="padding:10px 8px; text-align:right; font-weight:800; color:#0369A1;">{{ number_format($irtScore, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <p style="margin-top:10px; font-size:12px; color:#94A3B8;">
                    Keterangan: p = proporsi peserta menjawab benar, b = kesulitan
                    (makin besar makin sulit), bobot = poin maksimal soal. Total bobot
                    seluruh soal = 100.
                </p>
            </div>
        </details>
    @endif
</div>
@endif

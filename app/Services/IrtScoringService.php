<?php

namespace App\Services;

use App\Models\Tryout;
use App\Models\UserTryoutAttempt;
use Illuminate\Support\Facades\DB;

/**
 * Penilaian tryout berbasis Item Response Theory (model Rasch / 1PL).
 *
 * Alur:
 *  1. Peserta mengerjakan tryout mode 'irt'. Skor sementara memakai `regular`
 *     (belum bisa IRT karena bobot butuh data seluruh peserta).
 *  2. Setelah tryout ditutup, admin menjalankan calibrate():
 *      - p (proporsi benar) per soal dari seluruh attempt selesai.
 *      - b = ln((1-p)/p)  -> parameter kesulitan.
 *      - bobot ternormalisasi 0..100 berbanding lurus tingkat kesulitan (1-p).
 *  3. rescoreAll() menghitung ulang irt_score tiap attempt & rank_at_submit.
 *
 * Catatan: dalam IRT dikotomis, jawaban SALAH dan KOSONG sama-sama = 0
 * (tidak ada penalti minus). Untuk soal multiple, dihitung benar hanya bila
 * seluruh kunci tepat (grade() status 'correct').
 */
class IrtScoringService
{
    /** Batas p agar ln tidak meledak saat semua benar / semua salah. */
    private const P_MIN = 0.01;
    private const P_MAX = 0.99;

    /**
     * Hitung p, b, dan bobot ternormalisasi tiap soal lalu simpan.
     */
    public function calibrate(Tryout $tryout): void
    {
        $questions = $tryout->questions()->get();
        $attempts  = $tryout->attempts()->whereNotNull('submitted_at')->get();
        $totalPeserta = max(1, $attempts->count());

        // --- Hitung jumlah benar per soal ---
        $benarPerSoal = [];
        foreach ($questions as $q) {
            $benarPerSoal[$q->id] = 0;
        }

        foreach ($attempts as $att) {
            $answers = $att->answers ?? [];
            foreach ($questions as $q) {
                $userAnswer = $answers[$q->id] ?? null;
                if ($userAnswer === null) {
                    continue; // kosong = 0
                }
                $result = $q->grade($userAnswer);
                if (($result['status'] ?? '') === 'correct') {
                    $benarPerSoal[$q->id]++;
                }
            }
        }

        // --- p, b, dan tingkat kesulitan (1-p) ---
        $difficulty    = [];
        $sumDifficulty = 0.0;

        foreach ($questions as $q) {
            $p = $benarPerSoal[$q->id] / $totalPeserta;
            $p = min(self::P_MAX, max(self::P_MIN, $p));

            $b = log((1 - $p) / $p);   // Rasch
            $d = 1 - $p;               // makin sulit makin besar

            $difficulty[$q->id] = $d;
            $sumDifficulty     += $d;

            // correct_rate disimpan skala 0..100 (konsisten dgn sistem existing)
            $q->correct_rate   = round($p * 100, 2);
            $q->answered_count = $totalPeserta;
            $q->irt_b          = round($b, 4);
        }

        $sumDifficulty = max(1e-9, $sumDifficulty);

        // --- Bobot ternormalisasi 0..100 ---
        foreach ($questions as $q) {
            $q->irt_weight = round(($difficulty[$q->id] / $sumDifficulty) * 100, 2);
            $q->save();
        }

        $tryout->forceFill(['irt_calibrated' => true])->save();
    }

    /**
     * Skor satu attempt dengan bobot IRT. Skala 0..100.
     * (jumlah irt_weight semua soal = 100, jadi skor = % bobot yg dijawab benar)
     */
    public function score(UserTryoutAttempt $attempt): float
    {
        $tryout  = $attempt->tryout()->with('questions')->first();
        $answers = $attempt->answers ?? [];
        $total   = 0.0;

        foreach ($tryout->questions as $q) {
            $userAnswer = $answers[$q->id] ?? null;
            if ($userAnswer === null) {
                continue;
            }
            $result = $q->grade($userAnswer);
            if (($result['status'] ?? '') === 'correct') {
                $total += (float) ($q->irt_weight ?? 0);
            }
            // salah / partial / kosong = 0
        }

        return round($total, 2);
    }

    /**
     * Skor IRT langsung dari array jawaban (dipakai saat submit, sebelum
     * attempt tersimpan). $answers = [question_id => jawaban].
     */
    public function scoreFromAnswers(Tryout $tryout, array $answers): float
    {
        $tryout->loadMissing('questions');
        $total = 0.0;

        foreach ($tryout->questions as $q) {
            $userAnswer = $answers[$q->id] ?? null;
            if ($userAnswer === null) {
                continue;
            }
            $result = $q->grade($userAnswer);
            if (($result['status'] ?? '') === 'correct') {
                $total += (float) ($q->irt_weight ?? 0);
            }
        }

        return round($total, 2);
    }

    /**
     * Re-score semua attempt selesai & perbarui peringkat berbasis irt_score.
     * Mengembalikan jumlah attempt yang diperbarui.
     */
    public function rescoreAll(Tryout $tryout): int
    {
        $attempts = $tryout->attempts()->whereNotNull('submitted_at')->get();

        // Hitung skor dulu
        $scores = [];
        foreach ($attempts as $att) {
            $scores[$att->id] = $this->score($att);
        }

        // Ranking berdasarkan irt_score (desc)
        arsort($scores);
        $rank = 0;
        $updated = 0;
        foreach ($scores as $attemptId => $irtScore) {
            $rank++;
            DB::table('user_tryout_attempts')
                ->where('id', $attemptId)
                ->update([
                    'irt_score'      => $irtScore,
                    'rank_at_submit' => $rank,
                    'updated_at'     => now(),
                ]);
            $updated++;
        }

        return $updated;
    }
}

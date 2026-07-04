<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LeaderboardScore;
use App\Models\StudyHistory;
use App\Models\Tryout;
use App\Models\UserTryoutAttempt;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TryoutController extends Controller
{
    // Daftar tryout
    public function index(Request $request): View
    {
        $filter = $request->get('filter', '');

        $query = Tryout::published()
            ->forUser($request->user())
            ->with(['subject', 'attempts' => fn($q) => $q->where('user_id', auth()->id())]);

        if ($filter === 'gratis') {
            $query->free();
        } elseif ($filter === 'premium') {
            $query->premium();
        }

        $tryouts = $query->get();

        // Stats real dari DB
        $totalSoal      = \App\Models\TryoutQuestion::count();
        $soalDiselesaikan = \App\Models\UserTryoutAttempt::where('user_id', auth()->id())
            ->whereNotNull('submitted_at')
            ->withSum('tryout', 'total_questions') // tidak bisa langsung, pakai alternatif
            ->count(); // jumlah attempt selesai

        // Hitung total soal yang sudah dijawab user (dari attempts)
        $soalDijawab = \App\Models\UserTryoutAttempt::where('user_id', auth()->id())
            ->whereNotNull('submitted_at')
            ->selectRaw('SUM(correct_count + wrong_count + empty_count) as total')
            ->value('total') ?? 0;

        $totalPeserta = \App\Models\UserTryoutAttempt::whereNotNull('submitted_at')
            ->distinct('user_id')->count('user_id');

        $stats = [
            'total_soal'      => $totalSoal,
            'soal_dijawab'    => $soalDijawab,
            'total_tryout'    => Tryout::published()->count(),
            'total_peserta'   => $totalPeserta,
        ];

        return view('student.tryout.index', compact('tryouts', 'filter', 'stats'));
    }

    // Mulai tryout — buat attempt record
    public function start(Request $request, int $id): View|RedirectResponse
    {
        $tryout = Tryout::published()->with('questions.subject')->findOrFail($id);

        // Cek akses kelas/grade
        if (!$request->user()->canAccessGradeId($tryout->grade_id)) {
            $gradeName = optional($tryout->grade)->name ?? $tryout->grade ?? 'tertentu';
            return redirect()->route('student.tryout.index')
                ->with('error', 'Tryout ini untuk kelas ' . $gradeName . ', tidak tersedia untuk kelasmu.');
        }

        // Cek akses: tryout premium hanya untuk user premium
        if ($tryout->is_premium && !$request->user()->isPremium()) {
            return redirect()->route('upgrade.index')
                ->with('error', 'Tryout ini hanya tersedia untuk member Premium.');
        }

        // Cek apakah ada attempt yang belum selesai
        $existingAttempt = UserTryoutAttempt::where('user_id', $request->user()->id)
            ->where('tryout_id', $tryout->id)
            ->whereNull('submitted_at')
            ->latest()
            ->first();

        if ($existingAttempt) {
            return view('student.tryout.exam', [
                'tryout' => $tryout,
                'attempt' => $existingAttempt,
            ]);
        }

        // Buat attempt baru
        $attempt = UserTryoutAttempt::create([
            'user_id' => $request->user()->id,
            'tryout_id' => $tryout->id,
            'started_at' => now(),
        ]);

        return view('student.tryout.exam', compact('tryout', 'attempt'));
    }

    // Submit jawaban
    public function submit(Request $request, int $attemptId): RedirectResponse
    {
        $attempt = UserTryoutAttempt::where('id', $attemptId)
            ->where('user_id', $request->user()->id)
            ->whereNull('submitted_at')
            ->firstOrFail();

        $tryout = $attempt->tryout()->with('questions')->first();
        $answers = $request->input('answers', []);
        $correct = 0;
        $wrong = 0;
        $empty = 0;
        $partial = 0;

        // Akumulator skor utama (mendukung partial credit untuk soal multiple).
        // Skala per soal: benar penuh = +4, partial = pecahan 0..4, salah = 0 lalu
        // dikenai penalti -1 (mengikuti aturan lama benar*4 - salah*1).
        $rawScore = 0.0;

        // Akumulator skor berbobot kesulitan (info tambahan).
        $weightedRaw = 0.0;
        $weightedMax = 0.0;

        $fullPoint  = 4.0;
        $penaltyPer = 1.0;

        foreach ($tryout->questions as $question) {
            $userAnswer = $answers[$question->id] ?? null;

            // bobot kesulitan soal saat ini (sebelum statistik diperbarui)
            $weight       = $question->difficultyWeight();
            $weightedMax += $weight;

            // Penilaian terpadu (single & multiple) lewat model.
            $result = $question->grade($userAnswer, $fullPoint, $penaltyPer);
            $status = $result['status'];   // correct | partial | wrong | empty
            $earned = (float) $result['earned'];

            if ($status === 'empty') {
                $empty++;
            } elseif ($status === 'correct') {
                $correct++;
                $rawScore   += $earned;                     // +4
                $weightedRaw += $weight;                    // bobot penuh
            } elseif ($status === 'partial') {
                $partial++;
                $rawScore   += $earned;                     // pecahan 0..4
                // bobot proporsional terhadap perolehan poin
                $weightedRaw += $weight * ($earned / $fullPoint);
            } else { // wrong
                $wrong++;
                $rawScore   -= $penaltyPer;                 // -1
            }

            // --- Perbarui statistik global soal (rolling, hanya yang dijawab) ---
            $isAnswered = !($status === 'empty');
            if ($isAnswered) {
                // Untuk correct_rate: soal dihitung "benar" hanya jika status correct penuh.
                $isThisCorrect = ($status === 'correct');

                $prevAnswered = (int) $question->answered_count;
                $prevRate     = (float) ($question->correct_rate ?? 0);
                $prevCorrect  = $prevRate / 100 * $prevAnswered;

                $newAnswered = $prevAnswered + 1;
                $newCorrect  = $prevCorrect + ($isThisCorrect ? 1 : 0);
                $newRate     = round($newCorrect / $newAnswered * 100, 2);

                DB::table('tryout_questions')
                    ->where('id', $question->id)
                    ->update([
                        'answered_count' => $newAnswered,
                        'correct_rate'   => $newRate,
                    ]);
            }
        }

        // Skor regular (penentu ranking mode regular). Tidak boleh negatif.
        $score = max(0, round($rawScore, 2));

        // Skor berbobot kesulitan (skala 0..100). Info saja, tidak untuk ranking.
        $weightedScore = $weightedMax > 0
            ? round($weightedRaw / $weightedMax * 100, 2)
            : 0.0;

        // --- Skor IRT ---
        // Bila tryout mode IRT & sudah dikalibrasi, hitung skor IRT sekarang.
        // Bila belum dikalibrasi, irt_score menyusul saat admin menjalankan
        // kalibrasi (bobot baru valid setelah semua peserta selesai).
        $irtScore = null;

        // Kolom penentu ranking: regular -> `score`, irt(terkalibrasi) -> `irt_score`.
        $rankColumn = 'score';
        $rankValue  = $score;

        if ($tryout->isIrt() && $tryout->irt_calibrated) {
            $irtScore   = app(\App\Services\IrtScoringService::class)->scoreFromAnswers($tryout, $answers);
            $rankColumn = 'irt_score';
            $rankValue  = $irtScore;
        }

        // Hitung rank sementara sesuai mode
        $rank = UserTryoutAttempt::where('tryout_id', $tryout->id)
            ->whereNotNull('submitted_at')
            ->where($rankColumn, '>', $rankValue)
            ->count() + 1;

        $attempt->update([
            'submitted_at' => now(),
            'answers' => $answers,
            'score' => $score,
            'correct_count' => $correct,
            'wrong_count' => $wrong,
            'empty_count' => $empty,
            'rank_at_submit' => $rank,
            'weighted_score' => $weightedScore,
            'irt_score' => $irtScore,
            'tab_switch_count' => (int) $request->input('tab_switch_count', 0),
        ]);

        // Skor yang ditampilkan & dipakai leaderboard mengikuti mode aktif.
        $displayScore = ($tryout->isIrt() && $irtScore !== null) ? $irtScore : $score;

        // Catat ke study history (skor mengikuti mode aktif)
        StudyHistory::create([
            'user_id' => $request->user()->id,
            'activity_type' => 'tryout',
            'reference_id' => $tryout->id,
            'reference_type' => Tryout::class,
            'duration_seconds' => now()->diffInSeconds($attempt->started_at),
            'score' => $displayScore,
        ]);

        // Update leaderboard (skor mengikuti mode aktif)
        $this->updateLeaderboard($request->user()->id, $tryout->subject_id, $displayScore);

        // Notifikasi hasil tryout
        $skorLabel = $tryout->isIrt() ? "Skor IRT kamu {$displayScore}" : "Skor kamu {$displayScore}";
        Notification::notify(
            $request->user()->id,
            'tryout',
            'Tryout selesai: ' . $tryout->title,
            "{$skorLabel} (benar {$correct}" . ($partial > 0 ? ", partial {$partial}" : "") . ", salah {$wrong}). Peringkat #{$rank}.",
            route('student.tryout.result', $attempt->id),
            'fa-clipboard-check'
        );

        return redirect()->route('student.tryout.result', $attempt->id);
    }

    // Hasil tryout
    public function result(Request $request, int $attemptId): View
    {
        $attempt = UserTryoutAttempt::where('id', $attemptId)
            ->where('user_id', $request->user()->id)
            ->whereNotNull('submitted_at')
            ->with(['tryout.questions.subject'])
            ->firstOrFail();

        $totalParticipants = UserTryoutAttempt::where('tryout_id', $attempt->tryout_id)
            ->whereNotNull('submitted_at')
            ->count();

        // Peringkat versi skor berbobot kesulitan (info tambahan)
        $weightedRank = UserTryoutAttempt::where('tryout_id', $attempt->tryout_id)
            ->whereNotNull('submitted_at')
            ->where('weighted_score', '>', $attempt->weighted_score ?? 0)
            ->count() + 1;

        return view('student.tryout.result', compact('attempt', 'totalParticipants', 'weightedRank'));
    }

    private function updateLeaderboard(int $userId, ?int $subjectId, float $score): void
    {
        // Catatan: tidak memakai updateOrCreate dengan DB::raw karena kolom
        // total_score punya cast 'float' — meneruskan Expression ke atribut
        // ber-cast akan error. Pakai query builder langsung (ambil skor tertinggi).
        $existing = LeaderboardScore::where('user_id', $userId)
            ->where('subject_id', $subjectId)
            ->first();

        if ($existing) {
            // Simpan hanya bila skor baru lebih tinggi (best score)
            if ($score > (float) $existing->total_score) {
                LeaderboardScore::where('id', $existing->id)->update([
                    'total_score' => $score,
                    'updated_at'  => now(),
                ]);
            }
        } else {
            LeaderboardScore::insert([
                'user_id'     => $userId,
                'subject_id'  => $subjectId,
                'total_score' => $score,
                'updated_at'  => now(),
            ]);
        }
    }
}

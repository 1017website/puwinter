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
        if (!$request->user()->canAccessGrade($tryout->grade)) {
            return redirect()->route('student.tryout.index')
                ->with('error', 'Tryout ini untuk kelas ' . $tryout->grade . ', tidak tersedia untuk kelasmu.');
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

        // Akumulator skor berbobot kesulitan (ditampilkan sebagai info tambahan)
        $weightedRaw = 0.0;   // total bobot yang berhasil diraih
        $weightedMax = 0.0;   // total bobot maksimum (jika semua benar)

        foreach ($tryout->questions as $question) {
            $userAnswer = $answers[$question->id] ?? null;

            // bobot kesulitan soal saat ini (sebelum diperbarui statistiknya)
            $weight       = $question->difficultyWeight();
            $weightedMax += $weight;

            $isThisCorrect = $userAnswer && $question->isCorrect($userAnswer);

            if (!$userAnswer) {
                $empty++;
            } elseif ($isThisCorrect) {
                $correct++;
                $weightedRaw += $weight; // soal sulit memberi poin lebih besar
            } else {
                $wrong++;
            }

            // --- Perbarui statistik global soal (rolling, hanya yang dijawab) ---
            if ($userAnswer) {
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

        // Hitung skor (benar x 4, salah -1, kosong 0) — TETAP penentu ranking
        $score = ($correct * 4) - ($wrong * 1);

        // Skor berbobot kesulitan (skala 0..100). Ditampilkan saja, tidak untuk ranking.
        // Dua siswa dengan jumlah benar sama bisa beda nilai: yang menjawab benar
        // soal-soal sulit akan punya weighted_score lebih tinggi.
        $weightedScore = $weightedMax > 0
            ? round($weightedRaw / $weightedMax * 100, 2)
            : 0.0;

        // Hitung rank sementara
        $rank = UserTryoutAttempt::where('tryout_id', $tryout->id)
            ->whereNotNull('submitted_at')
            ->where('score', '>', $score)
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
        ]);

        // Catat ke study history
        StudyHistory::create([
            'user_id' => $request->user()->id,
            'activity_type' => 'tryout',
            'reference_id' => $tryout->id,
            'reference_type' => Tryout::class,
            'duration_seconds' => now()->diffInSeconds($attempt->started_at),
            'score' => $score,
        ]);

        // Update leaderboard
        $this->updateLeaderboard($request->user()->id, $tryout->subject_id, $score);

        // Notifikasi hasil tryout
        Notification::notify(
            $request->user()->id,
            'tryout',
            'Tryout selesai: ' . $tryout->title,
            "Skor kamu {$score} (benar {$correct}, salah {$wrong}). Peringkat #{$rank}.",
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

<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LeaderboardScore;
use App\Models\StudyHistory;
use App\Models\Tryout;
use App\Models\UserTryoutAttempt;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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

        // Hitung total soal dari tryout yang sudah diselesaikan user.
        // Jangan menjumlah correct/wrong/empty karena soal partial punya skor pecahan.
        $soalDijawab = \App\Models\UserTryoutAttempt::where('user_tryout_attempts.user_id', auth()->id())
            ->whereNotNull('user_tryout_attempts.submitted_at')
            ->join('tryouts', 'tryouts.id', '=', 'user_tryout_attempts.tryout_id')
            ->sum('tryouts.total_questions');

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

        // Aturan skor baru:
        // - Single answer: benar = 1, salah/kosong = 0.
        // - Multiple answer: nilai proporsional sesuai jumlah kunci benar yang dipilih.
        //   Contoh 2 dari 4 kunci benar = 0.5.
        // - Tidak ada penalti minus.
        $rawScore = 0.0;
        $questionScores = [];

        // Akumulator skor berbobot kesulitan (info tambahan, skala 0..100).
        $weightedRaw = 0.0;
        $weightedMax = 0.0;

        $fullPoint  = 1.0;
        $penaltyPer = 0.0;

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
            } elseif ($status === 'partial') {
                $partial++;
            } else {
                $wrong++;
            }

            $rawScore += $earned;
            $weightedRaw += $weight * ($earned / $fullPoint);

            // Simpan nilai per soal agar hasil bisa diaudit/detail di UI.
            $questionScores[$question->id] = [
                'question_id' => (int) $question->id,
                'status' => $status,
                'score' => round($earned, 2),
                'max' => $fullPoint,
                'selected' => $result['selected'] ?? [],
                'correct_keys' => $result['correct_keys'] ?? $question->correctKeys(),
                'correct_selected' => (int) ($result['correct_selected'] ?? 0),
                'wrong_selected' => (int) ($result['wrong_selected'] ?? 0),
            ];

            // --- Perbarui statistik global soal (rolling, hanya yang dijawab) ---
            $isAnswered = !($status === 'empty');
            if ($isAnswered) {
                // Untuk correct_rate: soal dihitung benar jika mendapat skor penuh.
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

        // Skor akhir penentu ranking. Maksimum = jumlah soal.
        $score = round($rawScore, 2);

        // Skor berbobot kesulitan tetap skala 0..100 sebagai info tambahan.
        $weightedScore = $weightedMax > 0
            ? round($weightedRaw / $weightedMax * 100, 2)
            : 0.0;

        // Hitung rank sementara
        $rank = UserTryoutAttempt::where('tryout_id', $tryout->id)
            ->whereNotNull('submitted_at')
            ->where('score', '>', $score)
            ->count() + 1;

        $updateData = [
            'submitted_at' => now(),
            'answers' => $answers,
            'score' => $score,
            'correct_count' => $correct,
            'wrong_count' => $wrong,
            'empty_count' => $empty,
            'rank_at_submit' => $rank,
            'weighted_score' => $weightedScore,
            'tab_switch_count' => (int) $request->input('tab_switch_count', 0),
        ];

        // Guard agar tidak 500 jika file sudah ter-upload tapi migration belum dijalankan.
        if (Schema::hasColumn('user_tryout_attempts', 'question_scores')) {
            $updateData['question_scores'] = $questionScores;
        }

        $attempt->update($updateData);

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
            "Skor kamu {$score} (benar {$correct}" . ($partial > 0 ? ", partial {$partial}" : "") . ", salah {$wrong}, kosong {$empty}). Peringkat #{$rank}.",
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

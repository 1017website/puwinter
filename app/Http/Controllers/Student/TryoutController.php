<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LeaderboardScore;
use App\Models\StudyHistory;
use App\Models\Tryout;
use App\Models\UserTryoutAttempt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TryoutController extends Controller
{
    // Daftar tryout
    public function index(): View
    {
        $tryouts = Tryout::published()->with('subject')->get();

        return view('student.tryout.index', compact('tryouts'));
    }

    // Mulai tryout — buat attempt record
    public function start(Request $request, int $id): View|RedirectResponse
    {
        $tryout = Tryout::published()->with('questions.subject')->findOrFail($id);
        $this->authorize('attempt', $tryout);

        // Cek apakah ada attempt yang belum selesai
        $existingAttempt = UserTryoutAttempt::where('user_id', $request->user()->id)
            ->where('tryout_id', $tryout->id)
            ->whereNull('submitted_at')
            ->latest()
            ->first();

        if ($existingAttempt) {
            return view('student.tryout.exam', [
                'tryout'  => $tryout,
                'attempt' => $existingAttempt,
            ]);
        }

        // Buat attempt baru
        $attempt = UserTryoutAttempt::create([
            'user_id'    => $request->user()->id,
            'tryout_id'  => $tryout->id,
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

        $tryout    = $attempt->tryout()->with('questions')->first();
        $answers   = $request->input('answers', []);
        $correct   = 0;
        $wrong     = 0;
        $empty     = 0;

        foreach ($tryout->questions as $question) {
            $userAnswer = $answers[$question->id] ?? null;

            if (!$userAnswer) {
                $empty++;
            } elseif ($question->isCorrect($userAnswer)) {
                $correct++;
            } else {
                $wrong++;
            }
        }

        // Hitung skor (benar x 4, salah -1, kosong 0)
        $score = ($correct * 4) - ($wrong * 1);

        // Hitung rank sementara
        $rank = UserTryoutAttempt::where('tryout_id', $tryout->id)
            ->whereNotNull('submitted_at')
            ->where('score', '>', $score)
            ->count() + 1;

        $attempt->update([
            'submitted_at'  => now(),
            'answers'       => $answers,
            'score'         => $score,
            'correct_count' => $correct,
            'wrong_count'   => $wrong,
            'empty_count'   => $empty,
            'rank_at_submit'=> $rank,
        ]);

        // Catat ke study history
        StudyHistory::create([
            'user_id'          => $request->user()->id,
            'activity_type'    => 'tryout',
            'reference_id'     => $tryout->id,
            'reference_type'   => Tryout::class,
            'duration_seconds' => now()->diffInSeconds($attempt->started_at),
            'score'            => $score,
        ]);

        // Update leaderboard
        $this->updateLeaderboard($request->user()->id, $tryout->subject_id, $score);

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

        return view('student.tryout.result', compact('attempt', 'totalParticipants'));
    }

    private function updateLeaderboard(int $userId, ?int $subjectId, float $score): void
    {
        LeaderboardScore::updateOrCreate(
            ['user_id' => $userId, 'subject_id' => $subjectId],
            ['total_score' => \DB::raw("GREATEST(total_score, {$score})"), 'updated_at' => now()]
        );
    }
}

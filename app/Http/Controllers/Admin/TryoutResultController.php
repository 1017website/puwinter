<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Tryout;
use App\Models\UserTryoutAttempt;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TryoutResultController extends Controller
{
    public function index(Request $request): View
    {
        $query = UserTryoutAttempt::query()
            ->submitted()
            ->with(['user.grade', 'tryout.subject'])
            ->whereHas('user')
            ->whereHas('tryout');

        if ($request->filled('search')) {
            $keyword = trim((string) $request->search);
            $query->where(function ($q) use ($keyword) {
                $q->whereHas('user', function ($userQuery) use ($keyword) {
                    $userQuery->where('name', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%")
                        ->orWhere('school', 'like', "%{$keyword}%");
                })->orWhereHas('tryout', function ($tryoutQuery) use ($keyword) {
                    $tryoutQuery->where('title', 'like', "%{$keyword}%")
                        ->orWhere('series', 'like', "%{$keyword}%");
                });
            });
        }

        if ($request->filled('tryout_id')) {
            $query->where('tryout_id', (int) $request->tryout_id);
        }

        if ($request->filled('subject_id')) {
            $query->whereHas('tryout', fn($q) => $q->where('subject_id', (int) $request->subject_id));
        }

        if ($request->filled('scoring_mode')) {
            $query->whereHas('tryout', fn($q) => $q->where('scoring_mode', $request->scoring_mode));
        }

        if ($request->filled('integrity')) {
            if ($request->integrity === 'flagged') {
                $query->where('tab_switch_count', '>', 0);
            } elseif ($request->integrity === 'clean') {
                $query->where(function ($q) {
                    $q->whereNull('tab_switch_count')->orWhere('tab_switch_count', 0);
                });
            }
        }

        $statsQuery = clone $query;
        $topQuery   = clone $query;

        $stats = [
            'total_attempts'  => (clone $statsQuery)->count(),
            'total_students'  => (clone $statsQuery)->distinct('user_id')->count('user_id'),
            'avg_score'       => round((float) (clone $statsQuery)->avg('score'), 2),
            'avg_irt_score'   => round((float) (clone $statsQuery)->whereNotNull('irt_score')->avg('irt_score'), 2),
            'flagged_attempts'=> (clone $statsQuery)->where('tab_switch_count', '>', 0)->count(),
        ];

        $topAttempt = $topQuery
            ->orderByRaw('COALESCE(irt_score, score) DESC')
            ->first();

        $sort = $request->input('sort', 'submitted_desc');
        match ($sort) {
            'score_desc' => $query->orderByRaw('COALESCE(irt_score, score) DESC')->orderByDesc('score'),
            'score_asc'  => $query->orderByRaw('COALESCE(irt_score, score) ASC')->orderBy('score'),
            'rank_asc'   => $query->orderByRaw('rank_at_submit IS NULL ASC')->orderBy('rank_at_submit'),
            default      => $query->latest('submitted_at'),
        };

        $attempts = $query->paginate(15)->withQueryString();

        $tryouts = Tryout::query()
            ->select('id', 'title', 'scoring_mode')
            ->orderBy('title')
            ->get();

        $subjects = Subject::active()->orderBy('order')->get();

        return view('admin.tryout-results.index', compact(
            'attempts',
            'tryouts',
            'subjects',
            'stats',
            'topAttempt',
            'sort'
        ));
    }

    public function show(UserTryoutAttempt $attempt): View
    {
        abort_if(is_null($attempt->submitted_at), 404);

        $attempt->load([
            'user.grade',
            'tryout.subject',
            'tryout.questions.subject',
        ]);

        $tryout    = $attempt->tryout;
        $questions = $tryout->questions;
        $answers   = $attempt->answers ?? [];

        $scoreColumn = ($tryout->isIrt() && $tryout->irt_calibrated && !is_null($attempt->irt_score))
            ? 'irt_score'
            : 'score';
        $scoreValue = (float) ($attempt->{$scoreColumn} ?? 0);

        $totalParticipants = UserTryoutAttempt::where('tryout_id', $tryout->id)
            ->whereNotNull('submitted_at')
            ->count();

        $currentRank = UserTryoutAttempt::where('tryout_id', $tryout->id)
            ->whereNotNull('submitted_at')
            ->where($scoreColumn, '>', $scoreValue)
            ->count() + 1;

        $weightedRank = UserTryoutAttempt::where('tryout_id', $tryout->id)
            ->whereNotNull('submitted_at')
            ->where('weighted_score', '>', $attempt->weighted_score ?? 0)
            ->count() + 1;

        $subjectStats = [];
        $answerRows = [];

        foreach ($questions as $index => $question) {
            $subjectName = $question->subject->name ?? 'Lainnya';
            $subjectStats[$subjectName] ??= [
                'total' => 0.0,
                'correct' => 0,
                'partial' => 0,
                'wrong' => 0,
                'empty' => 0,
                'earned' => 0.0,
            ];
            $subjectStats[$subjectName]['total'] += $question->scoreWeight();

            $userAnswer = $answers[$question->id] ?? null;
            $gradeResult = $question->grade($userAnswer, $question->scoreWeight());
            $status = $gradeResult['status'] ?? 'empty';
            $earned = (float) ($gradeResult['earned'] ?? 0);
            $subjectStats[$subjectName][$status] = ($subjectStats[$subjectName][$status] ?? 0) + 1;
            $subjectStats[$subjectName]['earned'] += $earned;

            $picked = is_array($userAnswer)
                ? array_values(array_map(fn($v) => strtolower((string) $v), $userAnswer))
                : (($userAnswer !== null && $userAnswer !== '') ? [strtolower((string) $userAnswer)] : []);

            $answerRows[] = [
                'number' => $index + 1,
                'question' => $question,
                'status' => $status,
                'earned' => $earned,
                'max' => $question->scoreWeight(),
                'picked' => $picked,
                'keys' => $question->correctKeys(),
            ];
        }

        return view('admin.tryout-results.show', compact(
            'attempt',
            'tryout',
            'questions',
            'answers',
            'totalParticipants',
            'currentRank',
            'weightedRank',
            'subjectStats',
            'answerRows',
            'scoreColumn',
            'scoreValue'
        ));
    }
}

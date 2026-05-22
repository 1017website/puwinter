<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\TryoutQuestion;
use App\Models\UserTryoutAttempt;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PembahasanController extends Controller
{
    public function index(Request $request): View
    {
        $user      = $request->user();
        $subjectId = $request->get('subject_id');
        $filter    = $request->get('filter', 'semua'); // semua | salah | benar

        // Ambil semua soal yang pernah dikerjakan user
        $attempts = UserTryoutAttempt::where('user_id', $user->id)
            ->whereNotNull('submitted_at')
            ->with('tryout.questions.subject')
            ->get();

        // Flatten semua soal + jawaban user
        $answeredQuestions = collect();
        foreach ($attempts as $attempt) {
            $answers = $attempt->answers ?? [];
            foreach ($attempt->tryout->questions as $question) {
                $userAnswer = $answers[$question->id] ?? null;
                $isCorrect  = $userAnswer && $question->isCorrect($userAnswer);

                if ($subjectId && $question->subject_id != $subjectId) continue;
                if ($filter === 'salah' && ($isCorrect || !$userAnswer)) continue;
                if ($filter === 'benar' && !$isCorrect) continue;

                $answeredQuestions->push([
                    'question'    => $question,
                    'user_answer' => $userAnswer,
                    'is_correct'  => $isCorrect,
                    'attempt'     => $attempt,
                ]);
            }
        }

        // Deduplicate by question_id (ambil attempt terbaru)
        $answeredQuestions = $answeredQuestions
            ->sortByDesc(fn($item) => $item['attempt']->submitted_at)
            ->unique(fn($item) => $item['question']->id)
            ->values();

        // Paginate manual
        $page     = $request->get('page', 1);
        $perPage  = 15;
        $total    = $answeredQuestions->count();
        $items    = $answeredQuestions->slice(($page - 1) * $perPage, $perPage)->values();
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $items, $total, $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $subjects = Subject::active()->get();

        $stats = [
            'total'  => $answeredQuestions->count(),
            'benar'  => $answeredQuestions->where('is_correct', true)->count(),
            'salah'  => $answeredQuestions->where('is_correct', false)->filter(fn($i) => $i['user_answer'])->count(),
            'kosong' => $answeredQuestions->filter(fn($i) => !$i['user_answer'])->count(),
        ];

        return view('student.pembahasan.index', compact(
            'paginator', 'subjects', 'subjectId', 'filter', 'stats'
        ));
    }
}

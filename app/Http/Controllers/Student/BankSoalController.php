<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\TryoutQuestion;
use App\Models\UserSavedQuestion;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BankSoalController extends Controller
{
    public function index(Request $request): View
    {
        $user      = $request->user();
        $subjectId = $request->get('subject_id');
        $difficulty = $request->get('difficulty');
        $filter    = $request->get('filter', 'semua'); // semua | disimpan

        $savedIds = $user->savedQuestions()->pluck('question_id');

        $query = TryoutQuestion::with(['tryout', 'subject'])
            ->when($subjectId, fn($q) => $q->where('subject_id', $subjectId))
            ->when($difficulty, fn($q) => $q->where('difficulty', $difficulty))
            ->when($filter === 'disimpan', fn($q) => $q->whereIn('id', $savedIds))
            ->orderBy('subject_id')
            ->orderBy('order');

        $questions = $query->paginate(20)->withQueryString();
        $subjects  = Subject::active()->get();

        return view('student.bank-soal.index', compact(
            'questions', 'subjects', 'savedIds', 'subjectId', 'difficulty', 'filter'
        ));
    }

    public function toggleSave(Request $request, int $questionId): RedirectResponse
    {
        $user = $request->user();
        $existing = UserSavedQuestion::where('user_id', $user->id)
            ->where('question_id', $questionId)
            ->first();

        if ($existing) {
            $existing->delete();
            $msg = 'Soal dihapus dari simpanan.';
        } else {
            UserSavedQuestion::create([
                'user_id'     => $user->id,
                'question_id' => $questionId,
                'saved_at'    => now(),
            ]);
            $msg = 'Soal berhasil disimpan.';
        }

        return back()->with('success', $msg);
    }
}

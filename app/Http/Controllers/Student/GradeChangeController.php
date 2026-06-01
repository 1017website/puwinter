<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\GradeChangeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GradeChangeController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $grades  = Grade::active()->where('id', '!=', $user->grade_id)->get();
        $history = $user->gradeChangeRequests()->with(['fromGrade', 'toGrade'])->get();
        $pending = $user->hasPendingGradeChange();

        return view('student.grade-change.index', compact('grades', 'history', 'pending'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Cegah duplikasi request yang masih pending.
        if ($user->hasPendingGradeChange()) {
            return back()->with('error', 'Kamu masih punya permintaan pindah kelas yang sedang diproses.');
        }

        $request->validate([
            'to_grade_id' => ['required', 'integer', 'exists:grades,id', 'different:current'],
            'reason'      => ['nullable', 'string', 'max:500'],
        ], [
            'to_grade_id.required' => 'Kelas tujuan wajib dipilih.',
        ]);

        if ((int) $request->to_grade_id === (int) $user->grade_id) {
            return back()->with('error', 'Kelas tujuan sama dengan kelas saat ini.');
        }

        GradeChangeRequest::create([
            'user_id'       => $user->id,
            'from_grade_id' => $user->grade_id,
            'to_grade_id'   => $request->to_grade_id,
            'reason'        => $request->input('reason'),
            'status'        => 'pending',
        ]);

        return back()->with('success', 'Permintaan pindah kelas terkirim. Menunggu persetujuan admin.');
    }
}

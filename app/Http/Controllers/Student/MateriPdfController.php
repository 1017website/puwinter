<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CourseMaterial;
use App\Models\UserSavedMaterial;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MateriPdfController extends Controller
{
    public function index(Request $request): View
    {
        $user      = $request->user();
        $subjectId = $request->get('subject_id');
        $filter    = $request->get('filter', 'semua'); // semua | disimpan

        $savedIds = $user->savedMaterials()->pluck('material_id');

        $query = CourseMaterial::with(['module.course.subject'])
            ->where('type', 'pdf')
            ->where('is_locked', false) // hanya yang bisa diakses
            ->when($subjectId, fn($q) => $q->whereHas('module.course', fn($q2) => $q2->where('subject_id', $subjectId)))
            ->when($filter === 'disimpan', fn($q) => $q->whereIn('id', $savedIds))
            ->when(!$user->isPremium(), fn($q) => $q->where('is_premium', false))
            ->orderBy('created_at', 'desc');

        // Jika premium, tampilkan semua
        if ($user->isPremium()) {
            $query = CourseMaterial::with(['module.course.subject'])
                ->where('type', 'pdf')
                ->when($subjectId, fn($q) => $q->whereHas('module.course', fn($q2) => $q2->where('subject_id', $subjectId)))
                ->when($filter === 'disimpan', fn($q) => $q->whereIn('id', $savedIds))
                ->orderBy('created_at', 'desc');
        }

        $materials = $query->paginate(18)->withQueryString();
        $subjects  = Subject::active()->get();

        return view('student.materi-pdf.index', compact(
            'materials', 'subjects', 'savedIds', 'subjectId', 'filter'
        ));
    }

    public function toggleSave(Request $request, int $materialId): RedirectResponse
    {
        $user = $request->user();
        $existing = UserSavedMaterial::where('user_id', $user->id)
            ->where('material_id', $materialId)
            ->first();

        if ($existing) {
            $existing->delete();
            $msg = 'Materi dihapus dari simpanan.';
        } else {
            UserSavedMaterial::create([
                'user_id'     => $user->id,
                'material_id' => $materialId,
                'saved_at'    => now(),
            ]);
            $msg = 'Materi berhasil disimpan.';
        }

        return back()->with('success', $msg);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GradeChangeRequest;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GradeChangeRequestController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->get('status', 'pending');

        $requests = GradeChangeRequest::with(['user', 'fromGrade', 'toGrade', 'processor'])
            ->when(in_array($status, ['pending', 'approved', 'rejected']),
                fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $pendingCount = GradeChangeRequest::pending()->count();

        return view('admin.grade-requests.index', compact('requests', 'status', 'pendingCount'));
    }

    public function approve(Request $request, GradeChangeRequest $gradeChangeRequest): RedirectResponse
    {
        if ($gradeChangeRequest->status !== 'pending') {
            return back()->with('error', 'Permintaan ini sudah diproses.');
        }

        $request->validate(['admin_note' => 'nullable|string|max:500']);

        $user  = $gradeChangeRequest->user;
        $grade = $gradeChangeRequest->toGrade;

        // Terapkan perubahan kelas; tetap dikunci agar perubahan berikutnya via request lagi.
        $user->update([
            'grade_id'     => $grade->id,
            'grade'        => $grade->code,
            'grade_locked' => true,
        ]);

        $gradeChangeRequest->update([
            'status'       => 'approved',
            'admin_note'   => $request->input('admin_note'),
            'processed_by' => $request->user()->id,
            'processed_at' => now(),
        ]);

        $this->notify($user->id, 'Permintaan pindah kelas disetujui',
            "Kelas kamu sekarang: {$grade->name}.");

        return back()->with('success', "Permintaan disetujui. Kelas {$user->name} kini {$grade->name}.");
    }

    public function reject(Request $request, GradeChangeRequest $gradeChangeRequest): RedirectResponse
    {
        if ($gradeChangeRequest->status !== 'pending') {
            return back()->with('error', 'Permintaan ini sudah diproses.');
        }

        $request->validate(['admin_note' => 'nullable|string|max:500']);

        $gradeChangeRequest->update([
            'status'       => 'rejected',
            'admin_note'   => $request->input('admin_note'),
            'processed_by' => $request->user()->id,
            'processed_at' => now(),
        ]);

        $this->notify($gradeChangeRequest->user_id, 'Permintaan pindah kelas ditolak',
            $request->input('admin_note') ?: 'Permintaan pindah kelas kamu ditolak admin.');

        return back()->with('success', 'Permintaan ditolak.');
    }

    private function notify(int $userId, string $title, string $body): void
    {
        // Aman walau struktur Notification berbeda; abaikan bila gagal.
        try {
            Notification::create([
                'user_id' => $userId,
                'title'   => $title,
                'body'    => $body,
                'type'    => 'info',
                'icon'    => 'fa-user-graduate',
                'url'     => route('student.settings.index'),
            ]);
        } catch (\Throwable $e) {
            // no-op: notifikasi opsional
        }
    }
}

<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LiveClass;
use App\Models\LiveClassAttendance;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LiveClassController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $upcoming = LiveClass::upcoming()
            ->forUser($user)
            ->with(['mentor', 'subject'])
            ->get();

        $live = LiveClass::live()
            ->forUser($user)
            ->with(['mentor', 'subject'])
            ->get();

        // Rekaman: live class yang sudah selesai dan punya recording_url
        $recordings = LiveClass::where('status', 'ended')
            ->forUser($user)
            ->whereNotNull('recording_url')
            ->with(['mentor', 'subject'])
            ->latest('scheduled_at')
            ->take(12)
            ->get();

        return view('student.live-classes.index', compact('upcoming', 'live', 'recordings'));
    }

    public function show(Request $request, int $id): View|\Illuminate\Http\RedirectResponse
    {
        $liveClass = LiveClass::with(['mentor', 'subject', 'course'])->findOrFail($id);
        $user      = $request->user();

        // Cek akses kelas/grade: student hanya boleh kelasnya sendiri
        if (!$user->canAccessGrade($liveClass->grade)) {
            return redirect()->route('student.live.index')
                ->with('error', 'Live class ini untuk kelas ' . $liveClass->grade . ', tidak tersedia untuk kelasmu.');
        }

        // Cek akses: live class premium hanya untuk user premium
        if ($liveClass->is_premium && !$user->isPremium()) {
            return redirect()->route('upgrade.index')
                ->with('error', 'Live class ini hanya tersedia untuk member Premium.');
        }

        // Catat kehadiran jika live
        if ($liveClass->isLive()) {
            LiveClassAttendance::firstOrCreate([
                'live_class_id' => $liveClass->id,
                'user_id'       => $user->id,
            ], ['joined_at' => now()]);
        }

        return view('student.live-classes.show', compact('liveClass'));
    }

    /**
     * Redirect aman ke Zoom.
     * Link Zoom asli TIDAK pernah dirender di HTML — siswa hanya menekan tombol
     * yang menuju route ini, lalu server me-redirect ke link Zoom sebenarnya.
     * Ini mencegah link tersalin/tersebar dari halaman.
     */
    public function join(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $liveClass = LiveClass::findOrFail($id);
        $user      = $request->user();

        // Validasi akses grade
        if (!$user->canAccessGrade($liveClass->grade)) {
            abort(403, 'Live class ini bukan untuk kelasmu.');
        }

        // Validasi premium
        if ($liveClass->is_premium && !$user->isPremium()) {
            return redirect()->route('upgrade.index')
                ->with('error', 'Live class ini hanya untuk member Premium.');
        }

        // Hanya boleh join saat status live
        if (!$liveClass->isLive() || empty($liveClass->zoom_link)) {
            return redirect()->route('student.live.show', $liveClass->id)
                ->with('error', 'Kelas belum dimulai atau link belum tersedia.');
        }

        // Catat kehadiran
        LiveClassAttendance::firstOrCreate([
            'live_class_id' => $liveClass->id,
            'user_id'       => $user->id,
        ], ['joined_at' => now()]);

        // Redirect server-side ke Zoom — URL asli tidak tampil di halaman
        return redirect()->away($liveClass->zoom_link);
    }
}

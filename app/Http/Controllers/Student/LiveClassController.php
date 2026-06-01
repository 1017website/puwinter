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

        // Tab aktif: 'regular' (default) atau 'private'
        $tab = $request->get('tab', 'regular');
        $tab = in_array($tab, ['regular', 'private']) ? $tab : 'regular';

        $base = fn() => LiveClass::forUser($user)->ofType($tab)->with(['mentor', 'subject']);

        $upcoming = $base()->where('status', 'scheduled')->where('scheduled_at', '>', now())
            ->orderBy('scheduled_at')->get();

        $live = $base()->where('status', 'live')->get();

        // Rekaman: live class yang sudah selesai dan punya recording_url
        $recordings = $base()->where('status', 'ended')
            ->whereNotNull('recording_url')
            ->latest('scheduled_at')
            ->take(12)
            ->get();

        // Jumlah private khusus untuk badge tab (hanya yang akan datang / live)
        $privateCount = LiveClass::forUser($user)->privateType()
            ->whereIn('status', ['scheduled', 'live'])->count();

        return view('student.live-classes.index', compact('upcoming', 'live', 'recordings', 'tab', 'privateCount'));
    }

    public function show(Request $request, int $id): View|\Illuminate\Http\RedirectResponse
    {
        $liveClass = LiveClass::with(['mentor', 'subject', 'course'])->findOrFail($id);
        $user      = $request->user();

        // Cek akses (grade + premium; private selalu wajib premium)
        if (!$liveClass->isAccessibleBy($user)) {
            if ($liveClass->requiresPremium() && !$user->isPremium()) {
                return redirect()->route('upgrade.index')
                    ->with('error', $liveClass->isPrivate()
                        ? 'Private class hanya tersedia untuk member Premium.'
                        : 'Live class ini hanya tersedia untuk member Premium.');
            }
            return redirect()->route('student.live.index')
                ->with('error', 'Live class ini tidak tersedia untuk kelasmu.');
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

        // Validasi akses (grade + premium; private wajib premium)
        if (!$liveClass->isAccessibleBy($user)) {
            if ($liveClass->requiresPremium() && !$user->isPremium()) {
                return redirect()->route('upgrade.index')
                    ->with('error', $liveClass->isPrivate()
                        ? 'Private class hanya untuk member Premium.'
                        : 'Live class ini hanya untuk member Premium.');
            }
            abort(403, 'Live class ini bukan untuk kelasmu.');
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

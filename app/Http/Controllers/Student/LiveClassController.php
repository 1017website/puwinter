<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LiveClass;
use App\Models\LiveClassAttendance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LiveClassController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // Enrollment program dimuat sekali supaya cek akses per-kartu tidak N+1.
        $user->loadMissing('programEnrollments');

        // Tab aktif: 'regular' (default) atau 'private'
        $tab = $request->get('tab', 'regular');
        $tab = in_array($tab, ['regular', 'private']) ? $tab : 'regular';

        $base = fn() => LiveClass::forUser($user)->ofType($tab)
            ->with(['mentor', 'subject', 'plan.grades']);

        $upcoming = $base()->where('status', 'scheduled')->where('scheduled_at', '>', now())
            ->orderBy('scheduled_at')->get();

        $live = $base()->where('status', 'live')->get();

        // Rekaman: kelas online yang sudah selesai dan punya recording_url
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

    public function show(Request $request, int $id): View|RedirectResponse
    {
        $liveClass = LiveClass::with(['mentor', 'subject', 'course', 'plan.grades'])->findOrFail($id);
        $user      = $request->user();

        if ($deny = $this->guardAccess($liveClass, $user)) {
            return $deny;
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
    public function join(Request $request, int $id): RedirectResponse
    {
        $liveClass = LiveClass::with(['plan.grades'])->findOrFail($id);
        $user      = $request->user();

        if ($deny = $this->guardAccess($liveClass, $user)) {
            return $deny;
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

    /**
     * Gerbang akses tunggal untuk show() & join(): null = boleh lanjut.
     * Untuk kelas online tier gratis, enrollment program dibuat otomatis di sini
     * supaya siswa tidak perlu mendaftar manual dulu.
     */
    private function guardAccess(LiveClass $liveClass, $user): ?RedirectResponse
    {
        $status = $liveClass->accessStatusFor($user);
        if ($status !== LiveClass::ACCESS_OK) {
            return $this->denyResponse($liveClass, $status, $user);
        }

        // Batas kelas online gratis: user free hanya boleh 1 kelas online seumur hidup.
        if (!$user->isPremium() && !$user->canJoinFreeLiveClass($liveClass->id)) {
            return redirect()->route('upgrade.index')
                ->with('error', 'Pengguna gratis hanya dapat mengikuti 1 kelas online. Upgrade ke Premium untuk akses tanpa batas.');
        }

        // Tier gratis: catat sebagai peserta program (idempoten, no-op jika sudah).
        $liveClass->autoEnroll($user);

        return null;
    }

    /**
     * Pesan + tujuan redirect sesuai alasan penolakan yang sebenarnya —
     * bukan pesan generik "bukan untuk kelasmu" untuk semua kasus.
     */
    private function denyResponse(LiveClass $liveClass, string $status, $user): RedirectResponse
    {
        $planName = $liveClass->plan->name ?? 'program ini';

        $planUrl = $liveClass->programPageOpenableBy($user)
            ? route('student.program.show', $liveClass->plan_id)
            : route('student.program.index');

        return match ($status) {
            LiveClass::ACCESS_WRONG_GRADE => redirect()->route('student.live.index')
                ->with('error', 'Kelas online ini khusus untuk '
                    . ($liveClass->gradeName() ?? 'kelas lain') . '.'),

            LiveClass::ACCESS_NOT_ENROLLED => redirect($planUrl)
                ->with('error', 'Kamu belum terdaftar di program "' . $planName
                    . '". Daftar dulu untuk mengikuti kelas online ini.'),

            LiveClass::ACCESS_NEEDS_PAID => redirect($planUrl)
                ->with('error', 'Kelas online ini hanya untuk peserta berbayar program "'
                    . $planName . '".'),

            LiveClass::ACCESS_NEEDS_EXCLUSIVE => redirect()->route('upgrade.index')
                ->with('error', 'Private class hanya tersedia untuk member Premium Exclusive.'),

            default => redirect()->route('student.live.index')
                ->with('error', 'Kelas online ini tidak tersedia untukmu.'),
        };
    }
}

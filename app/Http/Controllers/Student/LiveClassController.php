<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LiveClass;
use App\Models\LiveClassAttendance;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LiveClassController extends Controller
{
    public function index(): View
    {
        $upcoming = LiveClass::upcoming()
            ->with(['mentor', 'subject'])
            ->get();

        $live = LiveClass::live()
            ->with(['mentor', 'subject'])
            ->get();

        // Rekaman: live class yang sudah selesai dan punya recording_url
        $recordings = LiveClass::where('status', 'ended')
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

        // Cek akses: live class premium hanya untuk user premium
        if ($liveClass->is_premium && !$request->user()->isPremium()) {
            return redirect()->route('upgrade.index')
                ->with('error', 'Live class ini hanya tersedia untuk member Premium.');
        }

        // Catat kehadiran jika live
        if ($liveClass->isLive()) {
            LiveClassAttendance::firstOrCreate([
                'live_class_id' => $liveClass->id,
                'user_id'       => $request->user()->id,
            ], ['joined_at' => now()]);
        }

        return view('student.live-classes.show', compact('liveClass'));
    }
}

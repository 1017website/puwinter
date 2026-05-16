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
        $upcoming = LiveClass::upcoming()->with(['mentor', 'subject'])->get();
        $live     = LiveClass::live()->with(['mentor', 'subject'])->get();
        $ended    = LiveClass::where('status', 'ended')
            ->whereNotNull('recording_url')
            ->with(['mentor', 'subject'])
            ->latest('scheduled_at')
            ->take(9)
            ->get();

        return view('student.live-classes.index', compact('upcoming', 'live', 'ended'));
    }

    public function show(Request $request, int $id): View
    {
        $liveClass = LiveClass::with(['mentor', 'subject', 'course'])->findOrFail($id);
        $this->authorize('view', $liveClass);

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

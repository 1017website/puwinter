<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveClass;
use App\Models\Subject;
use App\Models\Course;
use App\Models\User;
use App\Models\Grade;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LiveClassController extends Controller
{
    public function index(Request $request): View
    {
        $query = LiveClass::with(['mentor', 'subject'])
            ->latest('scheduled_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $liveClasses = $query->paginate(15)->withQueryString();

        return view('admin.live-classes.index', compact('liveClasses'));
    }

    public function create(): View
    {
        $subjects = Subject::active()->get();
        $mentors  = User::where('role', 'mentor')->get();
        $courses  = Course::published()->with('subject')->get();
        $grades   = Grade::active()->get();

        $plans = \App\Models\SubscriptionPlan::active()->orderBy('order')->get();
        return view('admin.live-classes.create', compact('subjects', 'mentors', 'courses', 'grades', 'plans'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title'            => 'required|string|max:255',
            'subject_id'       => 'required|exists:subjects,id',
            'mentor_id'        => 'required|exists:users,id',
            'course_id'        => 'nullable|exists:courses,id',
            'grade_id'         => 'nullable|exists:grades,id',
            'class_type'       => 'required|in:regular,private',
            'description'      => 'nullable|string',
            'scheduled_at'     => 'required|date',
            'duration_minutes' => 'required|integer|min:1',
            'zoom_link'        => 'nullable|url',
            'zoom_meeting_id'  => 'nullable|string|max:100',
            'is_premium'       => 'boolean',
            'plan_id'          => 'nullable|exists:subscription_plans,id',
            'access_tier'      => 'required|in:free,paid,both',
        ]);

        $classType = $request->input('class_type', 'regular');
        $isPremium = $classType === 'private' ? true : $request->boolean('is_premium');

        LiveClass::create([
            'title'            => $request->title,
            'subject_id'       => $request->subject_id,
            'mentor_id'        => $request->mentor_id,
            'course_id'        => $request->course_id,
            'grade_id'         => $request->filled('grade_id') ? (int) $request->grade_id : null,
            'class_type'       => $classType,
            'description'      => $request->description,
            'scheduled_at'     => $request->scheduled_at,
            'duration_minutes' => $request->duration_minutes,
            'zoom_link'        => $request->zoom_link,
            'zoom_meeting_id'  => $request->zoom_meeting_id,
            'is_premium'       => $isPremium,
            'plan_id'          => $request->filled('plan_id') ? (int) $request->plan_id : null,
            'access_tier'      => $request->input('access_tier', 'paid'),
            'status'           => 'scheduled',
        ]);

        return redirect()->route('admin.live-classes.index')
            ->with('success', 'Live class berhasil dibuat.');
    }

    public function edit(LiveClass $liveClass): View
    {
        $subjects = Subject::active()->get();
        $mentors  = User::where('role', 'mentor')->get();
        $courses  = Course::published()->with('subject')->get();
        $grades   = Grade::active()->get();

        $plans = \App\Models\SubscriptionPlan::active()->orderBy('order')->get();
        return view('admin.live-classes.edit', compact('liveClass', 'subjects', 'mentors', 'courses', 'grades', 'plans'));
    }

    public function update(Request $request, LiveClass $liveClass): RedirectResponse
    {
        $request->validate([
            'title'            => 'required|string|max:255',
            'subject_id'       => 'required|exists:subjects,id',
            'mentor_id'        => 'required|exists:users,id',
            'course_id'        => 'nullable|exists:courses,id',
            'grade_id'         => 'nullable|exists:grades,id',
            'class_type'       => 'required|in:regular,private',
            'description'      => 'nullable|string',
            'scheduled_at'     => 'required|date',
            'duration_minutes' => 'required|integer|min:1',
            'zoom_link'        => 'nullable|url',
            'zoom_meeting_id'  => 'nullable|string|max:100',
            'recording_url'    => 'nullable|url',
            'status'           => 'required|in:scheduled,live,ended,cancelled',
            'is_premium'       => 'boolean',
            'plan_id'          => 'nullable|exists:subscription_plans,id',
            'access_tier'      => 'required|in:free,paid,both',
        ]);

        $classType = $request->input('class_type', 'regular');
        $isPremium = $classType === 'private' ? true : $request->boolean('is_premium');

        $liveClass->update([
            'title'            => $request->title,
            'subject_id'       => $request->subject_id,
            'mentor_id'        => $request->mentor_id,
            'course_id'        => $request->course_id,
            'grade_id'         => $request->filled('grade_id') ? (int) $request->grade_id : null,
            'class_type'       => $classType,
            'description'      => $request->description,
            'scheduled_at'     => $request->scheduled_at,
            'duration_minutes' => $request->duration_minutes,
            'zoom_link'        => $request->zoom_link,
            'zoom_meeting_id'  => $request->zoom_meeting_id,
            'recording_url'    => $request->recording_url,
            'status'           => $request->status,
            'is_premium'       => $isPremium,
            'plan_id'          => $request->filled('plan_id') ? (int) $request->plan_id : null,
            'access_tier'      => $request->input('access_tier', 'paid'),
        ]);

        return back()->with('success', 'Live class berhasil diperbarui.');
    }

    public function destroy(LiveClass $liveClass): RedirectResponse
    {
        $liveClass->delete();
        return redirect()->route('admin.live-classes.index')
            ->with('success', 'Live class berhasil dihapus.');
    }

    // Set status langsung (go live / end)
    public function setStatus(Request $request, LiveClass $liveClass): RedirectResponse
    {
        $request->validate(['status' => 'required|in:scheduled,live,ended,cancelled']);
        $liveClass->update(['status' => $request->status]);

        $label = match($request->status) {
            'live'      => 'dimulai (LIVE)',
            'ended'     => 'diakhiri',
            'cancelled' => 'dibatalkan',
            default     => 'dijadwalkan ulang',
        };

        return back()->with('success', "Live class berhasil $label.");
    }
}

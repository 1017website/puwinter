<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Grade;
use App\Models\CourseModule;
use App\Models\CourseMaterial;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $query = Course::with(['subject', 'mentor'])
            ->withCount(['enrollments', 'modules']);

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('is_published', $request->status === 'published');
        }

        $courses  = $query->latest()->paginate(15)->withQueryString();
        $subjects = Subject::active()->get();

        return view('admin.courses.index', compact('courses', 'subjects'));
    }

    public function create(): View
    {
        $subjects = Subject::active()->get();
        $mentors  = User::where('role', 'mentor')->get();
        $grades   = Grade::active()->get();
        $plans = \App\Models\SubscriptionPlan::active()->orderBy('order')->get();
        return view('admin.courses.create', compact('subjects', 'mentors', 'grades', 'plans'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title'      => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'grade_id'   => 'nullable|exists:grades,id',
            'course_type'=> 'required|in:regular,extra',
            'plan_id'    => 'nullable|exists:subscription_plans,id',
            'access_tier'=> 'required|in:free,paid,both',
            'mentor_id'  => 'required|exists:users,id',
            'description'=> 'nullable|string',
            'is_premium' => 'boolean',
            'thumbnail'  => 'nullable|image|max:2048',
        ]);

        $data = $request->except('thumbnail');
        $data['slug']       = Str::slug($request->title) . '-' . time();
        // access_tier adalah sumber kebenaran. Sinkronkan flag lama agar
        // tampilan/fitur legacy tidak bertentangan dengan pilihan admin.
        $data['is_premium']  = $request->input('access_tier') === 'paid';
        $data['grade_id']    = $request->filled('grade_id') ? (int) $request->grade_id : null;
        $data['course_type'] = $request->input('course_type', 'regular');
        // Kelas EXTRA lintas kelas & non-premium.
        if ($data['course_type'] === 'extra') { $data['grade_id'] = null; $data['is_premium'] = false; }
        $data['plan_id']     = $request->filled('plan_id') ? (int) $request->plan_id : null;
        $data['access_tier'] = $request->input('access_tier', 'both');

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('courses', 'public');
        }

        $course = Course::create($data);

        return redirect()->route('admin.courses.show', $course)
            ->with('success', 'Kelas berhasil dibuat.');
    }

    public function show(Course $course): View
    {
        $course->load(['subject', 'mentor', 'modules.materials', 'enrollments']);
        return view('admin.courses.show', compact('course'));
    }

    public function edit(Course $course): View
    {
        $subjects = Subject::active()->get();
        $mentors  = User::where('role', 'mentor')->get();
        $grades   = Grade::active()->get();
        $plans = \App\Models\SubscriptionPlan::active()->orderBy('order')->get();
        return view('admin.courses.edit', compact('course', 'subjects', 'mentors', 'grades', 'plans'));
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'subject_id'   => 'required|exists:subjects,id',
            'grade_id'     => 'nullable|exists:grades,id',
            'course_type'  => 'required|in:regular,extra',
            'plan_id'      => 'nullable|exists:subscription_plans,id',
            'access_tier'  => 'required|in:free,paid,both',
            'mentor_id'    => 'required|exists:users,id',
            'description'  => 'nullable|string',
            'is_premium'   => 'boolean',
            'is_published' => 'boolean',
            'thumbnail'    => 'nullable|image|max:2048',
        ]);

        $data = $request->except('thumbnail');
        // access_tier adalah sumber kebenaran. Sinkronkan flag lama agar
        // tampilan/fitur legacy tidak bertentangan dengan pilihan admin.
        $data['is_premium']   = $request->input('access_tier') === 'paid';
        $data['is_published'] = $request->boolean('is_published');
        $data['grade_id']     = $request->filled('grade_id') ? (int) $request->grade_id : null;
        $data['course_type']  = $request->input('course_type', 'regular');
        if ($data['course_type'] === 'extra') { $data['grade_id'] = null; $data['is_premium'] = false; }
        $data['plan_id']     = $request->filled('plan_id') ? (int) $request->plan_id : null;
        $data['access_tier'] = $request->input('access_tier', 'both');

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('courses', 'public');
        }

        $course->update($data);

        return back()->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        $course->delete();
        return redirect()->route('admin.courses.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }

    // Toggle publish
    public function togglePublish(Course $course): RedirectResponse
    {
        $course->update(['is_published' => !$course->is_published]);
        $status = $course->is_published ? 'dipublikasikan' : 'disembunyikan';
        return back()->with('success', "Kelas berhasil $status.");
    }

    // =========================================================================
    // MODULES
    // =========================================================================

    public function storeModule(Request $request, Course $course): RedirectResponse
    {
        $request->validate(['title' => 'required|string|max:255']);

        $course->modules()->create([
            'title' => $request->title,
            'order' => $course->modules()->max('order') + 1,
        ]);

        return back()->with('success', 'Modul berhasil ditambahkan.');
    }

    public function destroyModule(CourseModule $module): RedirectResponse
    {
        $courseId = $module->course_id;
        $module->delete();
        return redirect()->route('admin.courses.show', $courseId)
            ->with('success', 'Modul berhasil dihapus.');
    }

    // =========================================================================
    // MATERIALS
    // =========================================================================

    public function storeMaterial(Request $request, CourseModule $module): RedirectResponse
    {
        $request->validate([
            'title'            => 'required|string|max:255',
            'type'             => 'required|in:video,pdf,quiz,live_class',
            'content_url'      => 'nullable|url',
            'duration_minutes' => 'nullable|integer',
            'is_premium'       => 'boolean',
            'access_tier'      => 'required|in:free,paid,both',
        ]);

        $module->materials()->create([
            'title'            => $request->title,
            'type'             => $request->type,
            'content_url'      => $request->content_url,
            'duration_minutes' => $request->duration_minutes,
            'is_premium'       => $request->input('access_tier') === 'paid',
            'access_tier'      => $request->input('access_tier', 'both'),
            'order'            => $module->materials()->max('order') + 1,
        ]);

        return back()->with('success', 'Materi berhasil ditambahkan.');
    }

    public function destroyMaterial(CourseMaterial $material): RedirectResponse
    {
        $courseId = $material->module->course_id;
        $material->delete();
        return redirect()->route('admin.courses.show', $courseId)
            ->with('success', 'Materi berhasil dihapus.');
    }
}

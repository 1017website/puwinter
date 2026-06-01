<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExtraClassController extends Controller
{
    /**
     * Daftar Extra Class (mis. TOEFL). Bebas diakses semua siswa tanpa premium,
     * lintas kelas. Detail/materi tetap memakai alur Course (student.course.show)
     * yang sudah mengecek akses via Course::isAccessibleBy().
     */
    public function index(Request $request): View
    {
        $user   = $request->user();
        $search = $request->get('search');

        $enrolledIds = $user->enrollments()->pluck('course_id');

        $courses = Course::published()
            ->extraType()
            ->with(['subject', 'mentor'])
            ->withCount('enrollments')
            ->when($search, fn($q) => $q->where('title', 'like', "%{$search}%"))
            ->orderByDesc('enrollments_count')
            ->paginate(12)
            ->withQueryString();

        return view('student.extra-class.index', compact('courses', 'enrolledIds', 'search'));
    }
}

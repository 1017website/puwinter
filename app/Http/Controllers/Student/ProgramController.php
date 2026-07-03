<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ProgramEnrollment;
use App\Models\SubscriptionPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProgramController extends Controller
{
    /**
     * Daftar semua program (plan aktif) + status enrollment user.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $programs = SubscriptionPlan::active()->orderBy('order')->get();

        // status enrollment user per plan_id: 'free' | 'paid' | null (belum daftar)
        $enrollments = $user->programEnrollments()
            ->get()
            ->keyBy('plan_id');

        return view('student.programs.index', compact('programs', 'enrollments'));
    }

    /**
     * Daftar (enroll) ke sebuah program secara gratis.
     */
    public function enroll(Request $request, int $planId): RedirectResponse
    {
        $user = $request->user();
        $plan = SubscriptionPlan::active()->findOrFail($planId);

        ProgramEnrollment::firstOrCreate(
            ['user_id' => $user->id, 'plan_id' => $plan->id],
            ['status' => ProgramEnrollment::STATUS_FREE, 'enrolled_at' => now()]
        );

        return redirect()->route('student.program.show', $plan->id)
            ->with('success', 'Kamu berhasil terdaftar di program ' . $plan->name . '. Selamat belajar!');
    }

    /**
     * Detail satu program: tampilkan course/tryout/kelas online di dalamnya.
     */
    public function show(Request $request, int $planId): View
    {
        $user = $request->user();
        $plan = SubscriptionPlan::active()->findOrFail($planId);

        $enrollment = $user->programEnrollments()->where('plan_id', $plan->id)->first();
        $isEnrolled = $enrollment !== null;
        $isPaid     = $enrollment ? $enrollment->isPaidActive() : false;

        // Konten dalam program ini
        $courses = \App\Models\Course::published()
            ->where('plan_id', $plan->id)
            ->with(['subject', 'modules.materials'])
            ->orderBy('order')
            ->get();

        $tryouts = \App\Models\Tryout::published()
            ->where('plan_id', $plan->id)
            ->orderBy('order')
            ->get();

        $liveClasses = \App\Models\LiveClass::where('plan_id', $plan->id)
            ->orderBy('scheduled_at')
            ->get();

        return view('student.programs.show', compact(
            'plan', 'enrollment', 'isEnrolled', 'isPaid',
            'courses', 'tryouts', 'liveClasses'
        ));
    }
}

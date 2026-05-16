<?php

namespace App\Providers;

use App\Models\Course;
use App\Models\LiveClass;
use App\Models\StudentNote;
use App\Models\Tryout;
use App\Models\User;
use App\Policies\CoursePolicy;
use App\Policies\LiveClassPolicy;
use App\Policies\StudentNotePolicy;
use App\Policies\TryoutPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Course::class     => CoursePolicy::class,
        LiveClass::class  => LiveClassPolicy::class,
        Tryout::class     => TryoutPolicy::class,
        StudentNote::class => StudentNotePolicy::class,
    ];

    public function boot(): void
    {
        // Superadmin bypass semua gate
        Gate::before(function (User $user, string $ability) {
            if ($user->isSuperAdmin()) {
                return true;
            }
        });

        // -------------------------------------------------------------------------
        // Role Gates
        // -------------------------------------------------------------------------

        Gate::define('access-admin-panel', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('manage-users', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('manage-courses', function (User $user) {
            return $user->isAdmin() || $user->isMentor();
        });

        Gate::define('manage-live-classes', function (User $user) {
            return $user->isAdmin() || $user->isMentor();
        });

        Gate::define('manage-tryouts', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('manage-payments', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('view-analytics', function (User $user) {
            return $user->isAdmin();
        });

        // -------------------------------------------------------------------------
        // Premium Content Gates
        // -------------------------------------------------------------------------

        Gate::define('access-premium-content', function (User $user) {
            return $user->isPremium();
        });

        Gate::define('access-premium-course', function (User $user, Course $course) {
            if (!$course->is_premium) return true;
            return $user->isPremium();
        });

        Gate::define('access-premium-live-class', function (User $user, LiveClass $liveClass) {
            if (!$liveClass->is_premium) return true;
            return $user->isPremium();
        });

        Gate::define('access-premium-tryout', function (User $user, Tryout $tryout) {
            if (!$tryout->is_premium) return true;
            return $user->isPremium();
        });

        // -------------------------------------------------------------------------
        // Mentor-specific Gates
        // -------------------------------------------------------------------------

        Gate::define('manage-own-course', function (User $user, Course $course) {
            if ($user->isAdmin()) return true;
            return $user->isMentor() && $course->mentor_id === $user->id;
        });

        Gate::define('manage-own-live-class', function (User $user, LiveClass $liveClass) {
            if ($user->isAdmin()) return true;
            return $user->isMentor() && $liveClass->mentor_id === $user->id;
        });
    }
}

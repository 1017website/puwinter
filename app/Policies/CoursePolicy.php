<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function viewAny(User $user): bool
    {
        return true; // semua role bisa lihat daftar course
    }

    public function view(User $user, Course $course): bool
    {
        if (!$course->is_premium) return true;
        return $user->isPremium();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isMentor();
    }

    public function update(User $user, Course $course): bool
    {
        if ($user->isAdmin()) return true;
        return $user->isMentor() && $course->mentor_id === $user->id;
    }

    public function delete(User $user, Course $course): bool
    {
        if ($user->isAdmin()) return true;
        return $user->isMentor() && $course->mentor_id === $user->id;
    }
}

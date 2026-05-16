<?php

namespace App\Policies;

use App\Models\LiveClass;
use App\Models\User;

class LiveClassPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, LiveClass $liveClass): bool
    {
        if (!$liveClass->is_premium) return true;
        return $user->isPremium();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isMentor();
    }

    public function update(User $user, LiveClass $liveClass): bool
    {
        if ($user->isAdmin()) return true;
        return $user->isMentor() && $liveClass->mentor_id === $user->id;
    }

    public function delete(User $user, LiveClass $liveClass): bool
    {
        if ($user->isAdmin()) return true;
        return $user->isMentor() && $liveClass->mentor_id === $user->id;
    }
}

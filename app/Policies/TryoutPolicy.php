<?php

namespace App\Policies;

use App\Models\Tryout;
use App\Models\User;

class TryoutPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Tryout $tryout): bool
    {
        if (!$tryout->is_premium) return true;
        return $user->isPremium();
    }

    public function attempt(User $user, Tryout $tryout): bool
    {
        if (!$tryout->is_premium) return true;
        return $user->isPremium();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user): bool
    {
        return $user->isAdmin();
    }
}

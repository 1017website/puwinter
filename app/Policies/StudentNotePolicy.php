<?php

namespace App\Policies;

use App\Models\StudentNote;
use App\Models\User;

class StudentNotePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, StudentNote $note): bool
    {
        return $user->id === $note->user_id || $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return true; // semua user bisa buat catatan
    }

    public function update(User $user, StudentNote $note): bool
    {
        return $user->id === $note->user_id;
    }

    public function delete(User $user, StudentNote $note): bool
    {
        return $user->id === $note->user_id || $user->isAdmin();
    }
}

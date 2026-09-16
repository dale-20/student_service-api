<?php

namespace App\Policies;

use App\Enums\RoleSlug;
use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(RoleSlug::Administrator, RoleSlug::Registrar);
    }

    public function view(User $user, Student $student): bool
    {
        return $this->viewAny($user) || $student->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Student $student): bool
    {
        return $this->viewAny($user);
    }
}

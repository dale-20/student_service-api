<?php

namespace App\Policies;

use App\Enums\RoleSlug;
use App\Models\ProgramCourse;
use App\Models\User;

class ProgramCoursePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(RoleSlug::Administrator, RoleSlug::Registrar);
    }

    public function delete(User $user, ProgramCourse $record): bool
    {
        return $this->viewAny($user);
    }

    public function restore(User $user, ProgramCourse $record): bool
    {
        return $this->viewAny($user);
    }
}

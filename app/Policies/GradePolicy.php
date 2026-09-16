<?php

namespace App\Policies;

use App\Enums\RoleSlug;
use App\Models\Grade;
use App\Models\User;

class GradePolicy
{
    private function staff(User $user): bool
    {
        return $user->hasRole(RoleSlug::Administrator, RoleSlug::Registrar);
    }

    public function view(User $user, Grade $grade): bool
    {
        return $this->staff($user)
            || $grade->enrollment->student->user_id === $user->id
            || ($user->hasRole(RoleSlug::Instructor) && $grade->enrollment->courseOffering->instructor_id === $user->id);
    }

    public function update(User $user, Grade $grade): bool
    {
        return $this->staff($user)
            || ($user->hasRole(RoleSlug::Instructor) && $grade->enrollment->courseOffering->instructor_id === $user->id);
    }
}

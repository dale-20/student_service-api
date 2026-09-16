<?php

namespace App\Policies;

use App\Enums\RoleSlug;
use App\Models\Enrollment;
use App\Models\User;

class EnrollmentPolicy
{
    private function staff(User $user): bool
    {
        return $user->hasRole(RoleSlug::Administrator, RoleSlug::Registrar);
    }

    public function viewAny(User $user): bool
    {
        return $this->staff($user);
    }

    public function view(User $user, Enrollment $enrollment): bool
    {
        return $this->staff($user)
            || $enrollment->student->user_id === $user->id
            || ($user->hasRole(RoleSlug::Instructor) && $enrollment->courseOffering->instructor_id === $user->id);
    }

    public function create(User $user): bool
    {
        return $this->staff($user);
    }

    public function update(User $user, Enrollment $enrollment): bool
    {
        return $this->staff($user);
    }
}

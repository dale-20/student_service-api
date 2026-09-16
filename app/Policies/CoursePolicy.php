<?php

namespace App\Policies;

use App\Enums\RoleSlug;
use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    private function staff(User $user): bool
    {
        return $user->hasRole(RoleSlug::Administrator, RoleSlug::Registrar);
    }

    public function viewAny(User $user): bool
    {
        return $this->staff($user);
    }

    public function view(User $user, Course $course): bool
    {
        return $this->staff($user);
    }

    public function create(User $user): bool
    {
        return $this->staff($user);
    }

    public function update(User $user, Course $course): bool
    {
        return $this->staff($user);
    }
}

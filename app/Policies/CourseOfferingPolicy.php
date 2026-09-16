<?php

namespace App\Policies;

use App\Enums\RoleSlug;
use App\Models\CourseOffering;
use App\Models\User;

class CourseOfferingPolicy
{
    private function staff(User $user): bool
    {
        return $user->hasRole(RoleSlug::Administrator, RoleSlug::Registrar);
    }

    public function viewAny(User $user): bool
    {
        return $this->staff($user) || $user->hasRole(RoleSlug::Instructor);
    }

    public function view(User $user, CourseOffering $offering): bool
    {
        return $this->staff($user) || ($user->hasRole(RoleSlug::Instructor) && $offering->instructor_id === $user->id);
    }

    public function create(User $user): bool
    {
        return $this->staff($user);
    }

    public function update(User $user, CourseOffering $offering): bool
    {
        return $this->staff($user);
    }

    public function manageGrades(User $user, CourseOffering $offering): bool
    {
        return $this->staff($user) || ($user->hasRole(RoleSlug::Instructor) && $offering->instructor_id === $user->id);
    }
}

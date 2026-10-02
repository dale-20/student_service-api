<?php

namespace App\Policies;

use App\Enums\RoleSlug;
use App\Models\CourseOfferingSchedule;
use App\Models\User;

class CourseOfferingSchedulePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(RoleSlug::Administrator, RoleSlug::Registrar);
    }

    public function delete(User $user, CourseOfferingSchedule $record): bool
    {
        return $this->viewAny($user);
    }

    public function restore(User $user, CourseOfferingSchedule $record): bool
    {
        return $this->viewAny($user);
    }
}

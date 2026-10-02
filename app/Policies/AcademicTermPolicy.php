<?php

namespace App\Policies;

use App\Enums\RoleSlug;
use App\Models\AcademicTerm;
use App\Models\User;

class AcademicTermPolicy
{
    private function staff(User $user): bool
    {
        return $user->hasRole(RoleSlug::Administrator, RoleSlug::Registrar);
    }

    public function viewAny(User $user): bool
    {
        return $this->staff($user);
    }

    public function view(User $user, AcademicTerm $term): bool
    {
        return $this->staff($user);
    }

    public function create(User $user): bool
    {
        return $this->staff($user);
    }

    public function update(User $user, AcademicTerm $term): bool
    {
        return $this->staff($user);
    }

    public function delete(User $user, AcademicTerm $record): bool
    {
        return $user->hasRole(RoleSlug::Administrator, RoleSlug::Registrar);
    }

    public function restore(User $user, AcademicTerm $record): bool
    {
        return $user->hasRole(RoleSlug::Administrator, RoleSlug::Registrar);
    }
}

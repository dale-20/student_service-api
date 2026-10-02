<?php

namespace App\Policies;

use App\Enums\RoleSlug;
use App\Models\User;

class UserPolicy
{
    public function before(User $user): ?bool
    {
        return $user->hasRole(RoleSlug::Administrator) ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, User $model): bool
    {
        return $user->is($model);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, User $model): bool
    {
        return $user->is($model);
    }

    public function delete(User $user, User $record): bool
    {
        return $user->hasRole(RoleSlug::Administrator);
    }

    public function restore(User $user, User $record): bool
    {
        return $user->hasRole(RoleSlug::Administrator);
    }
}

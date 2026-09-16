<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminResetPasswordRequest;
use App\Http\Resources\UserResource;
use App\Models\User;

class UserPasswordController extends Controller
{
    public function update(AdminResetPasswordRequest $request, User $user): UserResource
    {
        $this->authorize('update', $user);
        $user->update(['password' => $request->validated('password'), 'must_change_password' => true]);

        return (new UserResource($user->load('role')))->additional(['message' => 'Password reset successfully.']);
    }
}

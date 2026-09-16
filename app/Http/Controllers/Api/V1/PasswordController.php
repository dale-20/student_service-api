<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PasswordChangeRequest;
use App\Http\Resources\UserResource;

class PasswordController extends Controller
{
    public function update(PasswordChangeRequest $request): UserResource
    {
        $user = $request->user();
        $user->forceFill(['password' => $request->validated('password'), 'must_change_password' => false])->save();
        $request->session()->regenerate();

        return (new UserResource($user->loadMissing(['role', 'student.program'])))->additional(['message' => 'Password changed successfully.']);
    }
}

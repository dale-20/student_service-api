<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function store(LoginRequest $request): UserResource
    {
        $user = User::query()->with(['role', 'student.program'])->where('email', $request->string('email')->toString())->first();

        if (! $user || $user->status !== UserStatus::Active || ! Hash::check($request->string('password')->toString(), $user->password)) {
            throw ValidationException::withMessages(['email' => ['The provided credentials are incorrect.']]);
        }

        Auth::guard('web')->login($user);
        $request->session()->regenerate();
        $user->forceFill(['last_login_at' => now()])->save();

        return (new UserResource($user->refresh()->load(['role', 'student.program'])))->additional(['message' => 'Signed in successfully.']);
    }
}

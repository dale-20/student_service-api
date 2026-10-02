<?php

namespace App\Http\Requests;

use App\Enums\RoleSlug;
use App\Enums\UserStatus;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'status' => ['required', Rule::enum(UserStatus::class)],
            'password' => [$user ? 'sometimes' : 'required', 'nullable', 'confirmed', Password::min(12)->mixedCase()->numbers()->symbols()],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            $account = $this->route('user');
            if (! $account || $validator->errors()->isNotEmpty()) {
                return;
            }
            $role = Role::query()->findOrFail($this->integer('role_id'))->slug;
            if ($role !== RoleSlug::Student && $account->student()->withTrashed()->exists()) {
                $validator->errors()->add('role_id', 'This account is linked to a student record. Unlink it before changing its role.');
            }
            if ($role !== RoleSlug::Instructor && $account->courseOfferings()->withTrashed()->exists()) {
                $validator->errors()->add('role_id', 'This account has assigned offerings. Reassign them before changing its role.');
            }
            if ($account->hasRole(RoleSlug::Administrator) && ($role !== RoleSlug::Administrator || $this->input('status') !== 'active')) {
                if ($account->is($this->user()) || ! User::query()->whereKeyNot($account->id)->where('status', 'active')->whereHas('role', fn ($query) => $query->where('slug', RoleSlug::Administrator))->exists()) {
                    $validator->errors()->add('role_id', 'Keep your administrator access and at least one active administrator.');
                }
            }
        }];
    }
}

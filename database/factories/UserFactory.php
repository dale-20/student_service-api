<?php

namespace Database\Factories;

use App\Enums\RoleSlug;
use App\Enums\UserStatus;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'role_id' => fn () => $this->roleId(RoleSlug::Student),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'must_change_password' => false,
            'status' => UserStatus::Active,
            'last_login_at' => null,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function administrator(): static
    {
        return $this->forRole(RoleSlug::Administrator);
    }

    public function registrar(): static
    {
        return $this->forRole(RoleSlug::Registrar);
    }

    public function instructor(): static
    {
        return $this->forRole(RoleSlug::Instructor);
    }

    public function student(): static
    {
        return $this->forRole(RoleSlug::Student);
    }

    private function forRole(RoleSlug $slug): static
    {
        return $this->state(fn () => ['role_id' => $this->roleId($slug)]);
    }

    private function roleId(RoleSlug $slug): int
    {
        $names = [
            RoleSlug::Administrator->value => 'Administrator',
            RoleSlug::Registrar->value => 'Registrar / Staff',
            RoleSlug::Instructor->value => 'Instructor',
            RoleSlug::Student->value => 'Student',
        ];

        return Role::query()->firstOrCreate(
            ['slug' => $slug],
            ['name' => $names[$slug->value]],
        )->getKey();
    }
}

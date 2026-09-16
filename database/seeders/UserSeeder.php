<?php

namespace Database\Seeders;

use App\Enums\RoleSlug;
use App\Enums\UserStatus;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roleIds = Role::query()->pluck('id', 'slug');
        $password = Hash::make('password');
        $users = [
            ['role' => RoleSlug::Administrator, 'name' => 'System Administrator', 'email' => 'admin@example.com'],
            ['role' => RoleSlug::Registrar, 'name' => 'Demo Registrar', 'email' => 'registrar@example.com'],
            ['role' => RoleSlug::Instructor, 'name' => 'Ada Instructor', 'email' => 'instructor1@example.com'],
            ['role' => RoleSlug::Instructor, 'name' => 'Alan Instructor', 'email' => 'instructor2@example.com'],
            ['role' => RoleSlug::Student, 'name' => 'Demo Student', 'email' => 'student@example.com'],
        ];

        foreach ($users as $user) {
            User::query()->updateOrCreate(
                ['email' => $user['email']],
                [
                    'role_id' => $roleIds->get($user['role']->value),
                    'name' => $user['name'],
                    'email_verified_at' => now(),
                    'password' => $password,
                    'status' => UserStatus::Active,
                ],
            );
        }
    }
}

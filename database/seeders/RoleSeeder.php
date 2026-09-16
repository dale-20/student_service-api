<?php

namespace Database\Seeders;

use App\Enums\RoleSlug;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            RoleSlug::Administrator->value => ['name' => 'Administrator', 'description' => 'Full system administration access.'],
            RoleSlug::Registrar->value => ['name' => 'Registrar / Staff', 'description' => 'Manages students and academic information.'],
            RoleSlug::Instructor->value => ['name' => 'Instructor', 'description' => 'Manages grades for assigned course offerings.'],
            RoleSlug::Student->value => ['name' => 'Student', 'description' => 'Views their own student and academic records.'],
        ];

        foreach ($roles as $slug => $attributes) {
            Role::query()->updateOrCreate(['slug' => $slug], $attributes);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Enums\RoleSlug;
use App\Enums\StudentStatus;
use App\Models\Program;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $programIds = Program::query()->orderBy('code')->pluck('id')->values();
        $studentUser = User::query()
            ->whereHas('role', fn ($query) => $query->where('slug', RoleSlug::Student->value))
            ->firstOrFail();

        foreach (range(1, 100) as $number) {
            Student::factory()->create([
                'user_id' => $number === 1 ? $studentUser->getKey() : null,
                'program_id' => $programIds[($number - 1) % $programIds->count()],
                'student_number' => sprintf('2026-%05d', $number),
                'email' => $number === 1 ? $studentUser->email : sprintf('student%03d@example.edu', $number),
                'year_level' => (($number - 1) % 5) + 1,
                'status' => StudentStatus::Active,
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Enums\ProgramStatus;
use App\Models\Program;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            ['code' => 'BSIT', 'name' => 'Bachelor of Science in Information Technology'],
            ['code' => 'BSCS', 'name' => 'Bachelor of Science in Computer Science'],
            ['code' => 'BSIS', 'name' => 'Bachelor of Science in Information Systems'],
        ];

        foreach ($programs as $program) {
            Program::query()->updateOrCreate(
                ['code' => $program['code']],
                [...$program, 'status' => ProgramStatus::Active],
            );
        }
    }
}

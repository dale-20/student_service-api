<?php

namespace Database\Factories;

use App\Enums\ProgramStatus;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Program> */
class ProgramFactory extends Factory
{
    public function definition(): array
    {
        $code = Str::upper(fake()->unique()->lexify('????'));

        return [
            'code' => $code,
            'name' => "Bachelor of Science in {$code}",
            'description' => fake()->sentence(),
            'status' => ProgramStatus::Active,
        ];
    }
}

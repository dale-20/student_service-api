<?php

namespace Database\Factories;

use App\Enums\Semester;
use App\Models\Course;
use App\Models\Program;
use App\Models\ProgramCourse;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ProgramCourse> */
class ProgramCourseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'program_id' => Program::factory(),
            'course_id' => Course::factory(),
            'year_level' => fake()->numberBetween(1, 4),
            'semester' => fake()->randomElement(Semester::cases()),
            'is_required' => true,
        ];
    }
}

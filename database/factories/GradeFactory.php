<?php

namespace Database\Factories;

use App\Enums\GradeRemark;
use App\Models\Enrollment;
use App\Models\Grade;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Grade> */
class GradeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'enrollment_id' => Enrollment::factory(),
            'midterm_grade' => fake()->randomElement([1.00, 1.25, 1.50, 1.75, 2.00, 2.25, 2.50]),
            'final_grade' => fake()->randomElement([1.00, 1.25, 1.50, 1.75, 2.00, 2.25, 2.50, 3.00]),
            'remarks' => GradeRemark::Passed,
        ];
    }
}

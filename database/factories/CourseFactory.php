<?php

namespace Database\Factories;

use App\Enums\CourseStatus;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Course> */
class CourseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'course_code' => fake()->unique()->bothify('??###'),
            'course_title' => fake()->sentence(3),
            'description' => fake()->sentence(),
            'units' => fake()->randomElement([1.00, 2.00, 3.00, 4.00]),
            'status' => CourseStatus::Active,
        ];
    }
}

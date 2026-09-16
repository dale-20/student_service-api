<?php

namespace Database\Factories;

use App\Enums\CourseOfferingStatus;
use App\Models\AcademicTerm;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CourseOffering> */
class CourseOfferingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'academic_term_id' => AcademicTerm::factory(),
            'instructor_id' => User::factory()->instructor(),
            'section' => 'A',
            'room' => fake()->bothify('LAB-#'),
            'schedule' => null,
            'capacity' => 35,
            'status' => CourseOfferingStatus::Open,
        ];
    }
}

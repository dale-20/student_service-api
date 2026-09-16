<?php

namespace Database\Factories;

use App\Enums\DayOfWeek;
use App\Models\CourseOffering;
use App\Models\CourseOfferingSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CourseOfferingSchedule> */
class CourseOfferingScheduleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'course_offering_id' => CourseOffering::factory(),
            'day_of_week' => fake()->randomElement(DayOfWeek::cases()),
            'start_time' => '08:00:00',
            'end_time' => '09:30:00',
            'room' => fake()->bothify('LAB-#'),
        ];
    }
}

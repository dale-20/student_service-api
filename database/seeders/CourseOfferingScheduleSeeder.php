<?php

namespace Database\Seeders;

use App\Enums\DayOfWeek;
use App\Models\CourseOffering;
use App\Models\CourseOfferingSchedule;
use Illuminate\Database\Seeder;

class CourseOfferingScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $patterns = [
            [[DayOfWeek::Monday, DayOfWeek::Wednesday], '08:00:00', '09:30:00'],
            [[DayOfWeek::Tuesday, DayOfWeek::Thursday], '09:30:00', '11:00:00'],
            [[DayOfWeek::Wednesday, DayOfWeek::Friday], '13:00:00', '14:30:00'],
            [[DayOfWeek::Monday, DayOfWeek::Saturday], '14:30:00', '16:00:00'],
        ];

        foreach (CourseOffering::query()->orderBy('id')->get() as $index => $offering) {
            [$days, $startTime, $endTime] = $patterns[$index % count($patterns)];

            foreach ($days as $day) {
                CourseOfferingSchedule::query()->updateOrCreate(
                    ['course_offering_id' => $offering->getKey(), 'day_of_week' => $day, 'start_time' => $startTime, 'end_time' => $endTime],
                    ['room' => $offering->room],
                );
            }
        }
    }
}

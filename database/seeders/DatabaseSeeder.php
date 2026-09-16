<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            ProgramSeeder::class,
            CourseSeeder::class,
            AcademicTermSeeder::class,
            StudentSeeder::class,
            ProgramCourseSeeder::class,
            CourseOfferingSeeder::class,
            CourseOfferingScheduleSeeder::class,
            EnrollmentSeeder::class,
            GradeSeeder::class,
        ]);
    }
}

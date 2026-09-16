<?php

namespace Database\Seeders;

use App\Enums\EnrollmentStatus;
use App\Models\CourseOffering;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::query()->orderBy('student_number')->get();
        $offerings = CourseOffering::query()->orderBy('id')->get();
        $enrollmentNumber = 0;

        foreach ($students as $studentIndex => $student) {
            $offeringIndexes = [$studentIndex % $offerings->count(), ($studentIndex + 7) % $offerings->count()];

            foreach ($offeringIndexes as $offeringIndex) {
                $status = match (true) {
                    $enrollmentNumber < 140 => EnrollmentStatus::Completed,
                    $enrollmentNumber < 180 => EnrollmentStatus::Enrolled,
                    $enrollmentNumber < 190 => EnrollmentStatus::Dropped,
                    default => EnrollmentStatus::Withdrawn,
                };

                Enrollment::query()->updateOrCreate(
                    ['student_id' => $student->getKey(), 'course_offering_id' => $offerings[$offeringIndex]->getKey()],
                    ['enrollment_date' => '2026-06-01', 'status' => $status],
                );

                $enrollmentNumber++;
            }
        }
    }
}

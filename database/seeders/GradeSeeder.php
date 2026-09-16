<?php

namespace Database\Seeders;

use App\Enums\EnrollmentStatus;
use App\Enums\GradeRemark;
use App\Models\Enrollment;
use App\Models\Grade;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    public function run(): void
    {
        $completed = Enrollment::query()->where('status', EnrollmentStatus::Completed)->orderBy('id')->limit(95)->get();
        $inProgress = Enrollment::query()->where('status', EnrollmentStatus::Enrolled)->orderBy('id')->limit(5)->get();
        $passingGrades = [1.00, 1.25, 1.50, 1.75, 2.00, 2.25, 2.50, 2.75, 3.00];

        foreach ($completed as $index => $enrollment) {
            Grade::query()->updateOrCreate(
                ['enrollment_id' => $enrollment->getKey()],
                [
                    'midterm_grade' => $passingGrades[$index % count($passingGrades)],
                    'final_grade' => $passingGrades[($index + 2) % count($passingGrades)],
                    'remarks' => GradeRemark::Passed,
                ],
            );
        }

        foreach ($inProgress as $index => $enrollment) {
            Grade::query()->updateOrCreate(
                ['enrollment_id' => $enrollment->getKey()],
                ['midterm_grade' => $passingGrades[$index], 'final_grade' => null, 'remarks' => GradeRemark::Incomplete],
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Enums\AcademicTermType;
use App\Enums\CourseOfferingStatus;
use App\Enums\RoleSlug;
use App\Models\AcademicTerm;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseOfferingSeeder extends Seeder
{
    public function run(): void
    {
        $courses = Course::query()->orderBy('course_code')->get();
        $terms = AcademicTerm::query()->get()->keyBy(fn (AcademicTerm $term) => $term->term->value);
        $instructors = User::query()
            ->whereHas('role', fn ($query) => $query->where('slug', RoleSlug::Instructor->value))
            ->orderBy('email')
            ->get();

        foreach ($courses as $index => $course) {
            $isFirstTerm = $index < 10;
            $term = $terms[$isFirstTerm ? AcademicTermType::FirstSemester->value : AcademicTermType::SecondSemester->value];

            CourseOffering::query()->updateOrCreate(
                ['course_id' => $course->getKey(), 'academic_term_id' => $term->getKey(), 'section' => 'A'],
                [
                    'instructor_id' => $instructors[$index % $instructors->count()]->getKey(),
                    'room' => sprintf('LAB-%d', ($index % 5) + 1),
                    'schedule' => null,
                    'capacity' => 35,
                    'status' => $isFirstTerm ? CourseOfferingStatus::Ongoing : CourseOfferingStatus::Open,
                ],
            );
        }
    }
}

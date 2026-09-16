<?php

namespace Database\Seeders;

use App\Enums\Semester;
use App\Models\Course;
use App\Models\Program;
use App\Models\ProgramCourse;
use Illuminate\Database\Seeder;

class ProgramCourseSeeder extends Seeder
{
    public function run(): void
    {
        $programs = Program::query()->get()->keyBy('code');
        $courses = Course::query()->orderBy('course_code')->get();
        $sharedCodes = ['IT101', 'IT102', 'IT201', 'IT202', 'GEN101', 'MATH101', 'MATH201'];

        foreach ($courses as $index => $course) {
            $programCodes = match (true) {
                in_array($course->course_code, $sharedCodes, true) => ['BSIT', 'BSCS', 'BSIS'],
                str_starts_with($course->course_code, 'CS') => ['BSCS'],
                str_starts_with($course->course_code, 'IS') => ['BSIS'],
                $course->course_code === 'CAP401' => ['BSIT', 'BSCS', 'BSIS'],
                default => ['BSIT'],
            };

            foreach ($programCodes as $programCode) {
                ProgramCourse::query()->updateOrCreate(
                    ['program_id' => $programs[$programCode]->getKey(), 'course_id' => $course->getKey()],
                    [
                        'year_level' => min(4, intdiv($index, 5) + 1),
                        'semester' => $index % 2 === 0 ? Semester::First : Semester::Second,
                        'is_required' => true,
                    ],
                );
            }
        }
    }
}

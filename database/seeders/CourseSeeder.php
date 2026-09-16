<?php

namespace Database\Seeders;

use App\Enums\CourseStatus;
use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            ['IT101', 'Introduction to Computing', 3],
            ['IT102', 'Computer Programming 1', 3],
            ['IT103', 'Computer Programming 2', 3],
            ['IT201', 'Database Systems', 3],
            ['IT202', 'Web Systems and Technologies', 3],
            ['IT203', 'Computer Networks', 3],
            ['IT204', 'Data Structures and Algorithms', 3],
            ['IT301', 'Systems Integration and Architecture', 3],
            ['IT302', 'Information Assurance and Security', 3],
            ['CS101', 'Discrete Structures', 3],
            ['CS201', 'Design and Analysis of Algorithms', 3],
            ['CS202', 'Operating Systems', 3],
            ['CS301', 'Artificial Intelligence', 3],
            ['IS101', 'Fundamentals of Information Systems', 3],
            ['IS201', 'Systems Analysis and Design', 3],
            ['IS202', 'Enterprise Architecture', 3],
            ['GEN101', 'Purposive Communication', 3],
            ['MATH101', 'College Algebra', 3],
            ['MATH201', 'Probability and Statistics', 3],
            ['CAP401', 'Computing Capstone Project', 6],
        ];

        foreach ($courses as [$courseCode, $courseTitle, $units]) {
            Course::query()->updateOrCreate(
                ['course_code' => $courseCode],
                ['course_title' => $courseTitle, 'units' => $units, 'status' => CourseStatus::Active],
            );
        }
    }
}

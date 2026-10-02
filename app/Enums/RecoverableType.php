<?php

namespace App\Enums;

use App\Models\AcademicTerm;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\CourseOfferingSchedule;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\ProgramCourse;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

enum RecoverableType: string
{
    case Users = 'users';
    case Students = 'students';
    case Programs = 'programs';
    case Courses = 'courses';
    case AcademicTerms = 'academic-terms';
    case CourseOfferings = 'course-offerings';
    case Enrollments = 'enrollments';
    case Curriculum = 'curriculum';
    case Schedules = 'schedules';

    /** @return class-string<Model> */
    public function modelClass(): string
    {
        return match ($this) {
            self::Users => User::class,
            self::Students => Student::class,
            self::Programs => Program::class,
            self::Courses => Course::class,
            self::AcademicTerms => AcademicTerm::class,
            self::CourseOfferings => CourseOffering::class,
            self::Enrollments => Enrollment::class,
            self::Curriculum => ProgramCourse::class,
            self::Schedules => CourseOfferingSchedule::class,
        };
    }

    public function searchColumns(): array
    {
        return match ($this) {
            self::Users => ['name', 'email'],
            self::Students => ['student_number', 'first_name', 'last_name'],
            self::Programs => ['code', 'name'],
            self::Courses => ['course_code', 'course_title'],
            self::AcademicTerms => ['academic_year', 'term'],
            self::CourseOfferings => ['section', 'room'],
            self::Enrollments => ['id', 'status'],
            self::Curriculum => ['id', 'semester'],
            self::Schedules => ['day_of_week', 'room'],
        };
    }

    public function relations(): array
    {
        return match ($this) {
            self::CourseOfferings => ['course'],
            self::Enrollments => ['student', 'courseOffering.course'],
            self::Curriculum => ['program', 'course'],
            self::Schedules => ['courseOffering.course'],
            default => [],
        };
    }

    public static function forModel(Model $model): self
    {
        foreach (self::cases() as $type) {
            if ($model instanceof ($type->modelClass())) {
                return $type;
            }
        }

        throw new \InvalidArgumentException('This record cannot be deleted or restored.');
    }
}

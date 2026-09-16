<?php

namespace App\Actions;

use App\Enums\CourseOfferingStatus;
use App\Enums\EnrollmentStatus;
use App\Enums\StudentStatus;
use App\Models\CourseOffering;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class EnrollStudent
{
    /** @param array<int, int> $offeringIds */
    public function execute(Student $student, array $offeringIds): Collection
    {
        if ($student->status !== StudentStatus::Active) {
            throw new ConflictHttpException('Only active students may be enrolled.');
        }

        return DB::transaction(function () use ($student, $offeringIds): Collection {
            $offerings = CourseOffering::query()->whereKey($offeringIds)->lockForUpdate()->get();

            if ($offerings->count() !== count($offeringIds)) {
                throw new ConflictHttpException('One or more course offerings are unavailable.');
            }

            $existing = Enrollment::query()->where('student_id', $student->id)->whereIn('course_offering_id', $offeringIds)->exists();
            if ($existing) {
                throw new ConflictHttpException('The student is already enrolled in one of the selected offerings.');
            }

            foreach ($offerings as $offering) {
                if ($offering->status !== CourseOfferingStatus::Open) {
                    throw new ConflictHttpException("{$offering->section} is not open for enrollment.");
                }

                $occupied = $offering->enrollments()->whereIn('status', [EnrollmentStatus::Enrolled->value, EnrollmentStatus::Completed->value])->count();
                if ($occupied >= $offering->capacity) {
                    throw new ConflictHttpException("{$offering->section} has reached capacity.");
                }
            }

            try {
                return $offerings->map(fn (CourseOffering $offering) => Enrollment::query()->create([
                    'student_id' => $student->id,
                    'course_offering_id' => $offering->id,
                    'enrollment_date' => now()->toDateString(),
                    'status' => EnrollmentStatus::Enrolled,
                ]));
            } catch (UniqueConstraintViolationException) {
                throw new ConflictHttpException('The student is already enrolled in one of the selected offerings.');
            }
        });
    }
}

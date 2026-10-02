<?php

namespace App\Actions;

use App\Enums\CourseOfferingStatus;
use App\Enums\EnrollmentStatus;
use App\Enums\StudentStatus;
use App\Models\CourseOffering;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class UpdateEnrollment
{
    public function execute(Enrollment $enrollment, array $data, bool $restore = false): Enrollment
    {
        return DB::transaction(function () use ($enrollment, $data, $restore): Enrollment {
            $offering = CourseOffering::query()->lockForUpdate()->find($data['course_offering_id'] ?? $enrollment->course_offering_id);
            $enrollment = Enrollment::withTrashed()->lockForUpdate()->findOrFail($enrollment->id);
            if (! $restore && $enrollment->trashed()) {
                throw new ConflictHttpException('This enrollment was deleted. Recover it before making changes.');
            }
            $student = Student::query()->find($data['student_id'] ?? $enrollment->student_id);
            if (! $offering || ! $student) {
                throw new ConflictHttpException('Restore the student and course offering before updating or recovering this enrollment.');
            }
            $moving = $student->id !== $enrollment->student_id || $offering->id !== $enrollment->course_offering_id;
            if ($moving && $enrollment->grade()->exists()) {
                throw new ConflictHttpException('An enrollment with grades cannot be reassigned. Its grades must remain with the original student and course.');
            }
            $duplicate = Enrollment::withTrashed()->where('student_id', $student->id)->where('course_offering_id', $offering->id)->whereKeyNot($enrollment->id)->exists();
            if ($duplicate) {
                throw new ConflictHttpException('This student already has an enrollment for the offering. Recover or update that enrollment instead.');
            }
            $status = isset($data['status']) ? EnrollmentStatus::from($data['status']) : $enrollment->status;
            $occupiesSeat = in_array($status, [EnrollmentStatus::Enrolled, EnrollmentStatus::Completed], true);
            $wasOccupying = ! $enrollment->trashed() && in_array($enrollment->status, [EnrollmentStatus::Enrolled, EnrollmentStatus::Completed], true);
            $reactivating = $status === EnrollmentStatus::Enrolled && $enrollment->status !== EnrollmentStatus::Enrolled;
            if ($occupiesSeat && ($moving || ! $wasOccupying || $restore || $reactivating)) {
                if ($student->status !== StudentStatus::Active || ! in_array($offering->status, [CourseOfferingStatus::Open, CourseOfferingStatus::Ongoing], true)) {
                    throw new ConflictHttpException('The student must be active and the offering open or ongoing to recover or reactivate an enrollment.');
                }
                $occupied = $offering->enrollments()->whereKeyNot($enrollment->id)->whereIn('status', [EnrollmentStatus::Enrolled, EnrollmentStatus::Completed])->count();
                if ($occupied >= $offering->capacity) {
                    throw new ConflictHttpException('The course offering has reached capacity.');
                }
            }
            $enrollment->fill($data)->save();
            if ($restore) {
                $enrollment->restore();
            }

            return $enrollment->refresh();
        });
    }
}

<?php

namespace App\Actions;

use App\Enums\RoleSlug;
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
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class RecycleRecord
{
    public function delete(Model $record, User $actor): void
    {
        DB::transaction(function () use ($record, $actor): void {
            $record = $record->newQuery()->lockForUpdate()->findOrFail($record->getKey());
            if ($record instanceof User && $record->is($actor)) {
                throw new ConflictHttpException('You cannot delete your own account.');
            }
            $blocked = match (true) {
                $record instanceof User => $record->student()->exists() || $record->courseOfferings()->exists(),
                $record instanceof Student => $record->enrollments()->exists(),
                $record instanceof Program => $record->students()->exists(),
                $record instanceof Course => $record->courseOfferings()->exists() || $record->programCourses()->exists(),
                $record instanceof AcademicTerm => $record->courseOfferings()->exists(),
                $record instanceof CourseOffering => $record->enrollments()->exists(),
                $record instanceof Enrollment => $record->grade()->exists(),
                default => false,
            };
            if ($blocked) {
                throw new ConflictHttpException($record instanceof Enrollment
                    ? 'Enrollments with grades cannot be deleted. Grades are permanent; update the enrollment status instead.'
                    : 'This record has linked records. Reassign or delete those records first, or change this record’s status.');
            }
            if ($record instanceof User && $record->hasRole(RoleSlug::Administrator) && $record->status->value === 'active'
                && ! User::query()->whereKeyNot($record->id)->where('status', 'active')->whereHas('role', fn ($query) => $query->where('slug', RoleSlug::Administrator))->exists()) {
                throw new ConflictHttpException('At least one active administrator must remain.');
            }
            $record->delete();
        });
    }

    public function restore(Model $record): void
    {
        if ($record instanceof Enrollment) {
            app(UpdateEnrollment::class)->execute($record, [], true);

            return;
        }
        DB::transaction(function () use ($record): void {
            $record = $record->newQuery()->onlyTrashed()->lockForUpdate()->findOrFail($record->getKey());
            $parents = match (true) {
                $record instanceof Student => [Program::query()->find($record->program_id), ...($record->user_id ? [User::query()->find($record->user_id)] : [])],
                $record instanceof CourseOffering => [Course::query()->find($record->course_id), AcademicTerm::query()->find($record->academic_term_id), ...($record->instructor_id ? [User::query()->find($record->instructor_id)] : [])],
                $record instanceof ProgramCourse => [Program::query()->find($record->program_id), Course::query()->find($record->course_id)],
                $record instanceof CourseOfferingSchedule => [CourseOffering::query()->find($record->course_offering_id)],
                default => [],
            };
            if (in_array(null, $parents, true)) {
                throw new ConflictHttpException('Restore the linked parent records first, then recover this record.');
            }
            $record->restore();
        });
    }
}

<?php

namespace App\Actions;

use App\Enums\EnrollmentStatus;
use App\Models\CourseOffering;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class SaveCourseOffering
{
    public function execute(array $data, ?CourseOffering $offering = null): CourseOffering
    {
        return DB::transaction(function () use ($data, $offering): CourseOffering {
            $offering = $offering ? CourseOffering::query()->lockForUpdate()->findOrFail($offering->id) : new CourseOffering;
            $occupied = $offering->exists
                ? $offering->enrollments()->whereIn('status', [EnrollmentStatus::Enrolled->value, EnrollmentStatus::Completed->value])->count()
                : 0;

            if ((int) $data['capacity'] < $occupied) {
                throw new ConflictHttpException('Capacity cannot be lower than the current enrollment count.');
            }

            if ($offering->exists && $offering->enrollments()->withTrashed()->exists()
                && ($offering->course_id !== (int) $data['course_id'] || $offering->academic_term_id !== (int) $data['academic_term_id'])) {
                throw new ConflictHttpException('An offering with enrollment history cannot change its course or academic term.');
            }
            $offering->fill(Arr::except($data, 'schedules'))->save();
            $retained = [];
            foreach ($data['schedules'] as $row) {
                $key = Arr::only($row, ['day_of_week', 'start_time', 'end_time']);
                // SQL time columns use seconds; normalize before matching archived schedules.
                $key['start_time'] .= ':00';
                $key['end_time'] .= ':00';
                $schedule = $offering->schedules()->withTrashed()->firstOrNew($key);
                $schedule->fill([...$row, ...$key])->save();
                if ($schedule->trashed()) {
                    $schedule->restore();
                }
                $retained[] = $schedule->id;
            }
            $offering->schedules()->whereNotIn('id', $retained)->delete();

            return $offering->refresh();
        });
    }
}

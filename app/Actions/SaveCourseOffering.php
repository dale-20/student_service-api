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
            $offering ??= new CourseOffering;
            $occupied = $offering->exists
                ? $offering->enrollments()->whereIn('status', [EnrollmentStatus::Enrolled->value, EnrollmentStatus::Completed->value])->count()
                : 0;

            if ((int) $data['capacity'] < $occupied) {
                throw new ConflictHttpException('Capacity cannot be lower than the current enrollment count.');
            }

            $offering->fill(Arr::except($data, 'schedules'))->save();
            $offering->schedules()->delete();
            $offering->schedules()->createMany($data['schedules']);

            return $offering->refresh();
        });
    }
}

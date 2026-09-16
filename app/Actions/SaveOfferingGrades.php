<?php

namespace App\Actions;

use App\Enums\EnrollmentStatus;
use App\Models\CourseOffering;
use App\Models\Grade;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class SaveOfferingGrades
{
    public function execute(CourseOffering $offering, array $rows): Collection
    {
        return DB::transaction(function () use ($offering, $rows): Collection {
            $enrollments = $offering->enrollments()->whereKey(collect($rows)->pluck('enrollment_id'))->lockForUpdate()->get()->keyBy('id');
            if ($enrollments->count() !== count($rows)) {
                throw new ConflictHttpException('Every grade must belong to the selected course offering.');
            }

            return collect($rows)->map(function (array $row) use ($enrollments): Grade {
                $enrollment = $enrollments->get($row['enrollment_id']);
                if (! in_array($enrollment->status, [EnrollmentStatus::Enrolled, EnrollmentStatus::Completed], true)) {
                    throw new ConflictHttpException('Dropped or withdrawn enrollments cannot be graded.');
                }

                return Grade::query()->updateOrCreate(['enrollment_id' => $enrollment->id], collect($row)->except('enrollment_id')->all());
            });
        });
    }
}

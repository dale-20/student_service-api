<?php

namespace App\Models;

use App\Enums\DayOfWeek;
use Database\Factories\CourseOfferingScheduleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['course_offering_id', 'day_of_week', 'start_time', 'end_time', 'room'])]
class CourseOfferingSchedule extends Model
{
    /** @use HasFactory<CourseOfferingScheduleFactory> */
    use HasFactory;

    public function courseOffering(): BelongsTo
    {
        return $this->belongsTo(CourseOffering::class);
    }

    protected function casts(): array
    {
        return [
            'day_of_week' => DayOfWeek::class,
        ];
    }
}

<?php

namespace App\Models;

use App\Enums\CourseOfferingStatus;
use Database\Factories\CourseOfferingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'course_id',
    'academic_term_id',
    'instructor_id',
    'section',
    'room',
    'schedule',
    'capacity',
    'status',
])]
class CourseOffering extends Model
{
    /** @use HasFactory<CourseOfferingFactory> */
    use HasFactory;

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function academicTerm(): BelongsTo
    {
        return $this->belongsTo(AcademicTerm::class);
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(CourseOfferingSchedule::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'enrollments')
            ->withPivot(['enrollment_date', 'status'])
            ->withTimestamps();
    }

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'status' => CourseOfferingStatus::class,
        ];
    }
}

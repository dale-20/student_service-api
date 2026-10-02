<?php

namespace App\Models;

use App\Enums\EnrollmentStatus;
use Database\Factories\EnrollmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['student_id', 'course_offering_id', 'enrollment_date', 'status'])]
class Enrollment extends Model
{
    /** @use HasFactory<EnrollmentFactory> */
    use HasFactory;

    use SoftDeletes;

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class)->withTrashed();
    }

    public function courseOffering(): BelongsTo
    {
        return $this->belongsTo(CourseOffering::class)->withTrashed();
    }

    public function grade(): HasOne
    {
        return $this->hasOne(Grade::class);
    }

    protected function casts(): array
    {
        return [
            'enrollment_date' => 'date',
            'status' => EnrollmentStatus::class,
        ];
    }
}

<?php

namespace App\Models;

use App\Enums\CourseStatus;
use Database\Factories\CourseFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['course_code', 'course_title', 'description', 'units', 'status'])]
class Course extends Model
{
    /** @use HasFactory<CourseFactory> */
    use HasFactory;

    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(Program::class, 'program_courses')
            ->withPivot(['year_level', 'semester', 'is_required'])
            ->withTimestamps();
    }

    public function programCourses(): HasMany
    {
        return $this->hasMany(ProgramCourse::class);
    }

    public function courseOfferings(): HasMany
    {
        return $this->hasMany(CourseOffering::class);
    }

    protected function casts(): array
    {
        return [
            'units' => 'decimal:2',
            'status' => CourseStatus::class,
        ];
    }
}

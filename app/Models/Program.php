<?php

namespace App\Models;

use App\Enums\ProgramStatus;
use Database\Factories\ProgramFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['code', 'name', 'description', 'status'])]
class Program extends Model
{
    /** @use HasFactory<ProgramFactory> */
    use HasFactory;

    use SoftDeletes;

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'program_courses')
            ->wherePivotNull('deleted_at')
            ->withPivot(['year_level', 'semester', 'is_required'])
            ->withTimestamps();
    }

    public function programCourses(): HasMany
    {
        return $this->hasMany(ProgramCourse::class);
    }

    protected function casts(): array
    {
        return [
            'status' => ProgramStatus::class,
        ];
    }
}

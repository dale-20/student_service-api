<?php

namespace App\Models;

use App\Enums\Semester;
use Database\Factories\ProgramCourseFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['program_id', 'course_id', 'year_level', 'semester', 'is_required'])]
class ProgramCourse extends Model
{
    /** @use HasFactory<ProgramCourseFactory> */
    use HasFactory;

    use SoftDeletes;

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class)->withTrashed();
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class)->withTrashed();
    }

    protected function casts(): array
    {
        return [
            'year_level' => 'integer',
            'semester' => Semester::class,
            'is_required' => 'boolean',
        ];
    }
}

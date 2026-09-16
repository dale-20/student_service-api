<?php

namespace App\Models;

use App\Enums\GradeRemark;
use Database\Factories\GradeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['enrollment_id', 'midterm_grade', 'final_grade', 'remarks'])]
class Grade extends Model
{
    /** @use HasFactory<GradeFactory> */
    use HasFactory;

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    protected function casts(): array
    {
        return [
            'midterm_grade' => 'decimal:2',
            'final_grade' => 'decimal:2',
            'remarks' => GradeRemark::class,
        ];
    }
}

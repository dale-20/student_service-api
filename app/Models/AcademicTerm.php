<?php

namespace App\Models;

use App\Enums\AcademicTermStatus;
use App\Enums\AcademicTermType;
use Database\Factories\AcademicTermFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['academic_year', 'term', 'start_date', 'end_date', 'status'])]
class AcademicTerm extends Model
{
    /** @use HasFactory<AcademicTermFactory> */
    use HasFactory;

    use SoftDeletes;

    public function courseOfferings(): HasMany
    {
        return $this->hasMany(CourseOffering::class);
    }

    protected function casts(): array
    {
        return [
            'term' => AcademicTermType::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'status' => AcademicTermStatus::class,
        ];
    }
}

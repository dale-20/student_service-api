<?php

namespace Database\Factories;

use App\Enums\AcademicTermStatus;
use App\Enums\AcademicTermType;
use App\Models\AcademicTerm;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<AcademicTerm> */
class AcademicTermFactory extends Factory
{
    public function definition(): array
    {
        $startYear = fake()->unique()->numberBetween(2030, 2090);

        return [
            'academic_year' => "{$startYear}-".($startYear + 1),
            'term' => AcademicTermType::FirstSemester,
            'start_date' => "{$startYear}-06-01",
            'end_date' => "{$startYear}-10-31",
            'status' => AcademicTermStatus::Upcoming,
        ];
    }
}

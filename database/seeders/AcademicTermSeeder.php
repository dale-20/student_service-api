<?php

namespace Database\Seeders;

use App\Enums\AcademicTermStatus;
use App\Enums\AcademicTermType;
use App\Models\AcademicTerm;
use Illuminate\Database\Seeder;

class AcademicTermSeeder extends Seeder
{
    public function run(): void
    {
        $terms = [
            ['term' => AcademicTermType::FirstSemester, 'start_date' => '2026-06-01', 'end_date' => '2026-10-31', 'status' => AcademicTermStatus::Active],
            ['term' => AcademicTermType::SecondSemester, 'start_date' => '2026-11-01', 'end_date' => '2027-03-31', 'status' => AcademicTermStatus::Upcoming],
        ];

        foreach ($terms as $term) {
            AcademicTerm::query()->updateOrCreate(
                ['academic_year' => '2026-2027', 'term' => $term['term']],
                $term,
            );
        }
    }
}

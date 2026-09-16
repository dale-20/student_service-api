<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum AcademicTermType: string
{
    use HasValues;

    case FirstSemester = 'first_semester';
    case SecondSemester = 'second_semester';
    case Summer = 'summer';
}

<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum AcademicTermStatus: string
{
    use HasValues;

    case Upcoming = 'upcoming';
    case Active = 'active';
    case Completed = 'completed';
}

<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum EnrollmentStatus: string
{
    use HasValues;

    case Enrolled = 'enrolled';
    case Dropped = 'dropped';
    case Withdrawn = 'withdrawn';
    case Completed = 'completed';
}

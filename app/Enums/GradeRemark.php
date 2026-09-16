<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum GradeRemark: string
{
    use HasValues;

    case Passed = 'passed';
    case Failed = 'failed';
    case Incomplete = 'incomplete';
    case Dropped = 'dropped';
}

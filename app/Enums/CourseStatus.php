<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum CourseStatus: string
{
    use HasValues;

    case Active = 'active';
    case Inactive = 'inactive';
}

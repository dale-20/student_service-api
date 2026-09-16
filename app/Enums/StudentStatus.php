<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum StudentStatus: string
{
    use HasValues;

    case Active = 'active';
    case Inactive = 'inactive';
    case Graduated = 'graduated';
    case Dropped = 'dropped';
}

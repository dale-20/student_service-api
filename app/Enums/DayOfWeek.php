<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum DayOfWeek: string
{
    use HasValues;

    case Monday = 'monday';
    case Tuesday = 'tuesday';
    case Wednesday = 'wednesday';
    case Thursday = 'thursday';
    case Friday = 'friday';
    case Saturday = 'saturday';
    case Sunday = 'sunday';
}

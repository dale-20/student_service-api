<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum Semester: string
{
    use HasValues;

    case First = 'first';
    case Second = 'second';
    case Summer = 'summer';
}

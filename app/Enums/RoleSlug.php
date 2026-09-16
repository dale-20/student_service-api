<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum RoleSlug: string
{
    use HasValues;

    case Administrator = 'administrator';
    case Registrar = 'registrar';
    case Instructor = 'instructor';
    case Student = 'student';
}

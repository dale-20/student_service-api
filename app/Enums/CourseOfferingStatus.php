<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum CourseOfferingStatus: string
{
    use HasValues;

    case Open = 'open';
    case Closed = 'closed';
    case Ongoing = 'ongoing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}

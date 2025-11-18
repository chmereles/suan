<?php

namespace App\Domain\Attendance\Enums;

enum AnomalyType: string
{
    case NO_MARKS = 'no_marks';
    case SINGLE_MARK = 'single_mark';
    case DUPLICATE_MARKS = 'duplicate_marks';
    case OUT_OF_ORDER = 'out_of_order';
    case LARGE_GAP = 'large_gap';
    case MISSING_CHECKOUT = 'missing_checkout';

    // Si mañana agregás más:
    // case MISSING_CHECKIN  = 'missing_checkin';
    // case CORRUPTED        = 'corrupted';
}

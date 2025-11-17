<?php

namespace App\Domain\Attendance\Enums;

enum DailyStatus: string
{
    case PRESENT             = 'present';
    case ABSENT_UNJUSTIFIED  = 'absent_unjustified';
    case ABSENT_JUSTIFIED    = 'absent_justified';
    case LICENSE             = 'license';
    case PARTIAL             = 'partial';
    case HOLIDAY             = 'holiday';
    case ANOMALY             = 'anomaly';
}

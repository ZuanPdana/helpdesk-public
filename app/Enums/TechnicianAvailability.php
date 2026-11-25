<?php

namespace App\Enums;

enum TechnicianAvailability: string
{
    case Available = 'available';
    case Busy = 'busy';
    case Away = 'away';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Available',
            self::Busy => 'Busy',
            self::Away => 'Away',
        };
    }
}

<?php

namespace App\Enums;

enum EventStatus: string
{
    case ACTIVE = 'active';
    case PENDING = 'pending';
    case SUSPENDED = 'suspended';
    case CANCELLED = 'cancelled';
    case ENDED = 'ended';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::PENDING => 'Pending',
            self::SUSPENDED => 'Suspended',
            self::CANCELLED => 'Cancelled',
            self::ENDED => 'SEnded',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

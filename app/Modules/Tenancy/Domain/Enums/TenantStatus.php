<?php

namespace App\Modules\Tenancy\Domain\Enums;

enum TenantStatus: string
{
    case Active = 'active';
    case Trial = 'trial';
    case Suspended = 'suspended';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Active => 'Active',
            self::Trial => 'Trial',
            self::Suspended => 'Suspended',
            self::Cancelled => 'Cancelled',
        };
    }

    public function isAccessible(): bool
    {
        return in_array($this, [self::Active, self::Trial]);
    }
}

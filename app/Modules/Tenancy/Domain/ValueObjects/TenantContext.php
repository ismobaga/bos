<?php

namespace App\Modules\Tenancy\Domain\ValueObjects;

use App\Modules\Tenancy\Domain\Models\Tenant;

final class TenantContext
{
    public function __construct(
        public readonly Tenant $tenant,
        public readonly ?int $branchId = null,
    ) {}

    public function id(): int
    {
        return $this->tenant->id;
    }

    public function isActive(): bool
    {
        return $this->tenant->isActive();
    }

    public function isTrial(): bool
    {
        return $this->tenant->isTrial();
    }
}

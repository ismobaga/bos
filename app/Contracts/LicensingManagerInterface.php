<?php

namespace App\Contracts;

interface LicensingManagerInterface
{
    public function moduleEnabled(int $tenantId, string $moduleKey): bool;

    public function featureEnabled(int $tenantId, string $featureKey): bool;

    public function getLimit(int $tenantId, string $key): int|float|null;

    public function canConsume(int $tenantId, string $key, int $amount = 1): bool;

    public function consume(int $tenantId, string $key, int $amount = 1): void;
}

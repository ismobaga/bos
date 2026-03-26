<?php

namespace App\Modules\Licensing\Application\Services;

use App\Contracts\LicensingManagerInterface;
use App\Modules\Licensing\Domain\Models\TenantEntitlement;
use App\Modules\Licensing\Domain\Models\UsageCounter;
use App\Modules\Licensing\Domain\Exceptions\UsageLimitExceededException;
use Illuminate\Support\Facades\Cache;

class LicensingManager implements LicensingManagerInterface
{
    public function moduleEnabled(int $tenantId, string $moduleKey): bool
    {
        return (bool) $this->getEntitlementValue($tenantId, "module.{$moduleKey}.enabled");
    }

    public function featureEnabled(int $tenantId, string $featureKey): bool
    {
        return (bool) $this->getEntitlementValue($tenantId, $featureKey);
    }

    public function getLimit(int $tenantId, string $key): int|float|null
    {
        $value = $this->getEntitlementValue($tenantId, $key);

        return $value !== null ? (int) $value : null;
    }

    public function canConsume(int $tenantId, string $key, int $amount = 1): bool
    {
        $limit = $this->getLimit($tenantId, $key);

        if ($limit === null) {
            return true; // no limit
        }

        $used = $this->getUsed($tenantId, $key);

        return ($used + $amount) <= $limit;
    }

    public function consume(int $tenantId, string $key, int $amount = 1): void
    {
        if (! $this->canConsume($tenantId, $key, $amount)) {
            throw new UsageLimitExceededException("Usage limit exceeded for {$key}.");
        }

        $counter = UsageCounter::firstOrCreate(
            [
                'tenant_id' => $tenantId,
                'key' => $key,
                'period_start' => now()->startOfMonth(),
            ],
            [
                'period_end' => now()->endOfMonth(),
                'used_value' => 0,
                'reset_strategy' => 'monthly',
            ]
        );

        $counter->increment('used_value', $amount);

        Cache::forget("usage:{$tenantId}:{$key}");
    }

    private function getEntitlementValue(int $tenantId, string $key): mixed
    {
        return Cache::remember(
            "entitlement:{$tenantId}:{$key}",
            now()->addMinutes(15),
            function () use ($tenantId, $key) {
                $entitlement = TenantEntitlement::where('tenant_id', $tenantId)
                    ->where('key', $key)
                    ->first();

                if (! $entitlement || ! $entitlement->isEffective()) {
                    return null;
                }

                return $entitlement->getValue();
            }
        );
    }

    private function getUsed(int $tenantId, string $key): int
    {
        return Cache::remember(
            "usage:{$tenantId}:{$key}",
            now()->addMinutes(5),
            function () use ($tenantId, $key) {
                return UsageCounter::where('tenant_id', $tenantId)
                    ->where('key', $key)
                    ->where('period_start', '<=', now())
                    ->where(function ($q) {
                        $q->whereNull('period_end')->orWhere('period_end', '>=', now());
                    })
                    ->sum('used_value');
            }
        );
    }
}

<?php

namespace App\Modules\Licensing\Infrastructure\Listeners;

use App\Modules\Licensing\Application\Services\LicensingManager;
use App\Modules\Licensing\Domain\Events\SubscriptionChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;

class RecomputeTenantEntitlements implements ShouldQueue
{
    public function handle(SubscriptionChanged $event): void
    {
        $tenantId = $event->subscription->tenant_id;

        // Flush all cached entitlements for this tenant
        // In a real implementation, we would use tagged cache or pattern deletion
        // For now, we flush using a tenant-specific cache prefix scan
        $this->flushTenantEntitlementCache($tenantId);
    }

    private function flushTenantEntitlementCache(int $tenantId): void
    {
        // Clear common entitlement keys
        $keys = [
            "module.crm.enabled",
            "module.invoice.enabled",
            "module.lms.enabled",
            "module.helpdesk.enabled",
            "module.hosting.enabled",
            "module.automation.enabled",
            "module.cmail.enabled",
            "module.fleet.enabled",
            "invoice.monthly_limit",
            "users.limit",
            "storage.gb",
            "lms.learners.limit",
        ];

        foreach ($keys as $key) {
            Cache::forget("entitlement:{$tenantId}:{$key}");
        }
    }
}

<?php

use App\Modules\Licensing\Application\Services\LicensingManager;
use App\Modules\Licensing\Domain\Models\TenantEntitlement;
use App\Modules\Licensing\Domain\Models\UsageCounter;
use Illuminate\Support\Facades\Cache;

describe('LicensingManager', function () {
    beforeEach(function () {
        Cache::flush();
    });

    it('returns false when module entitlement is missing', function () {
        $manager = app(LicensingManager::class);

        expect($manager->moduleEnabled(999, 'invoice'))->toBeFalse();
    });

    it('returns true when module is enabled via entitlement', function () {
        TenantEntitlement::factory()->create([
            'tenant_id' => 1,
            'key' => 'module.invoice.enabled',
            'value_type' => 'boolean',
            'value_boolean' => true,
        ]);

        $manager = app(LicensingManager::class);

        expect($manager->moduleEnabled(1, 'invoice'))->toBeTrue();
    });

    it('can consume usage when below limit', function () {
        TenantEntitlement::factory()->create([
            'tenant_id' => 1,
            'key' => 'invoice.monthly_limit',
            'value_type' => 'integer',
            'value_integer' => 100,
        ]);

        $manager = app(LicensingManager::class);

        expect($manager->canConsume(1, 'invoice.monthly_limit', 1))->toBeTrue();
    });

    it('blocks consumption when at limit', function () {
        TenantEntitlement::factory()->create([
            'tenant_id' => 1,
            'key' => 'invoice.monthly_limit',
            'value_type' => 'integer',
            'value_integer' => 5,
        ]);

        UsageCounter::factory()->create([
            'tenant_id' => 1,
            'key' => 'invoice.monthly_limit',
            'used_value' => 5,
            'period_start' => now()->startOfMonth(),
            'period_end' => now()->endOfMonth(),
        ]);

        $manager = app(LicensingManager::class);

        expect($manager->canConsume(1, 'invoice.monthly_limit', 1))->toBeFalse();
    });
});

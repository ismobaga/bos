<?php

use App\Modules\Tenancy\Domain\Enums\TenantStatus;
use App\Modules\Tenancy\Domain\Models\Tenant;

describe('Tenant model', function () {
    it('reports correct active status', function () {
        $tenant = new Tenant(['status' => 'active']);
        expect($tenant->isActive())->toBeTrue();
        expect($tenant->isTrial())->toBeFalse();
        expect($tenant->isSuspended())->toBeFalse();
    });

    it('reports correct trial status', function () {
        $tenant = new Tenant(['status' => 'trial']);
        expect($tenant->isActive())->toBeFalse();
        expect($tenant->isTrial())->toBeTrue();
    });

    it('reports correct suspended status', function () {
        $tenant = new Tenant(['status' => 'suspended']);
        expect($tenant->isSuspended())->toBeTrue();
    });
});

describe('TenantStatus enum', function () {
    it('correctly identifies accessible statuses', function () {
        expect(TenantStatus::Active->isAccessible())->toBeTrue();
        expect(TenantStatus::Trial->isAccessible())->toBeTrue();
        expect(TenantStatus::Suspended->isAccessible())->toBeFalse();
        expect(TenantStatus::Cancelled->isAccessible())->toBeFalse();
    });

    it('returns correct labels', function () {
        expect(TenantStatus::Active->label())->toBe('Active');
        expect(TenantStatus::Trial->label())->toBe('Trial');
        expect(TenantStatus::Suspended->label())->toBe('Suspended');
        expect(TenantStatus::Cancelled->label())->toBe('Cancelled');
    });
});

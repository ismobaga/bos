<?php

namespace Database\Factories;

use App\Modules\Licensing\Domain\Models\TenantEntitlement;
use App\Modules\Tenancy\Domain\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TenantEntitlement>
 */
class TenantEntitlementFactory extends Factory
{
    protected $model = TenantEntitlement::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'key' => 'module.invoice.enabled',
            'value_type' => 'boolean',
            'value_boolean' => true,
        ];
    }
}

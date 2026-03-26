<?php

namespace Database\Factories;

use App\Modules\Licensing\Domain\Models\UsageCounter;
use App\Modules\Tenancy\Domain\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UsageCounter>
 */
class UsageCounterFactory extends Factory
{
    protected $model = UsageCounter::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'key' => 'invoice.monthly_limit',
            'used_value' => 0,
            'period_start' => now()->startOfMonth(),
            'period_end' => now()->endOfMonth(),
            'reset_strategy' => 'monthly',
        ];
    }
}

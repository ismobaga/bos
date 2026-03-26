<?php

namespace Database\Seeders;

use App\Modules\Licensing\Domain\Models\Plan;
use App\Modules\Licensing\Domain\Models\ProductModule;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    private const PLANS = [
        [
            'key' => 'starter',
            'name' => 'Starter',
            'billing_period' => 'monthly',
            'price_minor' => 0,
            'currency' => 'USD',
            'modules' => ['crm', 'invoice'],
        ],
        [
            'key' => 'business',
            'name' => 'Business',
            'billing_period' => 'monthly',
            'price_minor' => 4900,
            'currency' => 'USD',
            'modules' => ['crm', 'invoice', 'lms', 'helpdesk', 'automation'],
        ],
        [
            'key' => 'enterprise',
            'name' => 'Enterprise',
            'billing_period' => 'monthly',
            'price_minor' => 14900,
            'currency' => 'USD',
            'modules' => ['crm', 'invoice', 'lms', 'helpdesk', 'hosting', 'automation', 'docs', 'fleet', 'cmail'],
        ],
    ];

    public function run(): void
    {
        foreach (self::PLANS as $planData) {
            $modules = $planData['modules'];
            unset($planData['modules']);

            $plan = Plan::updateOrCreate(
                ['key' => $planData['key']],
                array_merge($planData, ['is_active' => true])
            );

            $moduleIds = ProductModule::whereIn('key', $modules)->pluck('id');
            $plan->modules()->sync($moduleIds);
        }
    }
}

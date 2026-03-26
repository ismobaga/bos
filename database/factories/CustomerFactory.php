<?php

namespace Database\Factories;

use App\Modules\CRM\Domain\Models\Customer;
use App\Modules\Tenancy\Domain\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'name' => fake()->company(),
            'type' => fake()->randomElement(['individual', 'company']),
            'email' => fake()->unique()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'status' => 'active',
        ];
    }
}

<?php

namespace Database\Factories;

use App\Modules\Invoice\Domain\Models\Invoice;
use App\Modules\Tenancy\Domain\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        $subtotal = fake()->numberBetween(10000, 500000);

        return [
            'tenant_id' => Tenant::factory(),
            'number' => 'INV-' . fake()->unique()->numberBetween(1000, 9999),
            'status' => 'draft',
            'currency' => 'USD',
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'subtotal_minor' => $subtotal,
            'tax_minor' => 0,
            'total_minor' => $subtotal,
            'amount_paid_minor' => 0,
            'balance_due_minor' => $subtotal,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'amount_paid_minor' => $attributes['total_minor'],
            'balance_due_minor' => 0,
        ]);
    }
}

<?php

use App\Models\User;
use App\Modules\Invoice\Domain\Models\Invoice;
use App\Modules\Licensing\Domain\Models\TenantEntitlement;
use App\Modules\Tenancy\Domain\Models\Tenant;
use App\Modules\Tenancy\Domain\ValueObjects\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Invoice API', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->tenant = Tenant::factory()->create(['status' => 'active']);

        // Enable invoice module
        TenantEntitlement::factory()->create([
            'tenant_id' => $this->tenant->id,
            'key' => 'module.invoice.enabled',
            'value_type' => 'boolean',
            'value_boolean' => true,
        ]);

        // Set invoice monthly limit
        TenantEntitlement::factory()->create([
            'tenant_id' => $this->tenant->id,
            'key' => 'invoice.monthly_limit',
            'value_type' => 'integer',
            'value_integer' => 100,
        ]);

        // Bind tenant context
        app()->instance(TenantContext::class, new TenantContext($this->tenant));
    });

    it('returns list of invoices for tenant', function () {
        Invoice::factory()->count(3)->create(['tenant_id' => $this->tenant->id]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/invoice/invoices');

        $response->assertOk();
        $response->assertJsonCount(3, 'data');
    });

    it('creates an invoice successfully', function () {
        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/invoice/invoices', [
                'currency' => 'USD',
                'issue_date' => '2024-01-01',
                'due_date' => '2024-01-31',
                'items' => [
                    [
                        'description' => 'Service Fee',
                        'quantity' => 1,
                        'unit_price_minor' => 50000,
                        'tax_rate' => 0,
                    ],
                ],
            ]);

        $response->assertCreated();
        $response->assertJsonPath('status', 'draft');
    });
});

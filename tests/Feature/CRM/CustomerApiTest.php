<?php

use App\Models\User;
use App\Modules\CRM\Domain\Models\Customer;
use App\Modules\Licensing\Domain\Models\TenantEntitlement;
use App\Modules\Tenancy\Domain\Models\Tenant;
use App\Modules\Tenancy\Domain\ValueObjects\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('CRM Customer API', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->tenant = Tenant::factory()->create(['status' => 'active']);

        // Enable CRM module
        TenantEntitlement::factory()->create([
            'tenant_id' => $this->tenant->id,
            'key' => 'module.crm.enabled',
            'value_type' => 'boolean',
            'value_boolean' => true,
        ]);

        app()->instance(TenantContext::class, new TenantContext($this->tenant));
    });

    it('lists customers for the current tenant', function () {
        Customer::factory()->count(5)->create(['tenant_id' => $this->tenant->id]);
        Customer::factory()->count(2)->create(); // other tenant

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/crm/customers');

        $response->assertOk();
        $response->assertJsonCount(5, 'data');
    });

    it('creates a customer', function () {
        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/crm/customers', [
                'name' => 'Acme Corp',
                'type' => 'company',
                'email' => 'acme@example.com',
            ]);

        $response->assertCreated();
        $response->assertJsonPath('name', 'Acme Corp');
        $response->assertJsonPath('tenant_id', $this->tenant->id);
    });

    it('denies access to customers from another tenant', function () {
        $otherTenant = Tenant::factory()->create();
        $customer = Customer::factory()->create(['tenant_id' => $otherTenant->id]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/crm/customers/{$customer->id}");

        $response->assertForbidden();
    });
});

<?php

namespace App\Modules\CRM\Application\Actions;

use App\Modules\CRM\Domain\Events\CustomerCreated;
use App\Modules\CRM\Domain\Models\Customer;

class CreateCustomerAction
{
    public function execute(int $tenantId, int $userId, array $data): Customer
    {
        $customer = Customer::create([
            'tenant_id' => $tenantId,
            'name' => $data['name'],
            'type' => $data['type'] ?? 'individual',
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'status' => $data['status'] ?? 'active',
            'billing_address_json' => $data['billing_address'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);

        CustomerCreated::dispatch($tenantId, $customer->id);

        return $customer;
    }
}

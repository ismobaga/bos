<?php

namespace App\Modules\CRM\Application\Actions;

use App\Modules\CRM\Domain\Models\Customer;

class UpdateCustomerAction
{
    public function execute(Customer $customer, int $userId, array $data): Customer
    {
        $customer->update(array_merge($data, ['updated_by' => $userId]));

        return $customer->refresh();
    }
}

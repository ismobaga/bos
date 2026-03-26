<?php

namespace App\Modules\Tenancy\Application\Actions;

use App\Modules\AccessControl\Domain\Models\Membership;
use App\Modules\Tenancy\Domain\Events\TenantCreated;
use App\Modules\Tenancy\Domain\Models\Tenant;

class CreateTenantAction
{
    public function execute(int $ownerId, array $data): Tenant
    {
        $tenant = Tenant::create([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'status' => 'trial',
            'timezone' => $data['timezone'] ?? 'UTC',
            'locale' => $data['locale'] ?? 'en',
            'currency' => $data['currency'] ?? 'USD',
            'country_code' => $data['country_code'] ?? null,
            'owner_user_id' => $ownerId,
            'trial_ends_at' => now()->addDays(14),
        ]);

        // Add owner as a member
        Membership::create([
            'tenant_id' => $tenant->id,
            'user_id' => $ownerId,
            'status' => 'active',
            'joined_at' => now(),
        ]);

        TenantCreated::dispatch($tenant);

        return $tenant;
    }
}

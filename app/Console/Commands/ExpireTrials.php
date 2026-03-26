<?php

namespace App\Console\Commands;

use App\Modules\Tenancy\Domain\Models\Tenant;
use Illuminate\Console\Command;

class ExpireTrials extends Command
{
    protected $signature = 'crommix:expire-trials';
    protected $description = 'Expire trial tenants whose trial period has ended';

    public function handle(): int
    {
        $expired = Tenant::where('status', 'trial')
            ->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '<', now())
            ->get();

        foreach ($expired as $tenant) {
            $tenant->update(['status' => 'suspended', 'suspended_at' => now()]);
            $this->line("Expired trial for tenant: {$tenant->name} ({$tenant->slug})");
        }

        $this->info("Expired {$expired->count()} trial tenants.");

        return self::SUCCESS;
    }
}

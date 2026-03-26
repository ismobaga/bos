<?php

namespace App\Console\Commands;

use App\Modules\Licensing\Domain\Models\UsageCounter;
use Illuminate\Console\Command;

class ResetMonthlyUsageCounters extends Command
{
    protected $signature = 'crommix:reset-monthly-counters';
    protected $description = 'Reset monthly usage counters for all tenants';

    public function handle(): int
    {
        $count = UsageCounter::where('reset_strategy', 'monthly')
            ->where('period_end', '<', now())
            ->count();

        UsageCounter::where('reset_strategy', 'monthly')
            ->where('period_end', '<', now())
            ->delete();

        $this->info("Reset {$count} monthly usage counters.");

        return self::SUCCESS;
    }
}

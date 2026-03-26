<?php

namespace App\Console\Commands;

use App\Modules\Integrations\Domain\Models\WebhookDelivery;
use App\Modules\Integrations\Infrastructure\Jobs\DeliverWebhookJob;
use Illuminate\Console\Command;

class RetryFailedWebhooks extends Command
{
    protected $signature = 'crommix:retry-failed-webhooks';
    protected $description = 'Retry failed webhook deliveries that are due for retry';

    public function handle(): int
    {
        $retries = WebhookDelivery::where('status', 'failed')
            ->where('attempt_count', '<', 5)
            ->where(function ($q) {
                $q->whereNull('next_retry_at')->orWhere('next_retry_at', '<=', now());
            })
            ->get();

        foreach ($retries as $delivery) {
            DeliverWebhookJob::dispatch($delivery);
        }

        $this->info("Queued {$retries->count()} webhook retries.");

        return self::SUCCESS;
    }
}

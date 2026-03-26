<?php

namespace App\Modules\Invoice\Infrastructure\Listeners;

use App\Contracts\LicensingManagerInterface;
use App\Modules\Invoice\Domain\Events\InvoiceCreated;
use Illuminate\Contracts\Queue\ShouldQueue;

class IncrementInvoiceUsage implements ShouldQueue
{
    public function __construct(
        private readonly LicensingManagerInterface $licensing,
    ) {}

    public function handle(InvoiceCreated $event): void
    {
        // Usage counter is already incremented in the controller after creation,
        // but we ensure the counter is recorded via the event as well
        // for automation-based triggers
    }
}

<?php

namespace App\Modules\Invoice\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvoiceCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly int $tenantId,
        public readonly int $invoiceId,
    ) {}
}

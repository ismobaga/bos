<?php

namespace App\Console\Commands;

use App\Modules\Invoice\Domain\Events\InvoiceOverdue;
use App\Modules\Invoice\Domain\Models\Invoice;
use Illuminate\Console\Command;

class DetectOverdueInvoices extends Command
{
    protected $signature = 'crommix:detect-overdue-invoices';
    protected $description = 'Detect overdue invoices and dispatch InvoiceOverdue events';

    public function handle(): int
    {
        $overdue = Invoice::where('status', 'sent')
            ->whereNotNull('due_date')
            ->where('due_date', '<', today())
            ->where('balance_due_minor', '>', 0)
            ->get();

        foreach ($overdue as $invoice) {
            $invoice->update(['status' => 'overdue']);
            InvoiceOverdue::dispatch($invoice->tenant_id, $invoice->id);
        }

        $this->info("Processed {$overdue->count()} overdue invoices.");

        return self::SUCCESS;
    }
}

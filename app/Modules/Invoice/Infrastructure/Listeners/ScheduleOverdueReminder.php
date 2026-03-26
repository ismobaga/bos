<?php

namespace App\Modules\Invoice\Infrastructure\Listeners;

use App\Contracts\NotificationDispatcherInterface;
use App\Modules\Invoice\Domain\Events\InvoiceOverdue;
use App\Modules\Invoice\Domain\Models\Invoice;
use App\Modules\Notifications\Application\DTOs\NotificationMessageData;
use Illuminate\Contracts\Queue\ShouldQueue;

class ScheduleOverdueReminder implements ShouldQueue
{
    public function __construct(
        private readonly NotificationDispatcherInterface $notifications,
    ) {}

    public function handle(InvoiceOverdue $event): void
    {
        $invoice = Invoice::with('customer', 'tenant')->find($event->invoiceId);

        if (! $invoice || ! $invoice->customer) {
            return;
        }

        $to = $invoice->customer->email;

        if (! $to) {
            return;
        }

        $this->notifications->send(NotificationMessageData::make([
            'tenant_id' => $event->tenantId,
            'channel' => 'email',
            'to' => $to,
            'subject' => "Invoice {$invoice->number} is overdue",
            'body' => "Your invoice {$invoice->number} is overdue. Please arrange payment at your earliest convenience.",
            'template_key' => 'invoice.overdue',
            'metadata' => ['invoice_id' => $invoice->id],
        ]));
    }
}

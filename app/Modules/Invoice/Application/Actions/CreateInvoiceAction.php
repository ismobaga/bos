<?php

namespace App\Modules\Invoice\Application\Actions;

use App\Modules\Invoice\Domain\Events\InvoiceCreated;
use App\Modules\Invoice\Domain\Models\Invoice;
use App\Modules\Invoice\Domain\Models\InvoiceItem;
use Illuminate\Support\Str;

class CreateInvoiceAction
{
    public function execute(int $tenantId, int $userId, array $data): Invoice
    {
        $invoice = Invoice::create([
            'tenant_id' => $tenantId,
            'customer_id' => $data['customer_id'] ?? null,
            'number' => $this->generateNumber($tenantId),
            'status' => 'draft',
            'currency' => $data['currency'],
            'issue_date' => $data['issue_date'],
            'due_date' => $data['due_date'] ?? null,
            'notes' => $data['notes'] ?? null,
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);

        foreach ($data['items'] as $item) {
            $quantity = $item['quantity'];
            $unitPrice = $item['unit_price_minor'];
            $lineTotal = (int) ($quantity * $unitPrice);

            InvoiceItem::create([
                'tenant_id' => $tenantId,
                'invoice_id' => $invoice->id,
                'description' => $item['description'],
                'quantity' => $quantity,
                'unit_price_minor' => $unitPrice,
                'tax_rate' => $item['tax_rate'] ?? 0,
                'line_total_minor' => $lineTotal,
            ]);
        }

        $invoice->load('items');
        $invoice->recalculateTotals();

        InvoiceCreated::dispatch($tenantId, $invoice->id);

        return $invoice->refresh();
    }

    private function generateNumber(int $tenantId): string
    {
        $year = now()->year;
        $count = Invoice::where('tenant_id', $tenantId)
            ->whereYear('created_at', $year)
            ->count() + 1;

        return sprintf('INV-%d-%04d', $year, $count);
    }
}

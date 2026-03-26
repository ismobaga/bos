<?php

namespace App\Modules\Invoice\Http\Controllers;

use App\Modules\Invoice\Domain\Events\InvoicePaid;
use App\Modules\Invoice\Domain\Models\Invoice;
use App\Modules\Invoice\Domain\Models\InvoicePayment;
use App\Modules\Tenancy\Domain\ValueObjects\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoicePaymentController
{
    public function store(Request $request, Invoice $invoice): JsonResponse
    {
        /** @var TenantContext $ctx */
        $ctx = app(TenantContext::class);
        abort_unless($invoice->tenant_id === $ctx->id(), 403, 'Access denied.');

        $validated = $request->validate([
            'amount_minor' => 'required|integer|min:1',
            'currency' => 'required|string|size:3',
            'paid_at' => 'required|date',
            'method' => 'nullable|string|in:cash,bank_transfer,mobile_money,card',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $payment = InvoicePayment::create(array_merge($validated, [
            'tenant_id' => $ctx->id(),
            'invoice_id' => $invoice->id,
            'created_by' => $request->user()->id,
        ]));

        // Recalculate invoice totals
        $invoice->load('items', 'payments');
        $invoice->recalculateTotals();

        // Check if fully paid
        $invoice->refresh();
        if ($invoice->balance_due_minor <= 0) {
            $invoice->update(['status' => 'paid', 'updated_by' => $request->user()->id]);
            InvoicePaid::dispatch($invoice->tenant_id, $invoice->id);
        } elseif ($invoice->amount_paid_minor > 0) {
            $invoice->update(['status' => 'partial', 'updated_by' => $request->user()->id]);
        }

        return response()->json($payment, 201);
    }
}

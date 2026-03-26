<?php

namespace App\Modules\Invoice\Http\Controllers;

use App\Contracts\LicensingManagerInterface;
use App\Modules\Invoice\Application\Actions\CreateInvoiceAction;
use App\Modules\Invoice\Domain\Events\InvoiceSent;
use App\Modules\Invoice\Domain\Models\Invoice;
use App\Modules\Tenancy\Domain\ValueObjects\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController
{
    public function __construct(
        private readonly LicensingManagerInterface $licensing,
    ) {}

    public function index(Request $request): JsonResponse
    {
        /** @var TenantContext $ctx */
        $ctx = app(TenantContext::class);

        $invoices = Invoice::tenant($ctx->id())
            ->with(['customer'])
            ->when($request->input('status'), fn ($q, $s) => $q->where('status', $s))
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 15));

        return response()->json($invoices);
    }

    public function show(Request $request, Invoice $invoice): JsonResponse
    {
        $this->authorizeTenantAccess($invoice);

        return response()->json($invoice->load(['customer', 'items', 'payments']));
    }

    public function store(Request $request, CreateInvoiceAction $action): JsonResponse
    {
        /** @var TenantContext $ctx */
        $ctx = app(TenantContext::class);

        $validated = $request->validate([
            'customer_id' => 'nullable|integer',
            'currency' => 'required|string|size:3',
            'issue_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:issue_date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price_minor' => 'required|integer|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $invoice = $action->execute($ctx->id(), $request->user()->id, $validated);

        $this->licensing->consume($ctx->id(), 'invoice.monthly_limit');

        return response()->json($invoice->load('items'), 201);
    }

    public function update(Request $request, Invoice $invoice): JsonResponse
    {
        $this->authorizeTenantAccess($invoice);

        abort_if(in_array($invoice->status, ['paid', 'cancelled']), 422, 'Cannot update a paid or cancelled invoice.');

        $validated = $request->validate([
            'currency' => 'sometimes|string|size:3',
            'issue_date' => 'sometimes|date',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'status' => 'in:draft,sent,cancelled',
        ]);

        $invoice->update(array_merge($validated, ['updated_by' => $request->user()->id]));

        return response()->json($invoice);
    }

    public function destroy(Request $request, Invoice $invoice): JsonResponse
    {
        $this->authorizeTenantAccess($invoice);

        abort_if($invoice->status === 'paid', 422, 'Cannot delete a paid invoice.');

        $invoice->delete();

        return response()->json(null, 204);
    }

    public function send(Request $request, Invoice $invoice): JsonResponse
    {
        $this->authorizeTenantAccess($invoice);

        abort_if($invoice->status === 'paid', 422, 'Invoice is already paid.');

        $invoice->update(['status' => 'sent', 'updated_by' => $request->user()->id]);

        InvoiceSent::dispatch($invoice->tenant_id, $invoice->id);

        return response()->json($invoice);
    }

    private function authorizeTenantAccess(Invoice $invoice): void
    {
        /** @var TenantContext $ctx */
        $ctx = app(TenantContext::class);
        abort_unless($invoice->tenant_id === $ctx->id(), 403, 'Access denied.');
    }
}

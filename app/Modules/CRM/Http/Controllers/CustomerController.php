<?php

namespace App\Modules\CRM\Http\Controllers;

use App\Modules\CRM\Application\Actions\CreateCustomerAction;
use App\Modules\CRM\Application\Actions\UpdateCustomerAction;
use App\Modules\CRM\Domain\Models\Customer;
use App\Modules\Tenancy\Domain\ValueObjects\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController
{
    public function index(Request $request): JsonResponse
    {
        /** @var TenantContext $ctx */
        $ctx = app(TenantContext::class);

        $customers = Customer::tenant($ctx->id())
            ->when($request->input('search'), fn ($q, $search) =>
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%")
            )
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 15));

        return response()->json($customers);
    }

    public function show(Request $request, Customer $customer): JsonResponse
    {
        $this->authorizeTenantAccess($customer);

        return response()->json($customer->load(['contacts', 'tags']));
    }

    public function store(Request $request, CreateCustomerAction $action): JsonResponse
    {
        /** @var TenantContext $ctx */
        $ctx = app(TenantContext::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'in:individual,company',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'status' => 'in:active,inactive,archived',
        ]);

        $customer = $action->execute($ctx->id(), $request->user()->id, $validated);

        return response()->json($customer, 201);
    }

    public function update(Request $request, Customer $customer, UpdateCustomerAction $action): JsonResponse
    {
        $this->authorizeTenantAccess($customer);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'type' => 'in:individual,company',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'status' => 'in:active,inactive,archived',
        ]);

        $customer = $action->execute($customer, $request->user()->id, $validated);

        return response()->json($customer);
    }

    public function destroy(Request $request, Customer $customer): JsonResponse
    {
        $this->authorizeTenantAccess($customer);
        $customer->delete();

        return response()->json(null, 204);
    }

    private function authorizeTenantAccess(Customer $customer): void
    {
        /** @var TenantContext $ctx */
        $ctx = app(TenantContext::class);

        abort_unless($customer->tenant_id === $ctx->id(), 403, 'Access denied.');
    }
}

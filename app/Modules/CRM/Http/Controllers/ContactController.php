<?php

namespace App\Modules\CRM\Http\Controllers;

use App\Modules\CRM\Domain\Models\Contact;
use App\Modules\CRM\Domain\Models\Customer;
use App\Modules\Tenancy\Domain\ValueObjects\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController
{
    public function index(Request $request, Customer $customer): JsonResponse
    {
        $this->authorizeTenantAccess($customer);

        return response()->json($customer->contacts()->orderBy('is_primary', 'desc')->get());
    }

    public function store(Request $request, Customer $customer): JsonResponse
    {
        $this->authorizeTenantAccess($customer);

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'title' => 'nullable|string|max:100',
            'is_primary' => 'boolean',
        ]);

        /** @var TenantContext $ctx */
        $ctx = app(TenantContext::class);

        $contact = Contact::create(array_merge($validated, [
            'tenant_id' => $ctx->id(),
            'customer_id' => $customer->id,
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]));

        return response()->json($contact, 201);
    }

    public function update(Request $request, Contact $contact): JsonResponse
    {
        $this->authorizeTenantContactAccess($contact);

        $validated = $request->validate([
            'first_name' => 'sometimes|required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'title' => 'nullable|string|max:100',
            'is_primary' => 'boolean',
        ]);

        $contact->update(array_merge($validated, ['updated_by' => $request->user()->id]));

        return response()->json($contact);
    }

    public function destroy(Request $request, Contact $contact): JsonResponse
    {
        $this->authorizeTenantContactAccess($contact);
        $contact->delete();

        return response()->json(null, 204);
    }

    private function authorizeTenantAccess(Customer $customer): void
    {
        /** @var TenantContext $ctx */
        $ctx = app(TenantContext::class);
        abort_unless($customer->tenant_id === $ctx->id(), 403);
    }

    private function authorizeTenantContactAccess(Contact $contact): void
    {
        /** @var TenantContext $ctx */
        $ctx = app(TenantContext::class);
        abort_unless($contact->tenant_id === $ctx->id(), 403);
    }
}

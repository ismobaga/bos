<?php

namespace App\Modules\Tenancy\Http\Controllers\Admin;

use App\Modules\Tenancy\Domain\Events\TenantSuspended;
use App\Modules\Tenancy\Domain\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantAdminController
{
    public function index(Request $request): JsonResponse
    {
        $tenants = Tenant::query()
            ->when($request->input('status'), fn ($q, $s) => $q->where('status', $s))
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json($tenants);
    }

    public function show(Request $request, Tenant $tenant): JsonResponse
    {
        return response()->json($tenant->load(['owner', 'domains', 'branches', 'subscriptions']));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:tenants,slug',
            'status' => 'in:active,trial,suspended,cancelled',
        ]);

        $tenant = Tenant::create($validated);

        return response()->json($tenant, 201);
    }

    public function update(Request $request, Tenant $tenant): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'status' => 'in:active,trial,suspended,cancelled',
        ]);

        $tenant->update($validated);

        return response()->json($tenant);
    }

    public function suspend(Request $request, Tenant $tenant): JsonResponse
    {
        $tenant->update(['status' => 'suspended', 'suspended_at' => now()]);
        TenantSuspended::dispatch($tenant, $request->input('reason'));

        return response()->json($tenant);
    }

    public function activate(Request $request, Tenant $tenant): JsonResponse
    {
        $tenant->update(['status' => 'active', 'suspended_at' => null]);

        return response()->json($tenant);
    }
}

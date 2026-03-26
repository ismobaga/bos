<?php

namespace App\Modules\Tenancy\Http\Controllers;

use App\Modules\Tenancy\Application\Actions\CreateTenantAction;
use App\Modules\Tenancy\Domain\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantController
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $tenants = $user->tenants()->with('branding')->get();

        return response()->json($tenants);
    }

    public function store(Request $request, CreateTenantAction $action): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:100|unique:tenants,slug|regex:/^[a-z0-9\-]+$/',
            'timezone' => 'nullable|string|timezone',
            'locale' => 'nullable|string|size:2',
            'currency' => 'nullable|string|size:3',
            'country_code' => 'nullable|string|size:2',
        ]);

        $tenant = $action->execute($request->user()->id, $validated);

        return response()->json($tenant, 201);
    }
}

<?php

namespace App\Modules\Tenancy\Http\Controllers;

use App\Modules\Tenancy\Domain\Models\TenantBranding;
use App\Modules\Tenancy\Domain\ValueObjects\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrandingController
{
    public function show(Request $request): JsonResponse
    {
        /** @var TenantContext $ctx */
        $ctx = app(TenantContext::class);

        $branding = $ctx->tenant->branding ?? new TenantBranding();

        return response()->json($branding);
    }

    public function update(Request $request): JsonResponse
    {
        /** @var TenantContext $ctx */
        $ctx = app(TenantContext::class);

        $validated = $request->validate([
            'app_name' => 'nullable|string|max:100',
            'primary_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $branding = TenantBranding::updateOrCreate(
            ['tenant_id' => $ctx->id()],
            $validated
        );

        return response()->json($branding);
    }
}

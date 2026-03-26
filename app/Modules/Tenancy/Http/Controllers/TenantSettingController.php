<?php

namespace App\Modules\Tenancy\Http\Controllers;

use App\Modules\Tenancy\Domain\Models\Tenant;
use App\Modules\Tenancy\Domain\ValueObjects\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantSettingController
{
    public function index(Request $request): JsonResponse
    {
        /** @var TenantContext $ctx */
        $ctx = app(TenantContext::class);

        $settings = $ctx->tenant->settings()->get(['key', 'value']);

        return response()->json($settings);
    }

    public function update(Request $request): JsonResponse
    {
        /** @var TenantContext $ctx */
        $ctx = app(TenantContext::class);

        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string|max:100',
            'settings.*.value' => 'nullable|string',
        ]);

        foreach ($validated['settings'] as $setting) {
            $ctx->tenant->settings()->updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }

        return response()->json(['message' => 'Settings updated successfully.']);
    }
}

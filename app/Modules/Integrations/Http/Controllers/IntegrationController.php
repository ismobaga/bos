<?php

namespace App\Modules\Integrations\Http\Controllers;

use App\Modules\Integrations\Domain\Models\WebhookEndpoint;
use App\Modules\Tenancy\Domain\ValueObjects\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class IntegrationController
{
    public function index(Request $request): JsonResponse
    {
        /** @var TenantContext $ctx */
        $ctx = app(TenantContext::class);

        $webhooks = WebhookEndpoint::where('tenant_id', $ctx->id())->get();

        return response()->json(['webhooks' => $webhooks]);
    }

    public function storeWebhook(Request $request): JsonResponse
    {
        /** @var TenantContext $ctx */
        $ctx = app(TenantContext::class);

        $validated = $request->validate([
            'url' => 'required|url',
            'events' => 'required|array',
            'events.*' => 'string',
        ]);

        $webhook = WebhookEndpoint::create([
            'tenant_id' => $ctx->id(),
            'url' => $validated['url'],
            'secret' => Str::random(32),
            'events' => $validated['events'],
            'is_active' => true,
        ]);

        return response()->json($webhook, 201);
    }
}

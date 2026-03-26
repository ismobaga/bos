<?php

namespace App\Modules\Licensing\Http\Controllers;

use App\Modules\Licensing\Domain\Models\Subscription;
use App\Modules\Tenancy\Domain\ValueObjects\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController
{
    public function show(Request $request): JsonResponse
    {
        /** @var TenantContext $ctx */
        $ctx = app(TenantContext::class);

        $subscription = Subscription::where('tenant_id', $ctx->id())
            ->whereIn('status', ['active', 'trial', 'grace'])
            ->with('plan')
            ->latest()
            ->first();

        return response()->json($subscription);
    }
}

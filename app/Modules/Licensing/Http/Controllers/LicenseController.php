<?php

namespace App\Modules\Licensing\Http\Controllers;

use App\Modules\Licensing\Domain\Models\Subscription;
use App\Modules\Tenancy\Domain\ValueObjects\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LicenseController
{
    public function show(Request $request): JsonResponse
    {
        /** @var TenantContext $ctx */
        $ctx = app(TenantContext::class);

        $subscription = Subscription::where('tenant_id', $ctx->id())
            ->where('status', 'active')
            ->with('plan.modules')
            ->latest()
            ->first();

        if (! $subscription) {
            return response()->json(['subscription' => null, 'plan' => null, 'modules' => []]);
        }

        return response()->json([
            'subscription' => [
                'status' => $subscription->status,
                'starts_at' => $subscription->starts_at,
                'ends_at' => $subscription->ends_at,
            ],
            'plan' => [
                'key' => $subscription->plan->key,
                'name' => $subscription->plan->name,
            ],
            'modules' => $subscription->plan->modules->pluck('key'),
        ]);
    }
}

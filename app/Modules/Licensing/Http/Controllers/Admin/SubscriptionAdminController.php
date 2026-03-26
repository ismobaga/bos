<?php

namespace App\Modules\Licensing\Http\Controllers\Admin;

use App\Modules\Licensing\Domain\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionAdminController
{
    public function index(Request $request): JsonResponse
    {
        $subscriptions = Subscription::with(['tenant', 'plan'])
            ->when($request->input('status'), fn ($q, $s) => $q->where('status', $s))
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json($subscriptions);
    }
}

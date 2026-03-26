<?php

namespace App\Modules\Identity\Http\Controllers;

use App\Modules\Tenancy\Domain\ValueObjects\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeController
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar_url' => $user->avatar_url,
            'is_platform_admin' => $user->is_platform_admin,
        ];

        // Include tenant context if resolved
        if (app()->bound(TenantContext::class)) {
            $ctx = app(TenantContext::class);
            $data['current_tenant'] = [
                'id' => $ctx->tenant->id,
                'name' => $ctx->tenant->name,
                'slug' => $ctx->tenant->slug,
                'status' => $ctx->tenant->status,
            ];
        }

        return response()->json($data);
    }
}

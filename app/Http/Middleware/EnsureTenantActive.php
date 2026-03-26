<?php

namespace App\Http\Middleware;

use App\Modules\Tenancy\Domain\ValueObjects\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantActive
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var TenantContext $tenantContext */
        $tenantContext = app(TenantContext::class);

        if (! in_array($tenantContext->tenant->status, ['active', 'trial'])) {
            return response()->json([
                'message' => 'Tenant account is not active.',
                'status' => $tenantContext->tenant->status,
            ], 403);
        }

        return $next($request);
    }
}

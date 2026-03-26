<?php

namespace App\Http\Middleware;

use App\Modules\Tenancy\Domain\Models\Tenant;
use App\Modules\Tenancy\Domain\ValueObjects\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $this->resolve($request);

        if (! $tenant) {
            return response()->json(['message' => 'Tenant not found.'], 404);
        }

        app()->instance(TenantContext::class, new TenantContext($tenant));
        $request->attributes->set('tenant', $tenant);
        $request->attributes->set('tenant_id', $tenant->id);

        return $next($request);
    }

    private function resolve(Request $request): ?Tenant
    {
        // 1. Try X-Tenant-Slug header
        if ($slug = $request->header('X-Tenant-Slug')) {
            return Tenant::where('slug', $slug)->first();
        }

        // 2. Try subdomain
        $host = $request->getHost();
        $domain = Tenant::whereHas('domains', fn ($q) => $q->where('domain', $host))->first();
        if ($domain) {
            return $domain;
        }

        // 3. Try authenticated user's current tenant from session
        if (auth()->check()) {
            $tenantId = session('current_tenant_id');
            if ($tenantId) {
                return Tenant::find($tenantId);
            }
        }

        return null;
    }
}

<?php

namespace App\Modules\Tenancy\Application\Services;

use App\Contracts\TenantResolverInterface;
use App\Modules\Tenancy\Domain\Models\Tenant;
use App\Modules\Tenancy\Domain\ValueObjects\TenantContext;
use Illuminate\Http\Request;
use RuntimeException;

class TenantResolver implements TenantResolverInterface
{
    public function __construct(
        private readonly Request $request,
    ) {}

    public function resolve(): TenantContext
    {
        $tenant = $this->doResolve();

        if (! $tenant) {
            throw new RuntimeException('Unable to resolve tenant context.');
        }

        return new TenantContext($tenant);
    }

    private function doResolve(): ?Tenant
    {
        // From request attributes (set by middleware)
        if ($this->request->attributes->has('tenant')) {
            return $this->request->attributes->get('tenant');
        }

        // From X-Tenant-Slug header
        if ($slug = $this->request->header('X-Tenant-Slug')) {
            return Tenant::where('slug', $slug)->first();
        }

        // From session
        if (auth()->check()) {
            $tenantId = session('current_tenant_id');
            if ($tenantId) {
                return Tenant::find($tenantId);
            }
        }

        return null;
    }
}

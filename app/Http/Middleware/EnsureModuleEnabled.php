<?php

namespace App\Http\Middleware;

use App\Contracts\LicensingManagerInterface;
use App\Modules\Tenancy\Domain\ValueObjects\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleEnabled
{
    public function __construct(
        private readonly LicensingManagerInterface $licensing,
    ) {}

    public function handle(Request $request, Closure $next, string $moduleKey): Response
    {
        /** @var TenantContext $tenantContext */
        $tenantContext = app(TenantContext::class);

        if (! $this->licensing->moduleEnabled($tenantContext->id(), $moduleKey)) {
            return response()->json([
                'message' => "Module '{$moduleKey}' is not enabled for your plan.",
                'code' => 'module_not_licensed',
            ], 403);
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use App\Contracts\LicensingManagerInterface;
use App\Modules\Tenancy\Domain\ValueObjects\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUsage
{
    public function __construct(
        private readonly LicensingManagerInterface $licensing,
    ) {}

    public function handle(Request $request, Closure $next, string $key, int $amount = 1): Response
    {
        /** @var TenantContext $tenantContext */
        $tenantContext = app(TenantContext::class);

        if (! $this->licensing->canConsume($tenantContext->id(), $key, $amount)) {
            return response()->json([
                'message' => "Usage limit reached for '{$key}'.",
                'code' => 'usage_limit_exceeded',
                'limit_key' => $key,
            ], 422);
        }

        return $next($request);
    }
}

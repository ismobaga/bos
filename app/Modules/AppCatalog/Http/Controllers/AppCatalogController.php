<?php

namespace App\Modules\AppCatalog\Http\Controllers;

use App\Modules\AppCatalog\Application\Services\AppCatalogService;
use App\Modules\Tenancy\Domain\ValueObjects\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppCatalogController
{
    public function __construct(
        private readonly AppCatalogService $catalog,
    ) {}

    public function index(Request $request): JsonResponse
    {
        /** @var TenantContext $tenantContext */
        $tenantContext = app(TenantContext::class);
        $tenant = $tenantContext->tenant;
        $user = $request->user();

        $apps = $this->catalog->getAppsForTenant($tenant);

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'tenant' => [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'status' => $tenant->status,
                'slug' => $tenant->slug,
            ],
            'apps' => $apps,
        ]);
    }
}

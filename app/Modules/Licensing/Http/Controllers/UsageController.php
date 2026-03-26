<?php

namespace App\Modules\Licensing\Http\Controllers;

use App\Contracts\LicensingManagerInterface;
use App\Modules\Tenancy\Domain\ValueObjects\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsageController
{
    public function __construct(
        private readonly LicensingManagerInterface $licensing,
    ) {}

    public function show(Request $request): JsonResponse
    {
        /** @var TenantContext $ctx */
        $ctx = app(TenantContext::class);
        $tenantId = $ctx->id();

        $usageKeys = [
            'users' => 'users.limit',
            'invoices_monthly' => 'invoice.monthly_limit',
            'storage_gb' => 'storage.gb',
            'lms_learners' => 'lms.learners.limit',
        ];

        $usage = [];

        foreach ($usageKeys as $label => $key) {
            $limit = $this->licensing->getLimit($tenantId, $key);
            if ($limit !== null) {
                $used = $this->getUsed($tenantId, $key);
                $usage[$label] = [
                    'used' => $used,
                    'limit' => $limit,
                ];
            }
        }

        return response()->json(['usage' => $usage]);
    }

    private function getUsed(int $tenantId, string $key): int
    {
        return \App\Modules\Licensing\Domain\Models\UsageCounter::where('tenant_id', $tenantId)
            ->where('key', $key)
            ->where('period_start', '<=', now())
            ->where(function ($q) {
                $q->whereNull('period_end')->orWhere('period_end', '>=', now());
            })
            ->sum('used_value');
    }
}

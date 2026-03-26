<?php

namespace App\Modules\Audit\Http\Controllers;

use App\Modules\Audit\Domain\Models\AuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuditLogController
{
    public function index(Request $request): JsonResponse
    {
        $logs = AuditLog::query()
            ->when($request->input('tenant_id'), fn ($q, $t) => $q->where('tenant_id', $t))
            ->when($request->input('module'), fn ($q, $m) => $q->where('module', $m))
            ->when($request->input('action'), fn ($q, $a) => $q->where('action', $a))
            ->when($request->input('actor_id'), fn ($q, $a) => $q->where('actor_id', $a))
            ->orderByDesc('created_at')
            ->paginate(50);

        return response()->json($logs);
    }
}

<?php

namespace App\Modules\Audit\Application\Services;

use App\Contracts\AuditLoggerInterface;
use App\Modules\Audit\Application\DTOs\AuditEntryData;
use App\Modules\Audit\Domain\Models\AuditLog;

class AuditLogger implements AuditLoggerInterface
{
    public function log(AuditEntryData $data): void
    {
        AuditLog::create([
            'tenant_id' => $data->tenantId,
            'module' => $data->module,
            'action' => $data->action,
            'actor_type' => $data->actorType,
            'actor_id' => $data->actorId,
            'target_type' => $data->targetType,
            'target_id' => $data->targetId,
            'old_values' => $data->oldValues,
            'new_values' => $data->newValues,
            'context' => $data->context,
            'ip_address' => $data->ipAddress,
            'user_agent' => $data->userAgent,
            'correlation_id' => $data->correlationId,
        ]);
    }
}

<?php

namespace App\Modules\Audit\Application\DTOs;

final class AuditEntryData
{
    public function __construct(
        public readonly string $action,
        public readonly ?int $tenantId = null,
        public readonly ?string $module = null,
        public readonly ?string $actorType = null,
        public readonly ?int $actorId = null,
        public readonly ?string $targetType = null,
        public readonly ?int $targetId = null,
        public readonly ?array $oldValues = null,
        public readonly ?array $newValues = null,
        public readonly ?array $context = null,
        public readonly ?string $ipAddress = null,
        public readonly ?string $userAgent = null,
        public readonly ?string $correlationId = null,
    ) {}

    public static function make(string $action, array $attributes = []): self
    {
        return new self(
            action: $action,
            tenantId: $attributes['tenant_id'] ?? null,
            module: $attributes['module'] ?? null,
            actorType: $attributes['actor_type'] ?? null,
            actorId: $attributes['actor_id'] ?? null,
            targetType: $attributes['target_type'] ?? null,
            targetId: $attributes['target_id'] ?? null,
            oldValues: $attributes['old_values'] ?? null,
            newValues: $attributes['new_values'] ?? null,
            context: $attributes['context'] ?? null,
            ipAddress: $attributes['ip_address'] ?? null,
            userAgent: $attributes['user_agent'] ?? null,
            correlationId: $attributes['correlation_id'] ?? null,
        );
    }
}

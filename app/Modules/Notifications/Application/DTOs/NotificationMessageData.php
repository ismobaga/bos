<?php

namespace App\Modules\Notifications\Application\DTOs;

final class NotificationMessageData
{
    public function __construct(
        public readonly int $tenantId,
        public readonly string $channel,
        public readonly string $to,
        public readonly string $body,
        public readonly ?string $subject = null,
        public readonly ?string $templateKey = null,
        public readonly ?array $variables = null,
        public readonly ?array $metadata = null,
    ) {}

    public static function make(array $attributes): self
    {
        return new self(
            tenantId: $attributes['tenant_id'],
            channel: $attributes['channel'],
            to: $attributes['to'],
            body: $attributes['body'],
            subject: $attributes['subject'] ?? null,
            templateKey: $attributes['template_key'] ?? null,
            variables: $attributes['variables'] ?? null,
            metadata: $attributes['metadata'] ?? null,
        );
    }
}

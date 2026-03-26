<?php

namespace App\Contracts;

use App\Modules\Audit\Application\DTOs\AuditEntryData;

interface AuditLoggerInterface
{
    public function log(AuditEntryData $data): void;
}

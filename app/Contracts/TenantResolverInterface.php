<?php

namespace App\Contracts;

use App\Modules\Tenancy\Domain\ValueObjects\TenantContext;

interface TenantResolverInterface
{
    public function resolve(): TenantContext;
}

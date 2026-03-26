<?php

namespace App\Modules\Licensing\Domain\Exceptions;

use RuntimeException;

class ModuleNotLicensedException extends RuntimeException
{
    public function __construct(string $moduleKey)
    {
        parent::__construct("Module '{$moduleKey}' is not enabled for this tenant.");
    }
}

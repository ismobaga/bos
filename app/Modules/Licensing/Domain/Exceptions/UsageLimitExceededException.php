<?php

namespace App\Modules\Licensing\Domain\Exceptions;

use RuntimeException;

class UsageLimitExceededException extends RuntimeException
{
    public function __construct(string $message = 'Usage limit exceeded.')
    {
        parent::__construct($message);
    }
}

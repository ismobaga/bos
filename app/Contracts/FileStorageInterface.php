<?php

namespace App\Contracts;

use App\Modules\Files\Application\DTOs\FileUploadData;
use App\Modules\Files\Domain\Models\FileAsset;

interface FileStorageInterface
{
    public function put(FileUploadData $data): FileAsset;

    public function signedUrl(FileAsset $file, int $ttlSeconds = 300): string;
}

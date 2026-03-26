<?php

namespace App\Modules\Files\Application\DTOs;

use Illuminate\Http\UploadedFile;

final class FileUploadData
{
    public function __construct(
        public readonly int $tenantId,
        public readonly UploadedFile $file,
        public readonly int $uploadedBy,
        public readonly string $visibility = 'private',
        public readonly ?string $folder = null,
        public readonly ?string $purpose = null,
    ) {}
}

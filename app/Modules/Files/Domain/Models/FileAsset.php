<?php

namespace App\Modules\Files\Domain\Models;

use App\Models\User;
use App\Modules\Tenancy\Domain\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FileAsset extends Model
{
    protected $table = 'file_assets';

    protected $fillable = [
        'tenant_id',
        'disk',
        'bucket',
        'object_key',
        'original_name',
        'mime_type',
        'size_bytes',
        'checksum',
        'visibility',
        'uploaded_by',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(FileAttachment::class, 'file_asset_id');
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type ?? '', 'image/');
    }

    public function isPrivate(): bool
    {
        return $this->visibility === 'private';
    }
}

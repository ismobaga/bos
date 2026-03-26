<?php

namespace App\Modules\Files\Domain\Models;

use App\Modules\Tenancy\Domain\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class FileAttachment extends Model
{
    protected $table = 'file_attachments';

    protected $fillable = [
        'tenant_id',
        'file_asset_id',
        'attachable_type',
        'attachable_id',
        'purpose',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function fileAsset(): BelongsTo
    {
        return $this->belongsTo(FileAsset::class);
    }

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }
}

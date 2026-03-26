<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('file_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('disk')->default('minio');
            $table->string('bucket');
            $table->string('object_key');
            $table->string('original_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->string('checksum')->nullable();
            $table->string('visibility')->default('private'); // private, public
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('file_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('file_asset_id')->constrained()->cascadeOnDelete();
            $table->morphs('attachable'); // polymorphic relation
            $table->string('purpose')->nullable(); // invoice_pdf, profile_avatar, etc.
            $table->timestamps();
        });

        Schema::create('file_access_grants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('granted_to')->constrained('users')->cascadeOnDelete();
            $table->string('permission')->default('view'); // view, download
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('file_folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('file_folders')->nullOnDelete();
            $table->string('name');
            $table->string('path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('file_folders');
        Schema::dropIfExists('file_access_grants');
        Schema::dropIfExists('file_attachments');
        Schema::dropIfExists('file_assets');
    }
};

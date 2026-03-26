<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hosting_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('status')->default('active'); // active, deploying, failed, suspended
            $table->string('provider')->nullable(); // dokploy, coolify, manual
            $table->string('provider_reference')->nullable();
            $table->jsonb('config')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
        });

        Schema::create('hosting_deployments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('hosting_projects')->cascadeOnDelete();
            $table->string('commit_ref')->nullable();
            $table->string('status')->default('queued'); // queued, running, success, failed
            $table->text('log')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->foreignId('triggered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('hosting_domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('hosting_projects')->cascadeOnDelete();
            $table->string('domain');
            $table->boolean('ssl_enabled')->default(false);
            $table->string('status')->default('active');
            $table->timestamps();

            $table->unique(['project_id', 'domain']);
        });

        Schema::create('hosting_usage_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('hosting_projects')->cascadeOnDelete();
            $table->string('metric'); // bandwidth_gb, disk_gb, requests
            $table->decimal('value', 15, 4);
            $table->timestamp('recorded_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hosting_usage_records');
        Schema::dropIfExists('hosting_domains');
        Schema::dropIfExists('hosting_deployments');
        Schema::dropIfExists('hosting_projects');
    }
};

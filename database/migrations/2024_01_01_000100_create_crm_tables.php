<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type')->default('individual'); // individual, company
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('status')->default('active'); // active, inactive, archived
            $table->jsonb('billing_address_json')->nullable();
            $table->jsonb('metadata')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('tenant_id');
        });

        Schema::create('crm_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('crm_customers')->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('title')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('tenant_id');
        });

        Schema::create('crm_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('crm_customers')->cascadeOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('crm_contacts')->nullOnDelete();
            $table->text('content');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('tenant_id');
        });

        Schema::create('crm_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('color', 7)->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'name']);
        });

        Schema::create('crm_customer_tag', function (Blueprint $table) {
            $table->foreignId('customer_id')->constrained('crm_customers')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('crm_tags')->cascadeOnDelete();
            $table->primary(['customer_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_customer_tag');
        Schema::dropIfExists('crm_tags');
        Schema::dropIfExists('crm_notes');
        Schema::dropIfExists('crm_contacts');
        Schema::dropIfExists('crm_customers');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_modules', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->string('billing_period')->default('monthly'); // monthly, yearly, one_time
            $table->unsignedBigInteger('price_minor')->default(0); // price in cents
            $table->string('currency', 3)->default('USD');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('plan_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('module_id')->references('id')->on('product_modules')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['plan_id', 'module_id']);
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained();
            $table->string('status')->default('active'); // active, trial, grace, cancelled, expired
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('grace_ends_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });

        Schema::create('tenant_entitlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('key');
            $table->string('value_type'); // string, integer, boolean, json
            $table->string('value_string')->nullable();
            $table->bigInteger('value_integer')->nullable();
            $table->boolean('value_boolean')->nullable();
            $table->jsonb('value_json')->nullable();
            $table->string('source_type')->nullable(); // plan, override, manual
            $table->unsignedBigInteger('source_id')->nullable();
            $table->timestamp('effective_from')->nullable();
            $table->timestamp('effective_to')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'key']);
        });

        Schema::create('usage_counters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('key');
            $table->timestamp('period_start')->nullable();
            $table->timestamp('period_end')->nullable();
            $table->unsignedBigInteger('used_value')->default(0);
            $table->string('reset_strategy')->default('monthly'); // monthly, yearly, never
            $table->timestamps();

            $table->index(['tenant_id', 'key']);
        });

        Schema::create('feature_flags', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->boolean('globally_enabled')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feature_flags');
        Schema::dropIfExists('usage_counters');
        Schema::dropIfExists('tenant_entitlements');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('plan_modules');
        Schema::dropIfExists('plans');
        Schema::dropIfExists('product_modules');
    }
};

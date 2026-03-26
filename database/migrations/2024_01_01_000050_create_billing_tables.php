<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('legal_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->jsonb('billing_address_json')->nullable();
            $table->string('tax_id')->nullable();
            $table->timestamps();
        });

        Schema::create('billing_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_id')->constrained()->cascadeOnDelete();
            $table->string('provider')->nullable(); // stripe, wave, manual
            $table->string('provider_reference')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('billing_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('number')->unique();
            $table->string('status')->default('draft'); // draft, sent, paid, overdue, void
            $table->string('currency', 3)->default('USD');
            $table->unsignedBigInteger('subtotal_minor')->default(0);
            $table->unsignedBigInteger('tax_minor')->default(0);
            $table->unsignedBigInteger('total_minor')->default(0);
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->unsignedBigInteger('pdf_file_id')->nullable();
            $table->timestamps();
        });

        Schema::create('billing_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('billing_invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->string('provider')->nullable();
            $table->string('provider_reference')->nullable();
            $table->unsignedBigInteger('amount_minor');
            $table->string('currency', 3)->default('USD');
            $table->string('status')->default('pending'); // pending, completed, failed, refunded
            $table->timestamp('paid_at')->nullable();
            $table->jsonb('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // card, bank_transfer, mobile_money
            $table->string('provider')->nullable();
            $table->string('provider_reference')->nullable();
            $table->jsonb('details')->nullable(); // last4, brand, etc. (never full card data)
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::create('pricing_catalog', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->string('currency', 3);
            $table->unsignedBigInteger('price_minor');
            $table->string('billing_period');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['plan_id', 'currency', 'billing_period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_catalog');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('billing_payments');
        Schema::dropIfExists('billing_invoices');
        Schema::dropIfExists('billing_subscriptions');
        Schema::dropIfExists('billing_customers');
    }
};

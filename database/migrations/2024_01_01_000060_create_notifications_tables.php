<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->string('channel'); // email, whatsapp, sms, in_app, webhook
            $table->string('subject')->nullable();
            $table->text('body');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('notification_channels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // email, whatsapp, sms, webhook
            $table->string('name');
            $table->jsonb('config'); // provider-specific config
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('notification_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('channel');
            $table->string('to');
            $table->string('subject')->nullable();
            $table->text('body');
            $table->string('status')->default('pending'); // pending, sent, failed, delivered
            $table->jsonb('metadata')->nullable();
            $table->string('reference')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();
        });

        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event_key');
            $table->string('channel');
            $table->boolean('enabled')->default(true);
            $table->timestamps();

            $table->unique(['tenant_id', 'user_id', 'event_key', 'channel']);
        });

        Schema::create('notification_event_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('event_key');
            $table->foreignId('notification_message_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status');
            $table->jsonb('context')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_event_logs');
        Schema::dropIfExists('notification_preferences');
        Schema::dropIfExists('notification_messages');
        Schema::dropIfExists('notification_channels');
        Schema::dropIfExists('notification_templates');
    }
};

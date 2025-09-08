<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->text('message');
            $table->string('type')->nullable();
            $table->string('category')->nullable();
            $table->string('subcategory')->nullable();
            $table->integer('priority')->default(5);
            $table->enum('status', ['pending', 'scheduled', 'sent', 'failed', 'cancelled', 'expired'])->default('pending');
            $table->timestamp('read_at')->nullable();
            $table->string('action_url')->nullable();
            $table->string('action_text')->nullable();
            $table->string('related_model')->nullable();
            $table->unsignedBigInteger('related_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->enum('delivery_method', ['email', 'sms', 'push', 'in_app', 'webhook'])->default('in_app');
            $table->enum('delivery_status', ['pending', 'sent', 'delivered', 'failed', 'bounced', 'spam'])->default('pending');
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->integer('max_retries')->default(3);
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users');

            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['type', 'status']);
            $table->index(['category', 'status']);
            $table->index(['priority', 'status']);
            $table->index(['delivery_method', 'status']);
            $table->index(['delivery_status', 'status']);
            $table->index(['scheduled_at', 'status']);
            $table->index(['sent_at', 'status']);
            $table->index(['expires_at', 'status']);
            $table->index(['related_model', 'related_id']);
            $table->index(['is_active', 'status']);
            $table->index(['created_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

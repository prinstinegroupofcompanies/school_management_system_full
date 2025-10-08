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
        Schema::create('report_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('report_templates')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('subscription_name'); // Subscription name
            $table->text('description')->nullable(); // Subscription description
            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'quarterly', 'yearly'])->default('monthly');
            $table->json('report_params'); // Report parameters
            $table->json('filters'); // Applied filters
            $table->string('email'); // Email for notifications
            $table->json('export_settings'); // Export format preferences
            $table->boolean('is_active')->default(true); // Subscription status
            $table->datetime('last_sent')->nullable(); // Last report sent
            $table->datetime('next_send')->nullable(); // Next report send
            $table->integer('sent_count')->default(0); // Total reports sent
            $table->json('metadata')->nullable(); // Additional subscription data
            $table->timestamps();
            
            $table->index(['template_id', 'user_id']);
            $table->index(['user_id', 'is_active']);
            $table->index(['frequency', 'is_active']);
            $table->index(['next_send']);
            $table->index(['last_sent']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_subscriptions');
    }
};
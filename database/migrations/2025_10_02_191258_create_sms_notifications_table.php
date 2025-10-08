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
        Schema::create('sms_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number');
            $table->text('message');
            $table->enum('type', ['attendance', 'grades', 'urgent', 'general', 'payment', 'exam', 'event'])->default('general');
            $table->enum('status', ['pending', 'sent', 'delivered', 'failed', 'expired'])->default('pending');
            $table->string('provider')->nullable(); // SMS provider used
            $table->string('provider_message_id')->nullable(); // Provider's message ID
            $table->text('provider_response')->nullable(); // Provider's response
            $table->decimal('cost', 8, 4)->nullable(); // Cost of sending SMS
            $table->timestamp('scheduled_at')->nullable(); // When to send
            $table->timestamp('sent_at')->nullable(); // When actually sent
            $table->timestamp('delivered_at')->nullable(); // When delivered
            $table->timestamp('expires_at')->nullable(); // When message expires
            $table->json('metadata')->nullable(); // Additional data
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('guardians')->nullOnDelete();
            $table->timestamps();
            
            $table->index(['phone_number', 'status']);
            $table->index(['type', 'status']);
            $table->index(['scheduled_at', 'status']);
            $table->index(['user_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_notifications');
    }
};

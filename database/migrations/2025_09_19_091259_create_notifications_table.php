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
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->string('type'); // grade_published, payment_approved, exam_available, etc.
                $table->morphs('notifiable'); // User who receives the notification
                $table->json('data'); // Notification data
                $table->timestamp('read_at')->nullable();
                $table->timestamps();

                $table->index(['notifiable_type', 'notifiable_id']);
            });
        }

        if (!Schema::hasTable('real_time_activities')) {
            Schema::create('real_time_activities', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('activity_type'); // login, grade_view, payment_made, exam_taken
                $table->string('description');
                $table->json('metadata')->nullable(); // Additional activity data
                $table->string('ip_address')->nullable();
                $table->string('user_agent')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'activity_type']);
                $table->index('created_at');
            });
        }

        if (!Schema::hasTable('system_alerts')) {
            Schema::create('system_alerts', function (Blueprint $table) {
                $table->id();
                $table->string('alert_type'); // maintenance, exam_reminder, fee_due, etc.
                $table->string('title');
                $table->text('message');
                $table->json('target_users')->nullable(); // User types or specific IDs
                $table->string('priority')->default('normal'); // low, normal, high, urgent
                $table->boolean('is_active')->default(true);
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();

                $table->index(['alert_type', 'is_active']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('real_time_activities');
        Schema::dropIfExists('system_alerts');
    }
};
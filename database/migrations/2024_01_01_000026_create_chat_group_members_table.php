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
        Schema::create('chat_group_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('role', ['admin', 'moderator', 'member'])->default('member');
            $table->enum('status', ['active', 'inactive', 'banned', 'muted', 'left', 'pending'])->default('active');
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('left_at')->nullable();
            $table->timestamp('banned_at')->nullable();
            $table->unsignedBigInteger('banned_by')->nullable();
            $table->string('ban_reason')->nullable();
            $table->timestamp('muted_at')->nullable();
            $table->unsignedBigInteger('muted_by')->nullable();
            $table->string('mute_reason')->nullable();
            $table->integer('mute_duration')->nullable(); // in minutes
            $table->unsignedBigInteger('invited_by')->nullable();
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('last_activity')->nullable();
            $table->integer('message_count')->default(0);
            $table->integer('file_share_count')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('group_id')->references('id')->on('chat_groups');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('banned_by')->references('id')->on('users');
            $table->foreign('muted_by')->references('id')->on('users');
            $table->foreign('invited_by')->references('id')->on('users');

            // Unique constraints
            $table->unique(['group_id', 'user_id']);

            // Indexes
            $table->index(['group_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['role', 'status']);
            $table->index(['status', 'joined_at']);
            $table->index(['banned_by', 'status']);
            $table->index(['muted_by', 'status']);
            $table->index(['invited_by', 'status']);
            $table->index(['last_activity', 'status']);
            $table->index(['message_count', 'status']);
            $table->index(['file_share_count', 'status']);
            $table->index(['created_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_group_members');
    }
};

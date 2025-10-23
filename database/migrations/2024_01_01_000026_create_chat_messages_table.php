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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->text('message');
            $table->string('message_type')->default('text'); // text, image, file, audio, video
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_type')->nullable();
            $table->integer('file_size')->nullable(); // in bytes
            $table->string('thumbnail')->nullable();
            $table->text('metadata')->nullable(); // JSON array for additional data
            $table->boolean('is_edited')->default(false);
            $table->timestamp('edited_at')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamp('deleted_at')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->boolean('is_pinned')->default(false);
            $table->timestamp('pinned_at')->nullable();
            $table->unsignedBigInteger('receiver_id')->nullable();
            $table->text('reply_to_message')->nullable(); // For reply functionality
            $table->unsignedBigInteger('group_id')->nullable();
            $table->enum('status', ['sent', 'delivered', 'read', 'failed'])->default('sent');
            $table->timestamps();
            
            $table->index(['sender_id', 'receiver_id']);
            $table->index(['group_id', 'created_at']);
            $table->index(['message_type', 'is_deleted']);
            $table->index(['is_read', 'created_at']);
            $table->index(['is_pinned', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
}; 
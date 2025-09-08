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
        Schema::create('chat_groups', function (Blueprint $table) {
            $table->id();
            $table->string('group_name');
            $table->text('description')->nullable();
            $table->string('group_avatar')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->enum('group_type', ['class', 'subject', 'general', 'project', 'study'])->default('general');
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->integer('max_members')->default(100);
            $table->integer('current_members')->default(0);
            $table->boolean('is_public')->default(true);
            $table->boolean('require_invitation')->default(false);
            $table->boolean('allow_member_invite')->default(true);
            $table->boolean('allow_file_sharing')->default(true);
            $table->integer('max_file_size')->default(10485760); // 10MB
            $table->json('allowed_file_types')->nullable();
            $table->text('group_rules')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended', 'archived'])->default('active');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('admin_id')->references('id')->on('users');
            $table->foreign('class_id')->references('id')->on('classrooms');
            $table->foreign('subject_id')->references('id')->on('subjects');

            // Indexes
            $table->index(['group_name', 'status']);
            $table->index(['created_by', 'status']);
            $table->index(['admin_id', 'status']);
            $table->index(['group_type', 'status']);
            $table->index(['class_id', 'status']);
            $table->index(['subject_id', 'status']);
            $table->index(['is_public', 'status']);
            $table->index(['require_invitation', 'status']);
            $table->index(['max_members', 'current_members']);
            $table->index(['status', 'is_active']);
            $table->index(['created_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_groups');
    }
};

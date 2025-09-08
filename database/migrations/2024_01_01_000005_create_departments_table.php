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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('head_teacher_id')->nullable();
            $table->string('location')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->integer('teacher_count')->default(0);
            $table->integer('staff_count')->default(0);
            $table->integer('student_count')->default(0);
            $table->text('objectives')->nullable();
            $table->text('achievements')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->integer('display_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('head_teacher_id')->references('id')->on('users');

            // Indexes
            $table->index(['code', 'status']);
            $table->index(['name', 'status']);
            $table->index(['status', 'display_order']);
            $table->index(['head_teacher_id', 'status']);
            $table->index(['created_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};

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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Mathematics", "English Literature"
            $table->string('code')->unique(); // e.g., "MATH", "ENG"
            $table->text('description')->nullable();
            $table->foreignId('class_id')->constrained('class_rooms')->onDelete('cascade');
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->onDelete('set null');
            $table->enum('type', ['core', 'elective', 'optional'])->default('core');
            $table->integer('credits')->default(1);
            $table->integer('hours_per_week')->default(5);
            $table->string('book_name')->nullable();
            $table->string('book_author')->nullable();
            $table->string('book_publisher')->nullable();
            $table->string('book_isbn')->nullable();
            $table->decimal('passing_marks', 5, 2)->default(40.00);
            $table->decimal('full_marks', 5, 2)->default(100.00);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['name', 'class_id']);
            $table->index(['teacher_id', 'status']);
            $table->index(['type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
}; 
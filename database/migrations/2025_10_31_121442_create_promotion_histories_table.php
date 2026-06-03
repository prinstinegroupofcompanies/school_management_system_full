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
        Schema::create('promotion_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('from_class_id')->constrained('class_rooms')->onDelete('cascade');
            $table->foreignId('to_class_id')->nullable()->constrained('class_rooms')->onDelete('set null');
            $table->string('term');
            $table->integer('year');
            $table->enum('status', ['promoted', 'retained']);
            $table->decimal('average_score', 5, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('processed_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->index(['student_id', 'year']);
            $table->index(['from_class_id', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_histories');
    }
};

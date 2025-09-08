<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_schedule_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->decimal('score', 8, 2)->default(0);
            $table->string('status', 20)->default('in_progress'); // in_progress|submitted|graded
            $table->timestamps();
            $table->unique(['exam_schedule_id','student_id']);
        });

        Schema::create('exam_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_attempt_id')->constrained()->onDelete('cascade');
            $table->foreignId('exam_question_id')->constrained()->onDelete('cascade');
            $table->text('answer_text')->nullable();
            $table->decimal('marks_awarded', 8, 2)->default(0);
            $table->timestamps();
            $table->unique(['exam_attempt_id','exam_question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_answers');
        Schema::dropIfExists('exam_attempts');
    }
};



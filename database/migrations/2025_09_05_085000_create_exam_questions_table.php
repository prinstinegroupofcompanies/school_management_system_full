<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_schedule_id')->constrained()->onDelete('cascade');
            $table->text('question_text');
            $table->string('type', 20)->default('mcq'); // mcq|short
            $table->json('options')->nullable(); // for mcq: ["A) ...","B) ...",...]
            $table->string('correct_answer')->nullable();
            $table->decimal('marks', 8, 2)->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_questions');
    }
};



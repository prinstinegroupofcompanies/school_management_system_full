<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_performance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained()->onDelete('cascade');
            $table->foreignId('evaluator_id')->constrained('users')->onDelete('cascade');
            $table->string('evaluation_period'); // e.g., "2024-Q1", "2024-Annual"
            $table->date('evaluation_date');
            $table->date('period_start');
            $table->date('period_end');
            
            // Performance Metrics (1-5 scale)
            $table->integer('punctuality')->default(5);
            $table->integer('work_quality')->default(5);
            $table->integer('teamwork')->default(5);
            $table->integer('communication')->default(5);
            $table->integer('initiative')->default(5);
            $table->integer('problem_solving')->default(5);
            $table->integer('leadership')->default(5);
            $table->integer('adaptability')->default(5);
            
            // Overall Performance
            $table->decimal('overall_score', 3, 2); // Calculated average
            $table->enum('performance_rating', ['excellent', 'good', 'satisfactory', 'needs_improvement', 'unsatisfactory']);
            
            // Goals and Objectives
            $table->json('goals_achieved')->nullable();
            $table->json('goals_pending')->nullable();
            $table->text('strengths')->nullable();
            $table->text('areas_for_improvement')->nullable();
            
            // Development Plan
            $table->text('development_plan')->nullable();
            $table->json('training_recommendations')->nullable();
            $table->text('next_period_goals')->nullable();
            
            // Comments
            $table->text('evaluator_comments')->nullable();
            $table->text('staff_comments')->nullable();
            $table->text('hr_comments')->nullable();
            
            // Status
            $table->enum('status', ['draft', 'submitted', 'reviewed', 'approved', 'disputed'])->default('draft');
            $table->boolean('is_confidential')->default(true);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['staff_id', 'evaluation_period']);
            $table->index(['evaluator_id', 'evaluation_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_performance');
    }
};

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
        Schema::table('exam_papers', function (Blueprint $table) {
            $table->integer('passing_marks')->default(0)->after('total_marks');
            $table->string('exam_type')->default('online')->after('duration_minutes');
            $table->timestamp('start_time')->nullable()->after('exam_date');
            $table->timestamp('end_time')->nullable()->after('start_time');
            $table->boolean('is_published')->default(false)->after('status');
            $table->boolean('randomize_questions')->default(false)->after('is_published');
            $table->boolean('show_results_immediately')->default(false)->after('randomize_questions');
            $table->boolean('allow_review')->default(true)->after('show_results_immediately');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_papers', function (Blueprint $table) {
            $table->dropColumn([
                'passing_marks',
                'exam_type',
                'start_time',
                'end_time',
                'is_published',
                'randomize_questions',
                'show_results_immediately',
                'allow_review'
            ]);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lesson_plans', function (Blueprint $table) {
            $table->string('term')->nullable()->after('class_id');
            $table->string('file_path')->nullable()->after('homework');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null')->after('status');
            $table->text('review_remarks')->nullable()->after('reviewed_by');
            $table->timestamp('reviewed_at')->nullable()->after('review_remarks');
            
            // Change status enum to match requirements
            DB::statement("ALTER TABLE lesson_plans MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'draft', 'submitted', 'first_level_approved', 'second_level_approved') DEFAULT 'pending'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lesson_plans', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn(['term', 'file_path', 'reviewed_by', 'review_remarks', 'reviewed_at']);
        });
    }
};

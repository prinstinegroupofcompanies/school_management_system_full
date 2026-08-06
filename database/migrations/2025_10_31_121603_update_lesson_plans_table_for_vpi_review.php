<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('lesson_plans')) {
            return;
        }

        Schema::table('lesson_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('lesson_plans', 'term')) {
                $table->string('term')->nullable()->after('class_id');
            }
            if (!Schema::hasColumn('lesson_plans', 'file_path')) {
                $table->string('file_path')->nullable()->after('homework');
            }
            if (!Schema::hasColumn('lesson_plans', 'reviewed_by')) {
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null')->after('status');
            }
            if (!Schema::hasColumn('lesson_plans', 'review_remarks')) {
                $table->text('review_remarks')->nullable()->after('reviewed_by');
            }
            if (!Schema::hasColumn('lesson_plans', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('review_remarks');
            }
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE lesson_plans MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'draft', 'submitted', 'first_level_approved', 'second_level_approved') DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('lesson_plans')) {
            return;
        }

        Schema::table('lesson_plans', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn(['term', 'file_path', 'reviewed_by', 'review_remarks', 'reviewed_at']);
        });
    }
};

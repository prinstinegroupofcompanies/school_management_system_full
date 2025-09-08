<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            if (!Schema::hasColumn('grades', 'academic_year')) {
                $table->integer('academic_year')->after('teacher_id');
            }
            if (!Schema::hasColumn('grades', 'semester')) {
                $table->integer('semester')->after('academic_year');
            }
            $table->index(['academic_year','semester'], 'grades_year_sem_idx');
        });
    }

    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            try { $table->dropIndex('grades_year_sem_idx'); } catch (\Throwable $e) {}
            if (Schema::hasColumn('grades', 'semester')) { $table->dropColumn('semester'); }
            if (Schema::hasColumn('grades', 'academic_year')) { $table->dropColumn('academic_year'); }
        });
    }
};



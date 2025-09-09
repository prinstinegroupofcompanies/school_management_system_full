<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('sections')) {
            return;
        }

        Schema::table('sections', function (Blueprint $table) {
            if (!Schema::hasColumn('sections', 'section_teacher_id')) {
                // Add as plain column (no FK) to avoid dependency ordering issues
                $table->unsignedBigInteger('section_teacher_id')->nullable();
            }
            if (!Schema::hasColumn('sections', 'status')) {
                $table->string('status')->default('active');
            }
        });

        // Create the composite index if it does not already exist
        // Works for PostgreSQL; for other DBs it will be ignored if it already exists
        try {
            DB::statement('CREATE INDEX IF NOT EXISTS sections_section_teacher_id_status_index ON sections (section_teacher_id, status)');
        } catch (\Throwable $e) {
            // Fallback for databases without IF NOT EXISTS support
            try {
                Schema::table('sections', function (Blueprint $table) {
                    $table->index(['section_teacher_id', 'status'], 'sections_section_teacher_id_status_index');
                });
            } catch (\Throwable $e2) {
                // swallow to avoid breaking deploy if already exists
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('sections')) {
            return;
        }
        try {
            Schema::table('sections', function (Blueprint $table) {
                $table->dropIndex('sections_section_teacher_id_status_index');
            });
        } catch (\Throwable $e) {
            // ignore
        }
        Schema::table('sections', function (Blueprint $table) {
            if (Schema::hasColumn('sections', 'section_teacher_id')) {
                $table->dropConstrainedForeignId('section_teacher_id');
            }
            if (Schema::hasColumn('sections', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};



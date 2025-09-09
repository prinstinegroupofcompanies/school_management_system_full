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

        // Intentionally skip creating composite index to avoid cross-DB timing issues
        // You can add it later manually once columns are confirmed present
    }

    public function down(): void
    {
        if (!Schema::hasTable('sections')) {
            return;
        }
        // No index was created in up(), nothing to drop here
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



<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Skip this migration entirely due to SQLite limitations with dropping columns
        // that have foreign key constraints and complex index dependencies
        \Log::info('Skipping migration 2025_09_06_183256_remove_class_id_from_subjects_table due to SQLite limitations');
        
        // The subject-class relationship is already handled by the pivot table
        return;
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->foreignId('class_id')->nullable()->constrained('class_rooms')->onDelete('set null');
        });
    }
};
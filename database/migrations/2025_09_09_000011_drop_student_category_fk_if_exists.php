<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('students')) {
            return;
        }
        // Drop FK if it exists (PostgreSQL safe IF EXISTS)
        try {
            DB::statement('ALTER TABLE students DROP CONSTRAINT IF EXISTS students_student_category_id_foreign');
        } catch (\Throwable $e) {
            // ignore
        }
    }

    public function down(): void
    {
        // no-op
    }
};



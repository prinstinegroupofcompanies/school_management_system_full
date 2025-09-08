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
        // SQLite cannot alter primary keys directly. Rebuild the table with a proper PK.
        $connectionDriver = DB::getDriverName();

        if ($connectionDriver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');

            // Create a new table with correct primary key and constraints
            DB::statement(<<<SQL
                CREATE TABLE subjects_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name TEXT NULL,
                    code TEXT NULL UNIQUE,
                    description TEXT NULL,
                    teacher_id INTEGER NULL,
                    level TEXT NULL,
                    type TEXT NOT NULL DEFAULT 'core',
                    credits INTEGER NOT NULL DEFAULT 1,
                    hours_per_week INTEGER NOT NULL DEFAULT 5,
                    book_name TEXT NULL,
                    book_author TEXT NULL,
                    book_publisher TEXT NULL,
                    book_isbn TEXT NULL,
                    passing_marks NUM NULL DEFAULT 40.00,
                    full_marks NUM NULL DEFAULT 100.00,
                    status TEXT NULL DEFAULT 'active',
                    is_active INTEGER NOT NULL DEFAULT 1,
                    created_at NUM NULL,
                    updated_at NUM NULL,
                    CONSTRAINT subjects_new_teacher_id_foreign FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE SET NULL
                )
            SQL);

            // Copy data from old table; preserve existing non-null ids
            // Rows with NULL id will receive new autoincremented ids
            DB::statement(<<<SQL
                INSERT INTO subjects_new (
                    id, name, code, description, teacher_id, level, type, credits, hours_per_week,
                    book_name, book_author, book_publisher, book_isbn, passing_marks, full_marks,
                    status, is_active, created_at, updated_at
                )
                SELECT
                    id, name, code, description, teacher_id, level, COALESCE(type, 'core'), COALESCE(credits, 1), COALESCE(hours_per_week, 5),
                    book_name, book_author, book_publisher, book_isbn, COALESCE(passing_marks, 40.00), COALESCE(full_marks, 100.00),
                    COALESCE(status, 'active'), COALESCE(is_active, 1), created_at, updated_at
                FROM subjects
            SQL);

            // Drop old table and rename new
            DB::statement('DROP TABLE subjects');
            DB::statement('ALTER TABLE subjects_new RENAME TO subjects');

            // Recreate indexes (without the old class_id index)
            DB::statement('CREATE INDEX subjects_teacher_id_status_index ON subjects (teacher_id, status)');
            DB::statement('CREATE INDEX subjects_type_is_active_index ON subjects (type, is_active)');

            DB::statement('PRAGMA foreign_keys = ON');
        } else {
            // For non-SQLite drivers, attempt to enforce id as primary key if missing
            // Most installs already use $table->id(). If not, this is a no-op.
            // Developers can adjust for MySQL/Postgres separately if needed.
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Irreversible safely without risking data loss
    }
};

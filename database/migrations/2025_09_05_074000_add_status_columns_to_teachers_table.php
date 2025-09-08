<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('teachers')) {
            Schema::table('teachers', function (Blueprint $table) {
                if (!Schema::hasColumn('teachers', 'status')) {
                    $table->enum('status', ['active','inactive','suspended'])->default('active')->after('basic_salary');
                }
                if (!Schema::hasColumn('teachers', 'employment_status')) {
                    $table->enum('employment_status', ['active','on_leave','terminated'])->default('active')->after('status');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('teachers')) {
            Schema::table('teachers', function (Blueprint $table) {
                if (Schema::hasColumn('teachers', 'employment_status')) {
                    $table->dropColumn('employment_status');
                }
                if (Schema::hasColumn('teachers', 'status')) {
                    $table->dropColumn('status');
                }
            });
        }
    }
};



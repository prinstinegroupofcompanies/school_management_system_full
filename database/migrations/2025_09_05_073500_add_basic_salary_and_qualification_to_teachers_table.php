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
                if (!Schema::hasColumn('teachers', 'basic_salary')) {
                    $table->decimal('basic_salary', 10, 2)->default(0)->after('joining_date');
                }
                if (!Schema::hasColumn('teachers', 'qualification')) {
                    $table->string('qualification')->nullable()->after('designation_id');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('teachers')) {
            Schema::table('teachers', function (Blueprint $table) {
                if (Schema::hasColumn('teachers', 'basic_salary')) {
                    $table->dropColumn('basic_salary');
                }
                if (Schema::hasColumn('teachers', 'qualification')) {
                    $table->dropColumn('qualification');
                }
            });
        }
    }
};



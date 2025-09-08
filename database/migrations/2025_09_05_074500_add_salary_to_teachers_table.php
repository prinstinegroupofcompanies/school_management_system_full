<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('teachers') && !Schema::hasColumn('teachers', 'salary')) {
            Schema::table('teachers', function (Blueprint $table) {
                $table->decimal('salary', 10, 2)->default(0)->after('joining_date');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('teachers') && Schema::hasColumn('teachers', 'salary')) {
            Schema::table('teachers', function (Blueprint $table) {
                $table->dropColumn('salary');
            });
        }
    }
};



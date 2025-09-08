<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('teachers') && !Schema::hasColumn('teachers', 'teacher_id')) {
            Schema::table('teachers', function (Blueprint $table) {
                $table->string('teacher_id')->unique()->nullable()->after('user_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('teachers') && Schema::hasColumn('teachers', 'teacher_id')) {
            Schema::table('teachers', function (Blueprint $table) {
                $table->dropUnique(['teacher_id']);
                $table->dropColumn('teacher_id');
            });
        }
    }
};



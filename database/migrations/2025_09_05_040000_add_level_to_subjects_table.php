<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            if (!Schema::hasColumn('subjects', 'level')) {
                $table->string('level', 20)->default('junior')->after('teacher_id'); // junior|senior
            }
            if (Schema::hasColumn('subjects', 'credits')) {
                $table->dropColumn('credits');
            }
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            if (Schema::hasColumn('subjects', 'level')) {
                $table->dropColumn('level');
            }
            // Not re-adding credits on down for simplicity
        });
    }
};



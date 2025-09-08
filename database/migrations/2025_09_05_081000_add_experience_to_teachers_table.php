<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('teachers') && !Schema::hasColumn('teachers', 'experience')) {
            Schema::table('teachers', function (Blueprint $table) {
                $table->unsignedInteger('experience')->default(0)->after('qualification');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('teachers') && Schema::hasColumn('teachers', 'experience')) {
            Schema::table('teachers', function (Blueprint $table) {
                $table->dropColumn('experience');
            });
        }
    }
};



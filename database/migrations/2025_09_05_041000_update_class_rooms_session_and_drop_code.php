<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_rooms', function (Blueprint $table) {
            if (!Schema::hasColumn('class_rooms', 'session')) {
                $table->enum('session', ['A','B','C','D','E','F'])->default('A')->after('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('class_rooms', function (Blueprint $table) {
            if (Schema::hasColumn('class_rooms', 'session')) {
                $table->dropColumn('session');
            }
            // not re-adding code in down for simplicity
        });
    }
};



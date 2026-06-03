<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            if (!Schema::hasColumn('schools', 'login_background_image')) {
                $table->string('login_background_image')->nullable()->after('website');
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('schools', 'login_background_image')) {
            Schema::table('schools', function (Blueprint $table) {
                $table->dropColumn('login_background_image');
            });
        }
    }
};

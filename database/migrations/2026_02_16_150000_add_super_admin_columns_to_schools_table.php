<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            if (!Schema::hasColumn('schools', 'code')) {
                $table->string('code')->unique()->nullable()->after('name');
            }
            if (!Schema::hasColumn('schools', 'timezone')) {
                $table->string('timezone')->default('UTC')->after('address');
            }
            if (!Schema::hasColumn('schools', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('timezone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $columns = ['code', 'timezone', 'is_active'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('schools', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

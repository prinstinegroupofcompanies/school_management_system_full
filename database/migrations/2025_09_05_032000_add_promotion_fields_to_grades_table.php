<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            if (!Schema::hasColumn('grades', 'is_promoted')) {
                $table->boolean('is_promoted')->default(false)->after('approved_at');
            }
            if (!Schema::hasColumn('grades', 'honors_status')) {
                $table->string('honors_status')->nullable()->after('is_promoted');
            }
        });
    }

    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            if (Schema::hasColumn('grades', 'honors_status')) {
                $table->dropColumn('honors_status');
            }
            if (Schema::hasColumn('grades', 'is_promoted')) {
                $table->dropColumn('is_promoted');
            }
        });
    }
};



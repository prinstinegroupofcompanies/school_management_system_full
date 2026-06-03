<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('transport_routes', 'transport_id')) {
            Schema::table('transport_routes', function (Blueprint $table) {
                $table->foreignId('transport_id')->nullable()->after('id')->constrained('transports')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::table('transport_routes', function (Blueprint $table) {
            $table->dropForeign(['transport_id']);
        });
    }
};

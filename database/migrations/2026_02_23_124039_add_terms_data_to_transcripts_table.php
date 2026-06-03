<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transcripts', function (Blueprint $table) {
            if (!Schema::hasColumn('transcripts', 'terms_data')) {
                $table->json('terms_data')->nullable();
            }
            if (!Schema::hasColumn('transcripts', 'remarks')) {
                $table->text('remarks')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transcripts', function (Blueprint $table) {
            if (Schema::hasColumn('transcripts', 'terms_data')) {
                $table->dropColumn('terms_data');
            }
            if (Schema::hasColumn('transcripts', 'remarks')) {
                $table->dropColumn('remarks');
            }
        });
    }
};

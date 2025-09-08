<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the foreign key constraint first
        $foreignKeys = DB::select("PRAGMA foreign_key_list(subjects)");
        foreach ($foreignKeys as $foreignKey) {
            if ($foreignKey->table === 'class_rooms' && $foreignKey->from === 'class_id') {
                Schema::table('subjects', function (Blueprint $table) {
                    $table->dropForeign(['class_id']);
                });
                break;
            }
        }
        
        // Drop the column
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn('class_id');
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->foreignId('class_id')->nullable()->constrained('class_rooms')->onDelete('set null');
        });
    }
};
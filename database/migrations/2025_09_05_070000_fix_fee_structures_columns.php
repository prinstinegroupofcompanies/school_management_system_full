<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('fee_structures')) {
            Schema::table('fee_structures', function (Blueprint $table) {
                if (!Schema::hasColumn('fee_structures', 'class_id')) {
                    // Use plain integer to avoid SQLite foreign key alter limitations
                    $table->unsignedBigInteger('class_id')->nullable()->after('id');
                    $table->index(['class_id']);
                }
                if (!Schema::hasColumn('fee_structures', 'amount')) {
                    $table->decimal('amount', 10, 2)->default(0.00)->after('class_id');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('fee_structures')) {
            Schema::table('fee_structures', function (Blueprint $table) {
                if (Schema::hasColumn('fee_structures', 'amount')) {
                    $table->dropColumn('amount');
                }
                if (Schema::hasColumn('fee_structures', 'class_id')) {
                    $table->dropIndex(['class_id']);
                    $table->dropColumn('class_id');
                }
            });
        }
    }
};



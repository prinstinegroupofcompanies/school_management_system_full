<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'section_id')) {
                $table->unsignedBigInteger('section_id')->nullable()->change();
            }

            if (!Schema::hasColumn('students', 'student_id')) {
                $table->string('student_id')->unique()->after('user_id');
            }

            if (!Schema::hasColumn('students', 'level')) {
                $table->string('level', 20)->nullable()->after('date_of_birth');
            }

            if (!Schema::hasColumn('students', 'phone')) {
                $table->string('phone')->nullable()->after('level');
            }

            if (!Schema::hasColumn('students', 'address')) {
                $table->string('address')->nullable()->after('phone');
            }

            if (!Schema::hasColumn('students', 'guardian_name')) {
                $table->string('guardian_name')->nullable()->after('address');
            }

            if (!Schema::hasColumn('students', 'guardian_phone')) {
                $table->string('guardian_phone')->nullable()->after('guardian_name');
            }

            if (!Schema::hasColumn('students', 'guardian_email')) {
                $table->string('guardian_email')->nullable()->after('guardian_phone');
            }

            if (!Schema::hasColumn('students', 'guardian_address')) {
                $table->string('guardian_address')->nullable()->after('guardian_email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'guardian_address')) {
                $table->dropColumn('guardian_address');
            }
            if (Schema::hasColumn('students', 'guardian_email')) {
                $table->dropColumn('guardian_email');
            }
            if (Schema::hasColumn('students', 'guardian_phone')) {
                $table->dropColumn('guardian_phone');
            }
            if (Schema::hasColumn('students', 'guardian_name')) {
                $table->dropColumn('guardian_name');
            }
            if (Schema::hasColumn('students', 'address')) {
                $table->dropColumn('address');
            }
            if (Schema::hasColumn('students', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('students', 'level')) {
                $table->dropColumn('level');
            }
            if (Schema::hasColumn('students', 'student_id')) {
                $table->dropUnique(['student_id']);
                $table->dropColumn('student_id');
            }
            if (Schema::hasColumn('students', 'section_id')) {
                $table->unsignedBigInteger('section_id')->nullable(false)->change();
            }
        });
    }
};



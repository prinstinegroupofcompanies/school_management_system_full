<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Student;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Find users with user_type = 'student' who don't have student records
        $studentUsers = User::where('user_type', 'student')
            ->whereDoesntHave('student')
            ->get();

        foreach ($studentUsers as $user) {
            // Create student record for each user
            $student = new Student();
            $student->user_id = $user->id;
            $student->admission_no = Student::generateAdmissionNumber();
            $student->student_id = Student::generateStudentNumber();
            $student->first_name = explode(' ', $user->name)[0] ?? $user->name;
            $student->last_name = explode(' ', $user->name)[1] ?? '';
            $student->academic_year = date('Y');
            $student->admission_date = now();
            $student->gender = 'male'; // Default, can be updated later
            $student->date_of_birth = now()->subYears(18); // Default age 18
            $student->nationality = 'Liberian';
            $student->phone = $user->phone ?? '0000000000';
            $student->address = $user->address ?? 'Monrovia';
            $student->status = 'active';
            $student->is_active = true;
            
            // Assign to a default class (class_id = 1) if it exists
            $defaultClass = DB::table('class_rooms')->first();
            if ($defaultClass) {
                $student->class_id = $defaultClass->id;
            } else {
                $student->class_id = 1; // Fallback
            }
            
            // Create a default guardian record
            // Note: guardians table structure uses user_id, not first_name/last_name
            $guardianUser = DB::table('users')->where('email', 'guardian.' . $user->email)->first();
            if (!$guardianUser) {
                $guardianUser = DB::table('users')->insertGetId([
                    'name' => 'Guardian Of ' . $user->name,
                    'email' => 'guardian.' . $user->email,
                    'password' => bcrypt('password'),
                    'user_type' => 'guardian',
                    'phone' => $user->phone ?? '0000000000',
                    'address' => $user->address ?? 'Monrovia',
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $guardianUser = $guardianUser->id;
            }
            
            $guardian = DB::table('guardians')->insertGetId([
                'user_id' => $guardianUser,
                'guardian_id' => 'G' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                'relationship' => 'parent',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $student->guardian_id = $guardian;
            $student->save();
            
            echo "Created student record for user: {$user->name} (ID: {$user->id})\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove student records created by this migration
        // This is a destructive operation, so we'll just log it
        \Log::info('Rollback of create_missing_student_records migration requested');
    }
};
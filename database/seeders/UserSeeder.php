<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(['email' => 'admin@school.com'], [
            'name' => 'Admin User',
            'password' => Hash::make('password'),
            'user_type' => 'admin',
            'is_active' => true,
        ]);
        User::updateOrCreate(['email' => 'teacher@school.com'], [
            'name' => 'Teacher User',
            'password' => Hash::make('password'),
            'user_type' => 'teacher',
            'is_active' => true,
        ]);
        User::updateOrCreate(['email' => 'student@school.com'], [
            'name' => 'Student User',
            'password' => Hash::make('password'),
            'user_type' => 'student',
            'is_active' => true,
        ]);
        User::updateOrCreate(['email' => 'finance@school.com'], [
            'name' => 'Finance User',
            'password' => Hash::make('password'),
            'user_type' => 'finance',
            'is_active' => true,
        ]);
    }
}
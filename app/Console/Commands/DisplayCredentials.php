<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class DisplayCredentials extends Command
{
    protected $signature = 'users:credentials';
    protected $description = 'Display login credentials for all users';

    public function handle()
    {
        $this->info('==========================================');
        $this->info('        LOGIN CREDENTIALS');
        $this->info('==========================================');
        $this->newLine();

        $users = User::with('roles')->get();

        if ($users->isEmpty()) {
            $this->warn('No users found. Run seeder first:');
            $this->line('  php artisan db:seed --class=AllUsersSeeder');
            return;
        }

        $tableData = [];
        foreach ($users as $user) {
            $roles = $user->getRoleNames()->implode(', ') ?: $user->user_type;
            
            // Note: We cannot retrieve passwords from database, so we show the pattern
            // For seeded users, password format is typically: RoleName@2025
            $passwordHint = $this->getPasswordHint($roles, $user->email);
            
            $tableData[] = [
                'Role' => $roles,
                'Name' => $user->name,
                'Email' => $user->email,
                'Username' => $user->username ?? 'N/A',
                'Password Hint' => $passwordHint,
            ];
        }

        $this->table(
            ['Role', 'Name', 'Email', 'Username', 'Password Hint'],
            $tableData
        );

        $this->newLine();
        $this->info('Note: Passwords are hashed and cannot be retrieved.');
        $this->info('For seeded users, password format is typically: RoleName@2025');
        $this->info('Run: php artisan db:seed --class=AllUsersSeeder');
        $this->newLine();
    }

    protected function getPasswordHint($roles, $email)
    {
        $roleMap = [
            'super_admin' => 'SuperAdmin@2025',
            'vpi' => 'VPI@2025',
            'vpa' => 'VPA@2025',
            'registrar' => 'Registrar@2025',
            'teacher' => 'Teacher@2025',
            'librarian' => 'Librarian@2025',
            'accountant' => 'Finance@2025',
            'conductor_driver' => 'Driver@2025',
            'student' => 'Student@2025',
            'parent' => 'Parent@2025',
        ];

        foreach ($roleMap as $role => $password) {
            if (stripos($roles, $role) !== false || stripos($email, $role) !== false) {
                return $password;
            }
        }

        return 'Check LOGIN_CREDENTIALS.md';
    }
}

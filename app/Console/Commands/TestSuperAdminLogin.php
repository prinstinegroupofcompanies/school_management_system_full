<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TestSuperAdminLogin extends Command
{
    protected $signature = 'test:super-admin-login';
    protected $description = 'Verify Super Admin user exists and can access dashboard (dry run).';

    public function handle(): int
    {
        $email = 'superadmin@school.com';
        $password = 'password';

        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("FAIL: User {$email} not found. Run: php artisan db:seed --class=DefaultSchoolAndSuperAdminSeeder");
            return 1;
        }

        if (!Hash::check($password, $user->password)) {
            $this->error('FAIL: Password does not match (expected "password").');
            return 1;
        }

        if ($user->school_id !== null) {
            $this->warn("WARN: Super Admin should have school_id=null, got: {$user->school_id}");
        }

        if (!$user->hasRole('super_admin')) {
            $this->error('FAIL: User does not have role super_admin.');
            return 1;
        }

        if (!method_exists($user, 'isSuperAdmin') || !$user->isSuperAdmin()) {
            $this->error('FAIL: User isSuperAdmin() returned false.');
            return 1;
        }

        $this->info('OK: Super Admin user exists.');
        $this->info('   Email: ' . $email);
        $this->info('   Password: ' . $password);
        $this->info('   school_id: ' . json_encode($user->school_id));
        $this->info('   Dashboard route: ' . route('super_admin.dashboard'));
        $this->newLine();
        $this->info('To test in browser: log in at your app URL with the credentials above.');
        return 0;
    }
}

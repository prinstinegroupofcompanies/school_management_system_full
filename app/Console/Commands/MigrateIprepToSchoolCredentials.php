<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class MigrateIprepToSchoolCredentials extends Command
{
    protected $signature = 'users:migrate-iprep-to-school
                            {--dry-run : Show what would be updated without making changes}';
    protected $description = 'Update existing user emails from @iprep.edu.lr to @school.com and usernames from iprep. to school.';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->warn('Dry run – no changes will be made.');
        }

        $users = User::where('email', 'like', '%@iprep.edu.lr')
            ->orWhere('username', 'like', 'iprep.%')
            ->get();

        if ($users->isEmpty()) {
            $this->info('No users found with IPREP emails or usernames. Nothing to update.');
            return self::SUCCESS;
        }

        $this->info('Found ' . $users->count() . ' user(s) to update.');
        $updated = 0;

        foreach ($users as $user) {
            $emailChanged = str_ends_with($user->email, '@iprep.edu.lr');
            $usernameChanged = str_starts_with($user->username ?? '', 'iprep.');
            $newEmail = $emailChanged ? str_replace('@iprep.edu.lr', '@school.com', $user->email) : $user->email;
            $newUsername = $usernameChanged ? 'school.' . substr($user->username, 6) : $user->username;

            if ($emailChanged || $usernameChanged) {
                if (!$dryRun && $emailChanged && User::where('email', $newEmail)->where('id', '!=', $user->id)->exists()) {
                    $this->line("  {$user->email} → skip ({$newEmail} already exists)");
                    continue;
                }
                $this->line("  {$user->email} / {$user->username} → {$newEmail} / {$newUsername}");
                if (!$dryRun) {
                    $user->email = $newEmail;
                    $user->username = $newUsername;
                    $user->save();
                    $updated++;
                }
            }
        }

        if ($dryRun) {
            $this->info('Dry run complete. Run without --dry-run to apply changes.');
        } else {
            $this->info("Updated {$updated} user(s).");
        }

        return self::SUCCESS;
    }
}

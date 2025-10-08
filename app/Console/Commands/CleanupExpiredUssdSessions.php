<?php

namespace App\Console\Commands;

use App\Models\UssdSession;
use Illuminate\Console\Command;

class CleanupExpiredUssdSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ussd:cleanup-expired {--days=7 : Number of days to keep expired sessions}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired USSD sessions older than specified days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = now()->subDays($days);
        
        $this->info("Cleaning up USSD sessions older than {$days} days...");
        
        // Count sessions to be cleaned up
        $expiredCount = UssdSession::where('expires_at', '<', $cutoffDate)
            ->whereIn('status', ['timeout', 'completed', 'cancelled'])
            ->count();

        if ($expiredCount === 0) {
            $this->info('No expired USSD sessions found to clean up.');
            return 0;
        }

        $this->info("Found {$expiredCount} expired USSD sessions to clean up.");

        if ($this->confirm('Do you want to proceed with the cleanup?')) {
            // Delete expired sessions
            $deletedCount = UssdSession::where('expires_at', '<', $cutoffDate)
                ->whereIn('status', ['timeout', 'completed', 'cancelled'])
                ->delete();

            $this->info("Successfully cleaned up {$deletedCount} expired USSD sessions.");
        } else {
            $this->info('Cleanup cancelled.');
        }

        // Also clean up any sessions that should have expired but are still marked as active
        $staleActiveCount = UssdSession::where('status', 'active')
            ->where('expires_at', '<', now())
            ->update(['status' => 'timeout']);

        if ($staleActiveCount > 0) {
            $this->info("Marked {$staleActiveCount} stale active sessions as timeout.");
        }

        return 0;
    }
}

<?php

namespace App\Console\Commands;

use App\Models\SmsNotification;
use App\Services\SmsService;
use Illuminate\Console\Command;

class SendScheduledSms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:send-scheduled {--limit=100 : Maximum number of SMS to send}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send scheduled SMS notifications that are ready to be sent';

    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        parent::__construct();
        $this->smsService = $smsService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');
        
        $this->info('Starting scheduled SMS sending process...');
        
        // Get scheduled notifications ready to send
        $scheduledNotifications = SmsNotification::readyToSend()
            ->limit($limit)
            ->get();

        if ($scheduledNotifications->isEmpty()) {
            $this->info('No scheduled notifications ready to send.');
            return 0;
        }

        $this->info("Found {$scheduledNotifications->count()} scheduled notifications ready to send.");

        $successCount = 0;
        $failureCount = 0;

        foreach ($scheduledNotifications as $notification) {
            try {
                $result = $this->smsService->sendSms($notification);
                
                if ($result) {
                    $successCount++;
                    $this->line("✓ Sent SMS to {$notification->phone_number}");
                } else {
                    $failureCount++;
                    $this->error("✗ Failed to send SMS to {$notification->phone_number}");
                }
            } catch (\Exception $e) {
                $failureCount++;
                $this->error("✗ Exception sending SMS to {$notification->phone_number}: {$e->getMessage()}");
            }
        }

        $this->info("\nSMS sending completed:");
        $this->info("✓ Successfully sent: {$successCount}");
        $this->info("✗ Failed: {$failureCount}");
        $this->info("Total processed: " . ($successCount + $failureCount));

        // Clean up expired notifications
        $expiredCount = SmsNotification::where('expires_at', '<', now())
            ->where('status', 'pending')
            ->update(['status' => 'expired']);

        if ($expiredCount > 0) {
            $this->info("Marked {$expiredCount} expired notifications as expired.");
        }

        return 0;
    }
}

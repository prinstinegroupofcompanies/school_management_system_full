<?php

namespace App\Services;

use App\Models\SmsNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $provider;
    protected $apiKey;
    protected $apiUrl;
    protected $senderId;

    public function __construct()
    {
        $this->provider = config('sms.provider', 'africas_talking');
        $this->apiKey = config('sms.api_key');
        $this->apiUrl = config('sms.api_url');
        $this->senderId = config('sms.sender_id', 'SCHOOL');
    }

    /**
     * Send SMS notification
     */
    public function sendSms(SmsNotification $notification)
    {
        try {
            $response = $this->sendViaProvider($notification);
            
            if ($response['success']) {
                $notification->markAsSent(
                    $response['message_id'] ?? null,
                    $response['cost'] ?? null
                );
                
                Log::info('SMS sent successfully', [
                    'notification_id' => $notification->id,
                    'phone' => $notification->phone_number,
                    'provider_response' => $response
                ]);
                
                return true;
            } else {
                $notification->markAsFailed($response['error'] ?? 'Unknown error');
                
                Log::error('SMS sending failed', [
                    'notification_id' => $notification->id,
                    'phone' => $notification->phone_number,
                    'error' => $response['error'] ?? 'Unknown error'
                ]);
                
                return false;
            }
        } catch (\Exception $e) {
            $notification->markAsFailed($e->getMessage());
            
            Log::error('SMS sending exception', [
                'notification_id' => $notification->id,
                'phone' => $notification->phone_number,
                'exception' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Send SMS via configured provider
     */
    protected function sendViaProvider(SmsNotification $notification)
    {
        switch ($this->provider) {
            case 'africas_talking':
                return $this->sendViaAfricasTalking($notification);
            case 'twilio':
                return $this->sendViaTwilio($notification);
            case 'nexmo':
                return $this->sendViaNexmo($notification);
            default:
                return $this->sendViaAfricasTalking($notification);
        }
    }

    /**
     * Send via Africa's Talking
     */
    protected function sendViaAfricasTalking(SmsNotification $notification)
    {
        try {
            $response = Http::withHeaders([
                'apiKey' => $this->apiKey,
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json'
            ])->post($this->apiUrl . '/messaging', [
                'username' => config('sms.username'),
                'to' => $this->formatPhoneNumber($notification->phone_number),
                'message' => $notification->message,
                'from' => $this->senderId
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'message_id' => $data['SMSMessageData']['Recipients'][0]['messageId'] ?? null,
                    'cost' => $data['SMSMessageData']['Recipients'][0]['cost'] ?? null
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $response->body()
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send via Twilio
     */
    protected function sendViaTwilio(SmsNotification $notification)
    {
        try {
            $response = Http::withBasicAuth(
                config('sms.account_sid'),
                $this->apiKey
            )->post($this->apiUrl . '/Messages.json', [
                'From' => $this->senderId,
                'To' => $this->formatPhoneNumber($notification->phone_number),
                'Body' => $notification->message
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'message_id' => $data['sid'] ?? null,
                    'cost' => $data['price'] ?? null
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $response->body()
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send via Nexmo (Vonage)
     */
    protected function sendViaNexmo(SmsNotification $notification)
    {
        try {
            $response = Http::post($this->apiUrl, [
                'api_key' => config('sms.api_key'),
                'api_secret' => config('sms.api_secret'),
                'to' => $this->formatPhoneNumber($notification->phone_number),
                'from' => $this->senderId,
                'text' => $notification->message
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'message_id' => $data['messages'][0]['message-id'] ?? null,
                    'cost' => $data['messages'][0]['cost'] ?? null
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $response->body()
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Format phone number for international format
     */
    protected function formatPhoneNumber($phoneNumber)
    {
        // Remove any non-numeric characters
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Add country code if not present (assuming Liberia +231)
        if (!str_starts_with($phoneNumber, '231')) {
            $phoneNumber = '231' . $phoneNumber;
        }
        
        return '+' . $phoneNumber;
    }

    /**
     * Send bulk SMS notifications
     */
    public function sendBulkSms(array $notifications)
    {
        $results = [];
        
        foreach ($notifications as $notification) {
            $results[] = $this->sendSms($notification);
        }
        
        return $results;
    }

    /**
     * Send SMS to multiple recipients
     */
    public function sendToMultipleRecipients(array $phoneNumbers, $message, $type = 'general', $metadata = [])
    {
        $notifications = [];
        
        foreach ($phoneNumbers as $phoneNumber) {
            $notifications[] = SmsNotification::create([
                'phone_number' => $phoneNumber,
                'message' => $message,
                'type' => $type,
                'metadata' => $metadata
            ]);
        }
        
        return $this->sendBulkSms($notifications);
    }

    /**
     * Send attendance notification
     */
    public function sendAttendanceNotification($student, $attendanceData)
    {
        $message = "Dear Parent/Guardian,\n";
        $message .= "Student: {$student->first_name} {$student->last_name}\n";
        $message .= "Class: {$student->class->name}\n";
        $message .= "Date: {$attendanceData['date']}\n";
        $message .= "Status: {$attendanceData['status']}\n";
        $message .= "Time: {$attendanceData['time']}\n\n";
        $message .= "Thank you for your attention.\n";
        $message .= "- School Management System";

        return SmsNotification::createForStudent(
            $student,
            $message,
            'attendance',
            $attendanceData
        );
    }

    /**
     * Send grades notification
     */
    public function sendGradesNotification($student, $gradesData)
    {
        $message = "Dear Parent/Guardian,\n";
        $message .= "Student: {$student->first_name} {$student->last_name}\n";
        $message .= "Class: {$student->class->name}\n";
        $message .= "Period: {$gradesData['period']}\n\n";
        
        $message .= "GRADES:\n";
        foreach ($gradesData['grades'] as $grade) {
            $message .= "{$grade['subject']}: {$grade['grade']}\n";
        }
        
        $message .= "\nOverall Performance: {$gradesData['performance']}\n";
        $message .= "Thank you for your attention.\n";
        $message .= "- School Management System";

        return SmsNotification::createForStudent(
            $student,
            $message,
            'grades',
            $gradesData
        );
    }

    /**
     * Send urgent notification
     */
    public function sendUrgentNotification($student, $urgentMessage, $metadata = [])
    {
        $message = "URGENT NOTICE\n\n";
        $message .= "Student: {$student->first_name} {$student->last_name}\n";
        $message .= "Class: {$student->class->name}\n\n";
        $message .= $urgentMessage . "\n\n";
        $message .= "Please contact the school immediately.\n";
        $message .= "- School Management System";

        return SmsNotification::createForStudent(
            $student,
            $message,
            'urgent',
            $metadata
        );
    }

    /**
     * Send exam notification
     */
    public function sendExamNotification($student, $examData)
    {
        $message = "EXAM NOTIFICATION\n\n";
        $message .= "Student: {$student->first_name} {$student->last_name}\n";
        $message .= "Class: {$student->class->name}\n";
        $message .= "Subject: {$examData['subject']}\n";
        $message .= "Date: {$examData['date']}\n";
        $message .= "Time: {$examData['time']}\n";
        $message .= "Venue: {$examData['venue']}\n\n";
        $message .= "Please ensure your child is prepared.\n";
        $message .= "- School Management System";

        return SmsNotification::createForStudent(
            $student,
            $message,
            'exam',
            $examData
        );
    }

    /**
     * Send event notification
     */
    public function sendEventNotification($student, $eventData)
    {
        $message = "EVENT NOTIFICATION\n\n";
        $message .= "Student: {$student->first_name} {$student->last_name}\n";
        $message .= "Class: {$student->class->name}\n";
        $message .= "Event: {$eventData['title']}\n";
        $message .= "Date: {$eventData['date']}\n";
        $message .= "Time: {$eventData['time']}\n";
        $message .= "Venue: {$eventData['venue']}\n\n";
        $message .= "We look forward to your participation.\n";
        $message .= "- School Management System";

        return SmsNotification::createForStudent(
            $student,
            $message,
            'event',
            $eventData
        );
    }

    /**
     * Send payment notification
     */
    public function sendPaymentNotification($student, $paymentData)
    {
        $message = "PAYMENT NOTIFICATION\n\n";
        $message .= "Student: {$student->first_name} {$student->last_name}\n";
        $message .= "Class: {$student->class->name}\n";
        $message .= "Amount: \${$paymentData['amount']}\n";
        $message .= "Due Date: {$paymentData['due_date']}\n";
        $message .= "Description: {$paymentData['description']}\n\n";
        $message .= "Please make payment before the due date.\n";
        $message .= "- School Management System";

        return SmsNotification::createForStudent(
            $student,
            $message,
            'payment',
            $paymentData
        );
    }
}

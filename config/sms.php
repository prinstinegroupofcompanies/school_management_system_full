<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SMS Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for SMS services used by the
    | school management system for sending notifications to parents and students.
    |
    */

    'provider' => env('SMS_PROVIDER', 'africas_talking'),

    'api_key' => env('SMS_API_KEY'),
    'api_url' => env('SMS_API_URL'),
    'sender_id' => env('SMS_SENDER_ID', 'SCHOOL'),

    // Africa's Talking Configuration
    'username' => env('SMS_USERNAME'),
    'api_key_at' => env('SMS_API_KEY_AT'),

    // Twilio Configuration
    'account_sid' => env('TWILIO_ACCOUNT_SID'),
    'auth_token' => env('TWILIO_AUTH_TOKEN'),

    // Nexmo/Vonage Configuration
    'api_secret' => env('NEXMO_API_SECRET'),

    // Default settings
    'default_country_code' => '231', // Liberia
    'max_message_length' => 160,
    'retry_attempts' => 3,
    'retry_delay' => 30, // seconds

    // Rate limiting
    'rate_limit' => [
        'max_per_minute' => 60,
        'max_per_hour' => 1000,
        'max_per_day' => 10000,
    ],

    // Message templates
    'templates' => [
        'attendance' => [
            'student_absent' => "Dear Parent/Guardian,\nStudent: {student_name}\nClass: {class_name}\nDate: {date}\nStatus: Absent\nTime: {time}\nPlease contact the school if this is unexpected.\n- School Management System",
            'student_present' => "Dear Parent/Guardian,\nStudent: {student_name}\nClass: {class_name}\nDate: {date}\nStatus: Present\nTime: {time}\nThank you.\n- School Management System",
            'student_late' => "Dear Parent/Guardian,\nStudent: {student_name}\nClass: {class_name}\nDate: {date}\nStatus: Late\nTime: {time}\nPlease ensure punctuality.\n- School Management System",
        ],
        'grades' => [
            'grades_available' => "Dear Parent/Guardian,\nStudent: {student_name}\nClass: {class_name}\nPeriod: {period}\nGrades are now available. Please check the portal or contact the school.\n- School Management System",
            'grades_summary' => "Dear Parent/Guardian,\nStudent: {student_name}\nClass: {class_name}\nPeriod: {period}\nOverall Grade: {overall_grade}\nPerformance: {performance}\nPlease contact the school for detailed grades.\n- School Management System",
        ],
        'urgent' => [
            'general' => "URGENT NOTICE\n\nStudent: {student_name}\nClass: {class_name}\nMessage: {message}\nPlease contact the school immediately.\n- School Management System",
            'emergency' => "EMERGENCY ALERT\n\nStudent: {student_name}\nClass: {class_name}\nMessage: {message}\nPlease contact the school immediately.\n- School Management System",
        ],
        'exam' => [
            'exam_schedule' => "EXAM NOTIFICATION\n\nStudent: {student_name}\nClass: {class_name}\nSubject: {subject}\nDate: {date}\nTime: {time}\nVenue: {venue}\nPlease ensure your child is prepared.\n- School Management System",
            'exam_reminder' => "EXAM REMINDER\n\nStudent: {student_name}\nClass: {class_name}\nSubject: {subject}\nDate: {date}\nTime: {time}\nVenue: {venue}\nExam is tomorrow. Please prepare accordingly.\n- School Management System",
        ],
        'event' => [
            'event_notification' => "EVENT NOTIFICATION\n\nStudent: {student_name}\nClass: {class_name}\nEvent: {event_title}\nDate: {date}\nTime: {time}\nVenue: {venue}\nWe look forward to your participation.\n- School Management System",
            'event_reminder' => "EVENT REMINDER\n\nStudent: {student_name}\nClass: {class_name}\nEvent: {event_title}\nDate: {date}\nTime: {time}\nVenue: {venue}\nEvent is tomorrow. Please prepare accordingly.\n- School Management System",
        ],
        'payment' => [
            'payment_due' => "PAYMENT NOTIFICATION\n\nStudent: {student_name}\nClass: {class_name}\nAmount: {amount}\nDue Date: {due_date}\nDescription: {description}\nPlease make payment before the due date.\n- School Management System",
            'payment_overdue' => "PAYMENT OVERDUE\n\nStudent: {student_name}\nClass: {class_name}\nAmount: {amount}\nDue Date: {due_date}\nDescription: {description}\nPayment is overdue. Please contact the school immediately.\n- School Management System",
            'payment_received' => "PAYMENT CONFIRMATION\n\nStudent: {student_name}\nClass: {class_name}\nAmount: {amount}\nDate: {payment_date}\nDescription: {description}\nPayment received successfully. Thank you.\n- School Management System",
        ],
    ],

    // Delivery status webhook URLs
    'webhooks' => [
        'delivery_status' => env('SMS_DELIVERY_WEBHOOK_URL'),
        'incoming_sms' => env('SMS_INCOMING_WEBHOOK_URL'),
    ],

    // Queue configuration
    'queue' => [
        'enabled' => env('SMS_QUEUE_ENABLED', true),
        'connection' => env('SMS_QUEUE_CONNECTION', 'default'),
        'queue' => env('SMS_QUEUE_NAME', 'sms'),
    ],

    // Logging
    'logging' => [
        'enabled' => env('SMS_LOGGING_ENABLED', true),
        'level' => env('SMS_LOG_LEVEL', 'info'),
    ],
];

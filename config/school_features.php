<?php

/**
 * Feature add-ons that Super Admin can enable/disable per school.
 * Keys are used in school_addons.feature_key and in views/middleware.
 */
return [
    'students' => [
        'label' => 'Students',
        'description' => 'Student management, enrollment, records',
    ],
    'teachers' => [
        'label' => 'Teachers',
        'description' => 'Teacher management and assignments',
    ],
    'classes' => [
        'label' => 'Classes',
        'description' => 'Class and section management',
    ],
    'subjects' => [
        'label' => 'Subjects',
        'description' => 'Subject and curriculum management',
    ],
    'grades' => [
        'label' => 'Grades & Transcripts',
        'description' => 'Grade approvals, student grade sheets (by term/full year), and generated transcripts',
    ],
    'staff' => [
        'label' => 'Staff',
        'description' => 'Staff, payroll, performance',
    ],
    'attendance' => [
        'label' => 'Attendance',
        'description' => 'Student and teacher attendance',
    ],
    'lesson_plans' => [
        'label' => 'Lesson Plans',
        'description' => 'Lesson plans and approvals',
    ],
    'users' => [
        'label' => 'Users & Roles',
        'description' => 'User and permission management',
    ],
    'notifications' => [
        'label' => 'Notifications',
        'description' => 'Send and manage notifications',
    ],
    'reports' => [
        'label' => 'Reports',
        'description' => 'Academic and other reports',
    ],
    'finance' => [
        'label' => 'Finance',
        'description' => 'Fees, payments, finance officers',
    ],
    'transport' => [
        'label' => 'Transport',
        'description' => 'Transport and vehicles',
    ],
    'hostel' => [
        'label' => 'Hostel',
        'description' => 'Hostel and room management',
    ],
    'schedules' => [
        'label' => 'Schedules',
        'description' => 'Timetables and schedules',
    ],
    'exams' => [
        'label' => 'Exams',
        'description' => 'Exam types, schedules, marks',
    ],
    'library' => [
        'label' => 'Library',
        'description' => 'Books, members, issue/return',
    ],
    'signatures' => [
        'label' => 'Signatures',
        'description' => 'E-signature settings and templates',
    ],
    'inventory' => [
        'label' => 'Inventory',
        'description' => 'Inventory items, categories, suppliers',
    ],
    'visitation' => [
        'label' => 'Visitation',
        'description' => 'Visitor management, check-in/out, logs',
    ],
    'checkin' => [
        'label' => 'Check-in',
        'description' => 'Student/staff check-in and attendance',
    ],
    'health_safety' => [
        'label' => 'Health & Safety',
        'description' => 'Health incidents, safety reports',
    ],
];

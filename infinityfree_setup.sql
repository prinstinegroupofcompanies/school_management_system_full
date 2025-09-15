-- ========================================
-- Liberia School Management System
-- Database Setup for InfinityFree
-- ========================================

-- Create database (run this in phpMyAdmin)
-- CREATE DATABASE if0_XXXXXXX_school_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the database
-- USE if0_XXXXXXX_school_management;

-- ========================================
-- Core Tables
-- ========================================

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `email_verified_at` timestamp NULL DEFAULT NULL,
    `password` varchar(255) NOT NULL,
    `role` varchar(50) DEFAULT 'student',
    `phone` varchar(20) DEFAULT NULL,
    `address` text DEFAULT NULL,
    `date_of_birth` date DEFAULT NULL,
    `gender` enum('male','female','other') DEFAULT NULL,
    `profile_image` varchar(255) DEFAULT NULL,
    `is_active` tinyint(1) DEFAULT 1,
    `remember_token` varchar(100) DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`),
    KEY `users_role_index` (`role`),
    KEY `users_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Schools table
CREATE TABLE IF NOT EXISTS `schools` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `address` text NOT NULL,
    `phone` varchar(20) DEFAULT NULL,
    `email` varchar(255) DEFAULT NULL,
    `website` varchar(255) DEFAULT NULL,
    `logo` varchar(255) DEFAULT NULL,
    `motto` text DEFAULT NULL,
    `established_year` year DEFAULT NULL,
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Class rooms table
CREATE TABLE IF NOT EXISTS `class_rooms` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `class_code` varchar(50) DEFAULT NULL,
    `description` text DEFAULT NULL,
    `capacity` int(11) DEFAULT NULL,
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `class_rooms_class_code_unique` (`class_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Students table
CREATE TABLE IF NOT EXISTS `students` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` bigint(20) UNSIGNED NOT NULL,
    `student_id` varchar(50) NOT NULL,
    `class_room_id` bigint(20) UNSIGNED DEFAULT NULL,
    `admission_date` date NOT NULL,
    `guardian_name` varchar(255) DEFAULT NULL,
    `guardian_phone` varchar(20) DEFAULT NULL,
    `guardian_email` varchar(255) DEFAULT NULL,
    `guardian_relationship` varchar(50) DEFAULT NULL,
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `students_student_id_unique` (`student_id`),
    UNIQUE KEY `students_user_id_unique` (`user_id`),
    KEY `students_class_room_id_foreign` (`class_room_id`),
    CONSTRAINT `students_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `students_class_room_id_foreign` FOREIGN KEY (`class_room_id`) REFERENCES `class_rooms` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Teachers table
CREATE TABLE IF NOT EXISTS `teachers` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` bigint(20) UNSIGNED NOT NULL,
    `employee_id` varchar(50) NOT NULL,
    `department` varchar(100) DEFAULT NULL,
    `qualification` varchar(255) DEFAULT NULL,
    `experience_years` int(11) DEFAULT 0,
    `salary` decimal(10,2) DEFAULT NULL,
    `hire_date` date DEFAULT NULL,
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `teachers_employee_id_unique` (`employee_id`),
    UNIQUE KEY `teachers_user_id_unique` (`user_id`),
    CONSTRAINT `teachers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Subjects table
CREATE TABLE IF NOT EXISTS `subjects` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `code` varchar(50) DEFAULT NULL,
    `description` text DEFAULT NULL,
    `credits` int(11) DEFAULT 1,
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `subjects_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Fee structures table
CREATE TABLE IF NOT EXISTS `fee_structures` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `class_room_id` bigint(20) UNSIGNED DEFAULT NULL,
    `amount` decimal(10,2) NOT NULL,
    `description` text DEFAULT NULL,
    `due_date` date DEFAULT NULL,
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `fee_structures_class_room_id_foreign` (`class_room_id`),
    CONSTRAINT `fee_structures_class_room_id_foreign` FOREIGN KEY (`class_room_id`) REFERENCES `class_rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Fee payments table
CREATE TABLE IF NOT EXISTS `fee_payments` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `student_id` bigint(20) UNSIGNED NOT NULL,
    `fee_structure_id` bigint(20) UNSIGNED NOT NULL,
    `amount` decimal(10,2) NOT NULL,
    `payment_date` date NOT NULL,
    `payment_method` varchar(50) DEFAULT 'cash',
    `transaction_id` varchar(255) DEFAULT NULL,
    `status` enum('pending','completed','failed','refunded') DEFAULT 'pending',
    `notes` text DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `fee_payments_student_id_foreign` (`student_id`),
    KEY `fee_payments_fee_structure_id_foreign` (`fee_structure_id`),
    CONSTRAINT `fee_payments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fee_payments_fee_structure_id_foreign` FOREIGN KEY (`fee_structure_id`) REFERENCES `fee_structures` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Student attendances table
CREATE TABLE IF NOT EXISTS `student_attendances` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `student_id` bigint(20) UNSIGNED NOT NULL,
    `class_room_id` bigint(20) UNSIGNED NOT NULL,
    `date` date NOT NULL,
    `status` enum('present','absent','late','excused') DEFAULT 'present',
    `notes` text DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `student_attendances_student_id_foreign` (`student_id`),
    KEY `student_attendances_class_room_id_foreign` (`class_room_id`),
    UNIQUE KEY `student_attendances_student_id_class_room_id_date_unique` (`student_id`,`class_room_id`,`date`),
    CONSTRAINT `student_attendances_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
    CONSTRAINT `student_attendances_class_room_id_foreign` FOREIGN KEY (`class_room_id`) REFERENCES `class_rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Homeworks table
CREATE TABLE IF NOT EXISTS `homeworks` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `description` text NOT NULL,
    `class_room_id` bigint(20) UNSIGNED NOT NULL,
    `subject_id` bigint(20) UNSIGNED NOT NULL,
    `teacher_id` bigint(20) UNSIGNED NOT NULL,
    `due_date` datetime NOT NULL,
    `max_marks` int(11) DEFAULT 100,
    `attachments` text DEFAULT NULL,
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `homeworks_class_room_id_foreign` (`class_room_id`),
    KEY `homeworks_subject_id_foreign` (`subject_id`),
    KEY `homeworks_teacher_id_foreign` (`teacher_id`),
    CONSTRAINT `homeworks_class_room_id_foreign` FOREIGN KEY (`class_room_id`) REFERENCES `class_rooms` (`id`) ON DELETE CASCADE,
    CONSTRAINT `homeworks_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
    CONSTRAINT `homeworks_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exam schedules table
CREATE TABLE IF NOT EXISTS `exam_schedules` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `class_room_id` bigint(20) UNSIGNED NOT NULL,
    `subject_id` bigint(20) UNSIGNED NOT NULL,
    `exam_date` date NOT NULL,
    `start_time` time NOT NULL,
    `end_time` time NOT NULL,
    `venue` varchar(255) DEFAULT NULL,
    `max_marks` int(11) DEFAULT 100,
    `instructions` text DEFAULT NULL,
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `exam_schedules_class_room_id_foreign` (`class_room_id`),
    KEY `exam_schedules_subject_id_foreign` (`subject_id`),
    CONSTRAINT `exam_schedules_class_room_id_foreign` FOREIGN KEY (`class_room_id`) REFERENCES `class_rooms` (`id`) ON DELETE CASCADE,
    CONSTRAINT `exam_schedules_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Settings table
CREATE TABLE IF NOT EXISTS `settings` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `key` varchar(255) NOT NULL,
    `value` text DEFAULT NULL,
    `type` varchar(50) DEFAULT 'string',
    `description` text DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sessions table
CREATE TABLE IF NOT EXISTS `sessions` (
    `id` varchar(255) NOT NULL,
    `user_id` bigint(20) UNSIGNED DEFAULT NULL,
    `ip_address` varchar(45) DEFAULT NULL,
    `user_agent` text DEFAULT NULL,
    `payload` longtext NOT NULL,
    `last_activity` int(11) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `sessions_user_id_index` (`user_id`),
    KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Migrations table
CREATE TABLE IF NOT EXISTS `migrations` (
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `migration` varchar(255) NOT NULL,
    `batch` int(11) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Insert Initial Data
-- ========================================

-- Insert default school
INSERT INTO `schools` (`name`, `address`, `phone`, `email`, `motto`, `established_year`, `is_active`, `created_at`, `updated_at`) VALUES
('Liberia School Management System', 'Monrovia, Liberia', '+231-XXX-XXXX', 'info@school.com', 'Excellence in Education', 2024, 1, NOW(), NOW());

-- Insert default users
INSERT INTO `users` (`name`, `email`, `password`, `role`, `is_active`, `created_at`, `updated_at`) VALUES
('Admin User', 'admin@school.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, NOW(), NOW()),
('Teacher User', 'teacher@school.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', 1, NOW(), NOW()),
('Student User', 'student@school.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 1, NOW(), NOW()),
('Finance User', 'finance@school.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'finance', 1, NOW(), NOW());

-- Insert default class rooms
INSERT INTO `class_rooms` (`name`, `class_code`, `description`, `capacity`, `is_active`, `created_at`, `updated_at`) VALUES
('Grade 9A', 'G9A', 'Grade 9 Class A', 30, 1, NOW(), NOW()),
('Grade 9B', 'G9B', 'Grade 9 Class B', 30, 1, NOW(), NOW()),
('Grade 10A', 'G10A', 'Grade 10 Class A', 30, 1, NOW(), NOW()),
('Grade 10B', 'G10B', 'Grade 10 Class B', 30, 1, NOW(), NOW()),
('Grade 11A', 'G11A', 'Grade 11 Class A', 30, 1, NOW(), NOW()),
('Grade 11B', 'G11B', 'Grade 11 Class B', 30, 1, NOW(), NOW()),
('Grade 12A', 'G12A', 'Grade 12 Class A', 30, 1, NOW(), NOW()),
('Grade 12B', 'G12B', 'Grade 12 Class B', 30, 1, NOW(), NOW());

-- Insert default subjects
INSERT INTO `subjects` (`name`, `code`, `description`, `credits`, `is_active`, `created_at`, `updated_at`) VALUES
('Mathematics', 'MATH', 'Core Mathematics', 4, 1, NOW(), NOW()),
('English Language', 'ENG', 'English Language and Literature', 4, 1, NOW(), NOW()),
('Science', 'SCI', 'General Science', 3, 1, NOW(), NOW()),
('Social Studies', 'SOC', 'Social Studies and History', 3, 1, NOW(), NOW()),
('Physical Education', 'PE', 'Physical Education and Sports', 2, 1, NOW(), NOW()),
('Art', 'ART', 'Visual and Performing Arts', 2, 1, NOW(), NOW()),
('Computer Science', 'CS', 'Computer Science and Technology', 3, 1, NOW(), NOW()),
('French', 'FR', 'French Language', 3, 1, NOW(), NOW());

-- Insert default settings
INSERT INTO `settings` (`key`, `value`, `type`, `description`, `created_at`, `updated_at`) VALUES
('school_name', 'Liberia School Management System', 'string', 'Name of the school', NOW(), NOW()),
('school_address', 'Monrovia, Liberia', 'string', 'School address', NOW(), NOW()),
('school_phone', '+231-XXX-XXXX', 'string', 'School phone number', NOW(), NOW()),
('school_email', 'info@school.com', 'string', 'School email address', NOW(), NOW()),
('currency', 'LRD', 'string', 'Default currency', NOW(), NOW()),
('currency_symbol', 'L$', 'string', 'Currency symbol', NOW(), NOW()),
('academic_year', '2024', 'string', 'Current academic year', NOW(), NOW()),
('max_file_size', '10240', 'integer', 'Maximum file upload size in KB', NOW(), NOW()),
('allowed_file_types', 'jpg,jpeg,png,pdf,doc,docx,mp4,mp3', 'string', 'Allowed file types for upload', NOW(), NOW());

-- ========================================
-- Create Indexes for Performance
-- ========================================

-- Add indexes for better performance
CREATE INDEX idx_students_class_room_id ON students(class_room_id);
CREATE INDEX idx_students_is_active ON students(is_active);
CREATE INDEX idx_teachers_department ON teachers(department);
CREATE INDEX idx_teachers_is_active ON teachers(is_active);
CREATE INDEX idx_fee_payments_student_id ON fee_payments(student_id);
CREATE INDEX idx_fee_payments_status ON fee_payments(status);
CREATE INDEX idx_attendances_date ON student_attendances(date);
CREATE INDEX idx_attendances_status ON student_attendances(status);
CREATE INDEX idx_homeworks_due_date ON homeworks(due_date);
CREATE INDEX idx_exam_schedules_exam_date ON exam_schedules(exam_date);

-- ========================================
-- Database Setup Complete!
-- ========================================

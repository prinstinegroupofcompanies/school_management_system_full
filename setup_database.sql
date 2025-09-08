-- Liberia School Management System Database Setup
-- Run this script in MySQL to create the database and user

-- Create database
CREATE DATABASE IF NOT EXISTS school_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user for the application (optional - you can use root)
CREATE USER IF NOT EXISTS 'school_user'@'localhost' IDENTIFIED BY 'school_password123';
GRANT ALL PRIVILEGES ON school_management.* TO 'school_user'@'localhost';
FLUSH PRIVILEGES;

-- Use the database
USE school_management;

-- Show tables after migrations are run
SHOW TABLES;

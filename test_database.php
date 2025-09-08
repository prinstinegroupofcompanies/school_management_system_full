<?php
/**
 * Database Connection Test Script
 * Run this to test MySQL connection before running Laravel migrations
 */

echo "=== Database Connection Test ===\n\n";

// Test different MySQL configurations
$configs = [
    'XAMPP (no password)' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'username' => 'root',
        'password' => '',
        'database' => 'school_management'
    ],
    'MySQL Server (with password)' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'username' => 'root',
        'password' => 'password', // Change this to your MySQL password
        'database' => 'school_management'
    ]
];

foreach ($configs as $name => $config) {
    echo "Testing: $name\n";
    echo "Host: {$config['host']}:{$config['port']}\n";
    echo "Username: {$config['username']}\n";
    echo "Database: {$config['database']}\n";
    
    try {
        $dsn = "mysql:host={$config['host']};port={$config['port']};charset=utf8mb4";
        $pdo = new PDO($dsn, $config['username'], $config['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "✅ Connection successful!\n";
        
        // Try to create database
        try {
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$config['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            echo "✅ Database '{$config['database']}' created/verified!\n";
        } catch (PDOException $e) {
            echo "⚠️  Database creation failed: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
        break; // Stop on first successful connection
        
    } catch (PDOException $e) {
        echo "❌ Connection failed: " . $e->getMessage() . "\n\n";
    }
}

echo "=== Recommendations ===\n";
echo "1. Install XAMPP for easiest setup: https://www.apachefriends.org/\n";
echo "2. Start Apache and MySQL services in XAMPP\n";
echo "3. Access phpMyAdmin at: http://localhost/phpmyadmin\n";
echo "4. Create database 'school_management'\n";
echo "5. Update .env file with correct credentials\n";
echo "6. Run: php artisan migrate\n\n";

echo "=== Alternative: Use SQLite ===\n";
echo "If MySQL setup is complex, you can use SQLite:\n";
echo "1. Change DB_CONNECTION=sqlite in .env\n";
echo "2. Set DB_DATABASE=database/database.sqlite\n";
echo "3. Run: php artisan migrate\n";
?>

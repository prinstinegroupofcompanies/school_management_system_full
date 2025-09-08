<?php
/**
 * Test MySQL Password Script
 * This will help you find the correct MySQL password
 */

echo "=== MySQL Password Test ===\n\n";

// Common passwords to test
$passwords = [
    '',           // No password
    'password',   // Default password
    'root',       // Common password
    'admin',      // Common password
    '123456',     // Common password
    'mysql',      // Common password
    'root123',    // Common password
    'password123', // Common password
];

$host = '127.0.0.1';
$port = 3306;
$username = 'root';
$database = 'school_management';

echo "Testing connection to: $host:$port\n";
echo "Username: $username\n";
echo "Database: $database\n\n";

foreach ($passwords as $password) {
    echo "Testing password: " . ($password === '' ? '(empty)' : $password) . " ... ";
    
    try {
        $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "✅ SUCCESS!\n";
        echo "✅ Correct password found: " . ($password === '' ? '(empty)' : $password) . "\n";
        
        // Test database access
        try {
            $pdo->exec("USE `$database`");
            echo "✅ Database '$database' accessible!\n";
        } catch (PDOException $e) {
            echo "⚠️  Database '$database' not accessible: " . $e->getMessage() . "\n";
        }
        
        echo "\nTo update your .env file, run:\n";
        if ($password === '') {
            echo "powershell -Command \"(Get-Content .env) -replace 'DB_PASSWORD=.*', 'DB_PASSWORD=' | Set-Content .env\"\n";
        } else {
            echo "powershell -Command \"(Get-Content .env) -replace 'DB_PASSWORD=.*', 'DB_PASSWORD=$password' | Set-Content .env\"\n";
        }
        
        break;
        
    } catch (PDOException $e) {
        echo "❌ Failed\n";
    }
}

echo "\n=== Manual Password Entry ===\n";
echo "If none of the common passwords worked, please:\n";
echo "1. Remember what password you used when connecting to MySQL\n";
echo "2. Update the .env file manually with the correct password\n";
echo "3. Or run: .\\update_mysql_password.bat\n";
?>

<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Manual Migration Runner ===\n\n";

try {
    echo "1. Running migrations...\n";
    $exitCode = Artisan::call('migrate', ['--force' => true]);
    
    if ($exitCode === 0) {
        echo "✅ Migrations completed successfully\n\n";
        
        echo "2. Running seeders...\n";
        $exitCode = Artisan::call('db:seed', ['--force' => true]);
        
        if ($exitCode === 0) {
            echo "✅ Seeders completed successfully\n\n";
        } else {
            echo "❌ Seeders failed\n";
        }
        
    } else {
        echo "❌ Migrations failed\n";
    }
    
    echo "\n=== Process Complete ===\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

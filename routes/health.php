<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

// Health check endpoint for deployment verification
Route::get('/health', function () {
    try {
        // Check database connection
        DB::connection()->getPdo();
        
        return response()->json([
            'status' => 'healthy',
            'database' => 'connected',
            'timestamp' => now()->toISOString()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'unhealthy',
            'database' => 'disconnected',
            'error' => $e->getMessage(),
            'timestamp' => now()->toISOString()
        ], 500);
    }
});

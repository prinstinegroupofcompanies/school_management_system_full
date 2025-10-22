<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HealthIncident;
use App\Models\SafetyCheck;
use App\Models\HealthRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HealthSafetyDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function dashboard()
    {
        // Get statistics
        $stats = [
            'total_incidents' => HealthIncident::count(),
            'critical_incidents' => HealthIncident::where('severity', 'critical')->count(),
            'total_safety_checks' => SafetyCheck::count(),
            'total_health_records' => HealthRecord::count(),
            'passed_checks' => SafetyCheck::where('status', 'passed')->count(),
            'failed_checks' => SafetyCheck::where('status', 'failed')->count(),
            'needs_attention_checks' => SafetyCheck::where('status', 'needs_attention')->count(),
            'critical_checks' => SafetyCheck::where('status', 'critical')->count(),
        ];

        // Get recent incidents
        $recentIncidents = HealthIncident::with(['reportedBy'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.health-safety.dashboard', compact('stats', 'recentIncidents'));
    }
}

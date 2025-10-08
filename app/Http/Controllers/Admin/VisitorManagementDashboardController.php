<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use App\Models\VisitorLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VisitorManagementDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function dashboard()
    {
        // Get statistics
        $stats = [
            'total_visitors' => Visitor::count(),
            'currently_visiting' => VisitorLog::where('status', 'checked_in')->count(),
            'blacklisted_visitors' => Visitor::where('is_blacklisted', true)->count(),
            'todays_visits' => VisitorLog::whereDate('check_in_time', Carbon::today())->count(),
        ];

        // Get recent visits
        $recentVisits = VisitorLog::with(['visitor', 'host'])
            ->orderBy('check_in_time', 'desc')
            ->limit(5)
            ->get();

        // Get visitor trends for the last 7 days
        $visitorTrends = VisitorLog::select(
                DB::raw('DATE(check_in_time) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('check_in_time', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.visitor-management.dashboard', compact('stats', 'recentVisits', 'visitorTrends'));
    }
}

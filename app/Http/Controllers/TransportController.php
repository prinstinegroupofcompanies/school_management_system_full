<?php

namespace App\Http\Controllers;

use App\Models\Transport;
use App\Models\TransportRoute;
use App\Models\Driver;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransportController extends Controller
{
    public function index()
    {
        try {
            // Get transport statistics
            $stats = [
                'total_vehicles' => Transport::count(),
                'active_vehicles' => Transport::where('status', 'active')->count(),
                'total_routes' => TransportRoute::count(),
                'active_routes' => TransportRoute::where('is_active', true)->count(),
                'total_drivers' => Driver::count(),
                'active_drivers' => Driver::where('status', 'active')->count(),
                'total_students' => Student::whereHas('transportRoute')->count(),
                'total_capacity' => Transport::sum('capacity'),
                'utilized_capacity' => Student::whereHas('transportRoute')->count(),
            ];

            // Get recent transport activities
            $recentRoutes = TransportRoute::with('transport')
                ->latest()
                ->limit(10)
                ->get();

            // Get vehicles by status
            $vehiclesByStatus = Transport::selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status');

            // Get routes by status
            $routesByStatus = TransportRoute::selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status');

            // Get popular routes (most students)
            $popularRoutes = TransportRoute::withCount('students')
                ->orderBy('students_count', 'desc')
                ->limit(5)
                ->get();

            // Get maintenance due vehicles
            $maintenanceDue = Transport::where('next_maintenance_date', '<=', now()->addDays(30))
                ->where('status', '!=', 'maintenance')
                ->limit(10)
                ->get();

            return view('transport.index', compact(
                'stats',
                'recentRoutes',
                'vehiclesByStatus',
                'routesByStatus',
                'popularRoutes',
                'maintenanceDue'
            ));
        } catch (\Exception $e) {
            \Log::error('TransportController index error: ' . $e->getMessage());
            
            // Fallback data if database issues
            $stats = [
                'total_vehicles' => 0,
                'active_vehicles' => 0,
                'total_routes' => 0,
                'active_routes' => 0,
                'total_drivers' => 0,
                'active_drivers' => 0,
                'total_students' => 0,
                'total_capacity' => 0,
                'utilized_capacity' => 0,
            ];
            
            $recentRoutes = collect();
            $vehiclesByStatus = collect();
            $routesByStatus = collect();
            $popularRoutes = collect();
            $maintenanceDue = collect();
            
            return view('transport.index', compact(
                'stats',
                'recentRoutes',
                'vehiclesByStatus',
                'routesByStatus',
                'popularRoutes',
                'maintenanceDue'
            ));
        }
    }

    public function routes(Request $request)
    {
        try {
            $query = TransportRoute::with(['transport', 'students']);

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('route_name', 'like', "%{$search}%")
                      ->orWhere('route_code', 'like', "%{$search}%")
                      ->orWhere('start_location', 'like', "%{$search}%")
                      ->orWhere('end_location', 'like', "%{$search}%");
                });
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $routes = $query->orderBy('route_name')->paginate(20);
            $transports = Transport::where('status', 'active')->get();

            return view('transport.routes', compact('routes', 'transports'));
        } catch (\Exception $e) {
            \Log::error('TransportController routes error: ' . $e->getMessage());
            $routes = collect()->paginate(20);
            $transports = collect();
            return view('transport.routes', compact('routes', 'transports'));
        }
    }

    public function vehicles(Request $request)
    {
        try {
            $query = Transport::with(['routes', 'driver']);

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('vehicle_number', 'like', "%{$search}%")
                      ->orWhere('driver_name', 'like', "%{$search}%");
                });
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $vehicles = $query->orderBy('name')->paginate(20);

            // Get vehicle statistics
            $vehicleStats = [
                'total_vehicles' => Transport::count(),
                'active_vehicles' => Transport::where('status', 'active')->count(),
                'maintenance_vehicles' => Transport::where('status', 'maintenance')->count(),
                'total_capacity' => Transport::sum('capacity'),
            ];

            return view('transport.vehicles', compact('vehicles', 'vehicleStats'));
        } catch (\Exception $e) {
            \Log::error('TransportController vehicles error: ' . $e->getMessage());
            $vehicles = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
            $vehicleStats = [
                'total_vehicles' => 0,
                'active_vehicles' => 0,
                'maintenance_vehicles' => 0,
                'total_capacity' => 0,
            ];
            return view('transport.vehicles', compact('vehicles', 'vehicleStats'));
        }
    }

    public function schedule(Request $request)
    {
        try {
            $query = TransportRoute::with(['transport', 'students']);

            // Filter by route
            if ($request->filled('route_id')) {
                $query->where('id', $request->route_id);
            }

            $routes = $query->where('is_active', true)->orderBy('morning_pickup_time')->get();
            $allRoutes = TransportRoute::where('is_active', true)->get();

            // Get schedule statistics
            $scheduleStats = [
                'total_routes' => $routes->count(),
                'total_students' => $routes->sum('students_count'),
            ];

            return view('transport.schedule', compact('routes', 'allRoutes', 'scheduleStats'));
        } catch (\Exception $e) {
            \Log::error('TransportController schedule error: ' . $e->getMessage());
            $routes = collect();
            $allRoutes = collect();
            $scheduleStats = [
                'total_routes' => 0,
                'total_students' => 0,
            ];
            return view('transport.schedule', compact('routes', 'allRoutes', 'scheduleStats'));
        }
    }
}
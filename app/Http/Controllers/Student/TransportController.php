<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\TransportVehicle;
use App\Models\TransportRoute;
use App\Models\TransportStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransportController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            $student = $user->student;
            
            if (!$student) {
                return redirect()->route('student.dashboard')
                    ->with('error', 'Student profile not found.');
            }

            // Get transport statistics
            $stats = [
                'total_vehicles' => TransportVehicle::where('status', 'active')->count(),
                'active_routes' => TransportRoute::where('status', 'active')->count(),
                'total_students' => TransportStudent::where('status', 'active')->count(),
            ];

            // Get student's transport assignment
            $myAssignment = TransportStudent::with(['vehicle', 'route'])
                ->where('student_id', $student->id)
                ->where('status', 'active')
                ->first();

            // Get all available routes
            $availableRoutes = TransportRoute::with(['vehicle'])
                ->where('status', 'active')
                ->orderBy('name')
                ->get();

            // Get recent transport activities
            $recentActivities = collect([
                [
                    'type' => 'assignment',
                    'description' => $myAssignment ? 'Assigned to ' . $myAssignment->route->name : 'No transport assignment',
                    'date' => $myAssignment ? $myAssignment->created_at : now(),
                ],
                [
                    'type' => 'route_update',
                    'description' => 'Transport routes updated',
                    'date' => now()->subDays(2),
                ],
            ]);

            return view('student.transport.index', compact(
                'stats', 
                'myAssignment', 
                'availableRoutes', 
                'recentActivities'
            ));
        } catch (\Exception $e) {
            \Log::error('Student TransportController index error: ' . $e->getMessage());
            
            // Fallback data
            $stats = [
                'total_vehicles' => 0,
                'active_routes' => 0,
                'total_students' => 0,
            ];
            $myAssignment = null;
            $availableRoutes = collect();
            $recentActivities = collect();
            
            return view('student.transport.index', compact(
                'stats', 
                'myAssignment', 
                'availableRoutes', 
                'recentActivities'
            ));
        }
    }

    public function routes()
    {
        try {
            $routes = TransportRoute::with(['vehicle'])
                ->where('status', 'active')
                ->orderBy('name')
                ->paginate(12);

            $user = Auth::user();
            $student = $user->student;
            $myAssignment = null;
            
            if ($student) {
                $myAssignment = TransportStudent::with(['vehicle', 'route'])
                    ->where('student_id', $student->id)
                    ->where('status', 'active')
                    ->first();
            }

            return view('student.transport.routes', compact('routes', 'myAssignment'));
        } catch (\Exception $e) {
            \Log::error('Student TransportController routes error: ' . $e->getMessage());
            
            $routes = new \Illuminate\Pagination\LengthAwarePaginator(
                collect(), 0, 12, 1, ['path' => request()->url()]
            );
            $myAssignment = null;
            
            return view('student.transport.routes', compact('routes', 'myAssignment'));
        }
    }

    public function vehicles()
    {
        try {
            $vehicles = TransportVehicle::with(['routes'])
                ->where('status', 'active')
                ->orderBy('vehicle_number')
                ->get();

            return view('student.transport.vehicles', compact('vehicles'));
        } catch (\Exception $e) {
            \Log::error('Student TransportController vehicles error: ' . $e->getMessage());
            
            $vehicles = collect();
            
            return view('student.transport.vehicles', compact('vehicles'));
        }
    }

    public function schedule()
    {
        try {
            $user = Auth::user();
            $student = $user->student;
            
            if (!$student) {
                return redirect()->route('student.dashboard')
                    ->with('error', 'Student profile not found.');
            }

            $myAssignment = TransportStudent::with(['vehicle', 'route'])
                ->where('student_id', $student->id)
                ->where('status', 'active')
                ->first();

            if (!$myAssignment) {
                return redirect()->route('student.transport.index')
                    ->with('error', 'No transport assignment found.');
            }

            // Get schedule for the assigned route
            $schedule = [
                'morning_pickup' => $myAssignment->route->morning_pickup_time ?? '07:00 AM',
                'evening_dropoff' => $myAssignment->route->evening_dropoff_time ?? '04:00 PM',
                'pickup_location' => $myAssignment->route->pickup_location ?? 'School Gate',
                'dropoff_location' => $myAssignment->route->dropoff_location ?? 'School Gate',
            ];

            return view('student.transport.schedule', compact('myAssignment', 'schedule'));
        } catch (\Exception $e) {
            \Log::error('Student TransportController schedule error: ' . $e->getMessage());
            
            return redirect()->route('student.transport.index')
                ->with('error', 'Unable to load schedule.');
        }
    }
}
<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;

class TransportController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $student = $user->student;
        
        if (!$student) {
            abort(403, 'Student record not found');
        }

        // Mock transport data for students
        $routes = [
            [
                'id' => 1,
                'name' => 'Route A - Downtown',
                'pickup_time' => '07:30 AM',
                'dropoff_time' => '03:30 PM',
                'driver' => 'John Smith',
                'vehicle' => 'Bus #001',
                'capacity' => 50,
                'current_passengers' => 35,
                'status' => 'active',
            ],
            [
                'id' => 2,
                'name' => 'Route B - Suburbs',
                'pickup_time' => '07:45 AM',
                'dropoff_time' => '03:45 PM',
                'driver' => 'Jane Doe',
                'vehicle' => 'Bus #002',
                'capacity' => 45,
                'current_passengers' => 42,
                'status' => 'active',
            ],
            [
                'id' => 3,
                'name' => 'Route C - East Side',
                'pickup_time' => '08:00 AM',
                'dropoff_time' => '04:00 PM',
                'driver' => 'Mike Johnson',
                'vehicle' => 'Bus #003',
                'capacity' => 40,
                'current_passengers' => 28,
                'status' => 'active',
            ],
        ];

        $myRoute = [
            'id' => 1,
            'name' => 'Route A - Downtown',
            'pickup_location' => '123 Main Street',
            'pickup_time' => '07:30 AM',
            'dropoff_time' => '03:30 PM',
            'driver' => 'John Smith',
            'driver_phone' => '+1234567890',
            'vehicle' => 'Bus #001',
            'status' => 'active',
        ];

        $transportStats = [
            'total_routes' => 8,
            'active_routes' => 6,
            'total_vehicles' => 12,
            'total_students' => 450,
        ];

        return view('student.transport.index', compact('routes', 'myRoute', 'transportStats'));
    }

    public function routes()
    {
        $user = auth()->user();
        $student = $user->student;
        
        if (!$student) {
            abort(403, 'Student record not found');
        }

        // Mock all available routes
        $routes = [
            [
                'id' => 1,
                'name' => 'Route A - Downtown',
                'pickup_time' => '07:30 AM',
                'dropoff_time' => '03:30 PM',
                'driver' => 'John Smith',
                'vehicle' => 'Bus #001',
                'capacity' => 50,
                'current_passengers' => 35,
                'status' => 'active',
                'pickup_locations' => ['123 Main Street', '456 Oak Avenue', '789 Pine Road'],
            ],
            [
                'id' => 2,
                'name' => 'Route B - Suburbs',
                'pickup_time' => '07:45 AM',
                'dropoff_time' => '03:45 PM',
                'driver' => 'Jane Doe',
                'vehicle' => 'Bus #002',
                'capacity' => 45,
                'current_passengers' => 42,
                'status' => 'active',
                'pickup_locations' => ['321 Elm Street', '654 Maple Drive', '987 Cedar Lane'],
            ],
        ];

        return view('student.transport.routes', compact('routes'));
    }

    public function request(Request $request)
    {
        $user = auth()->user();
        $student = $user->student;
        
        if (!$student) {
            abort(403, 'Student record not found');
        }

        $request->validate([
            'route_id' => 'required|integer',
            'pickup_location' => 'required|string|max:255',
        ]);

        // Mock transport request logic
        return redirect()->route('student.transport.index')
            ->with('success', 'Transport request submitted successfully! You will be notified once approved.');
    }
}

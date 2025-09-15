<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Transport;
use App\Models\TransportRoute;

class TransportController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $student = $user->student;
        
        if (!$student) {
            abort(403, 'Student record not found');
        }

        // Get real transport data
        $routes = TransportRoute::where('status', 'active')
            ->where('is_active', true)
            ->latest()
            ->take(10)
            ->get();

        // Get student's assigned route if they have one
        $myRoute = null;
        if ($student->transport_route_id) {
            $myRoute = TransportRoute::find($student->transport_route_id);
        }

        // Calculate real transport statistics
        $totalRoutes = TransportRoute::count();
        $activeRoutes = TransportRoute::where('status', 'active')->where('is_active', true)->count();
        $totalVehicles = Transport::where('status', 'active')->count();
        $totalStudents = Student::whereNotNull('transport_route_id')->count();

        $transportStats = [
            'total_routes' => $totalRoutes,
            'active_routes' => $activeRoutes,
            'total_vehicles' => $totalVehicles,
            'total_students' => $totalStudents,
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

        // Get real available routes
        $routes = TransportRoute::where('status', 'active')
            ->where('is_active', true)
            ->paginate(20);

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

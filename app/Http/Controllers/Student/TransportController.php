<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransportController extends Controller
{
    public function index()
    {
        try {
            $user = auth()->user();
            $student = $user->student;
        } catch (\Exception $e) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not available. Please contact administrator.');
        }
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student record not found. Please contact administrator.');
        }

        // Get real-time transport statistics
        $transportStats = [
            'total_routes' => \App\Models\TransportRoute::count(),
            'active_routes' => \App\Models\TransportRoute::where('status', 'active')->count(),
            'total_vehicles' => \App\Models\Transport::where('status', 'active')->count(),
            'total_students' => \App\Models\Student::whereNotNull('transport_route_id')->count(),
        ];

        // Get student's transport route
        $myRoute = null;
        if ($student->transport_route_id) {
            $myRoute = \App\Models\TransportRoute::with('transport')->find($student->transport_route_id);
        }

        // Get all available routes
        $availableRoutes = \App\Models\TransportRoute::with('transport')
            ->where('status', 'active')
            ->get();

        return view('student.transport.index', compact('transportStats', 'myRoute', 'availableRoutes'));
    }

    public function schedule()
    {
        return view('student.transport.schedule');
    }

    public function routes()
    {
        return view('student.transport.routes');
    }

    public function request(Request $request)
    {
        // Placeholder for transport request
        return redirect()->route('student.transport.index')->with('success', 'Transport request submitted successfully');
    }
}
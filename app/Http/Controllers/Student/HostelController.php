<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HostelController extends Controller
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

        // Get real-time hostel statistics
        $hostelStats = [
            'total_hostels' => \App\Models\Hostel::where('status', 'active')->count(),
            'total_rooms' => \App\Models\HostelRoom::where('is_active', true)->count(),
            'total_capacity' => \App\Models\HostelRoom::where('is_active', true)->sum('capacity'),
            'current_occupancy' => \App\Models\HostelRoom::where('is_active', true)->sum('current_occupancy'),
        ];

        // Get student's hostel room
        $myRoom = null;
        if ($student->hostel_room_id) {
            $myRoom = \App\Models\HostelRoom::with('hostel')->find($student->hostel_room_id);
        }

        // Get available hostels
        $availableHostels = \App\Models\Hostel::where('status', 'active')
            ->with(['rooms' => function($query) {
                $query->where('is_active', true)->where('current_occupancy', '<', \DB::raw('capacity'));
            }])
            ->get();

        return view('student.hostel.index', compact('hostelStats', 'myRoom', 'availableHostels'));
    }

    public function rooms()
    {
        return view('student.hostel.rooms');
    }

    public function payments()
    {
        return view('student.hostel.payments');
    }

    public function myRoom()
    {
        return view('student.hostel.room');
    }

    public function facilities()
    {
        return view('student.hostel.facilities');
    }

    public function request(Request $request)
    {
        // Placeholder for hostel request
        return redirect()->route('student.hostel.index')->with('success', 'Hostel request submitted successfully');
    }
}
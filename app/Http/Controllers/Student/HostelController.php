<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Hostel;
use App\Models\HostelRoom;
use App\Models\HostelPayment;

class HostelController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $student = $user->student;
        
        if (!$student) {
            abort(403, 'Student record not found');
        }

        // Get real hostel data
        $hostels = Hostel::where('status', 'active')
            ->with(['rooms' => function($query) {
                $query->where('is_active', true);
            }])
            ->get();

        // Get student's current room if they have one
        $myRoom = null;
        if ($student->hostel_room_id) {
            $myRoom = HostelRoom::with('hostel')
                ->find($student->hostel_room_id);
        }

        // Calculate real hostel statistics
        $totalRooms = HostelRoom::where('is_active', true)->count();
        $totalCapacity = HostelRoom::where('is_active', true)->sum('capacity');
        $currentOccupancy = HostelRoom::where('is_active', true)->sum('current_occupancy');
        
        $hostelStats = [
            'total_hostels' => $hostels->count(),
            'total_rooms' => $totalRooms,
            'total_capacity' => $totalCapacity,
            'current_occupancy' => $currentOccupancy,
        ];

        return view('student.hostel.index', compact('hostels', 'myRoom', 'hostelStats'));
    }

    public function rooms()
    {
        $user = auth()->user();
        $student = $user->student;
        
        if (!$student) {
            abort(403, 'Student record not found');
        }

        // Get real available rooms
        $rooms = HostelRoom::with('hostel')
            ->where('is_active', true)
            ->where('status', 'available')
            ->whereRaw('current_occupancy < capacity')
            ->get();

        // Get all hostels for filter dropdown
        $hostels = Hostel::where('status', 'active')->get();

        return view('student.hostel.rooms', compact('rooms', 'hostels'));
    }

    public function request(Request $request)
    {
        $user = auth()->user();
        $student = $user->student;
        
        if (!$student) {
            abort(403, 'Student record not found');
        }

        $request->validate([
            'hostel_id' => 'required|integer',
            'room_id' => 'required|integer',
            'preferred_roommate' => 'nullable|string|max:255',
        ]);

        // Mock hostel request logic
        return redirect()->route('student.hostel.index')
            ->with('success', 'Hostel request submitted successfully! You will be notified once approved.');
    }

    public function payments()
    {
        $user = auth()->user();
        $student = $user->student;
        
        if (!$student) {
            abort(403, 'Student record not found');
        }

        // Get real payment history
        $payments = HostelPayment::where('student_id', $student->id)
            ->orderBy('due_date', 'desc')
            ->get();

        // Calculate payment statistics
        $totalPaid = $payments->where('status', 'paid')->sum('amount');
        $outstanding = $payments->where('status', 'pending')->sum('amount');
        $nextDueDate = $payments->where('status', 'pending')->min('due_date');

        return view('student.hostel.payments', compact('payments', 'totalPaid', 'outstanding', 'nextDueDate'));
    }
}

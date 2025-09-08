<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;

class HostelController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $student = $user->student;
        
        if (!$student) {
            abort(403, 'Student record not found');
        }

        // Mock hostel data for students
        $hostels = [
            [
                'id' => 1,
                'name' => 'Liberty Hall',
                'type' => 'Boys Hostel',
                'capacity' => 200,
                'current_occupancy' => 180,
                'facilities' => ['WiFi', 'Laundry', 'Common Room', 'Study Hall'],
                'monthly_fee' => 150.00,
                'status' => 'available',
            ],
            [
                'id' => 2,
                'name' => 'Freedom Hall',
                'type' => 'Girls Hostel',
                'capacity' => 150,
                'current_occupancy' => 120,
                'facilities' => ['WiFi', 'Laundry', 'Common Room', 'Study Hall', 'Gym'],
                'monthly_fee' => 175.00,
                'status' => 'available',
            ],
        ];

        $myRoom = [
            'id' => 101,
            'hostel' => 'Liberty Hall',
            'room_number' => '101',
            'room_type' => 'Double Occupancy',
            'roommate' => 'John Doe',
            'monthly_fee' => 150.00,
            'next_payment_due' => '2024-10-01',
            'status' => 'occupied',
        ];

        $hostelStats = [
            'total_hostels' => 4,
            'total_rooms' => 200,
            'total_capacity' => 400,
            'current_occupancy' => 320,
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

        // Mock available rooms
        $rooms = [
            [
                'id' => 101,
                'hostel' => 'Liberty Hall',
                'room_number' => '101',
                'room_type' => 'Double Occupancy',
                'capacity' => 2,
                'current_occupancy' => 1,
                'monthly_fee' => 150.00,
                'facilities' => ['Bed', 'Study Table', 'Wardrobe', 'WiFi'],
                'status' => 'available',
            ],
            [
                'id' => 102,
                'hostel' => 'Liberty Hall',
                'room_number' => '102',
                'room_type' => 'Single Occupancy',
                'capacity' => 1,
                'current_occupancy' => 0,
                'monthly_fee' => 200.00,
                'facilities' => ['Bed', 'Study Table', 'Wardrobe', 'WiFi', 'Private Bathroom'],
                'status' => 'available',
            ],
        ];

        return view('student.hostel.rooms', compact('rooms'));
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

        // Mock payment history
        $payments = [
            [
                'id' => 1,
                'month' => 'September 2024',
                'amount' => 150.00,
                'due_date' => '2024-09-01',
                'paid_date' => '2024-08-28',
                'status' => 'paid',
                'method' => 'Bank Transfer',
            ],
            [
                'id' => 2,
                'month' => 'October 2024',
                'amount' => 150.00,
                'due_date' => '2024-10-01',
                'paid_date' => null,
                'status' => 'pending',
                'method' => null,
            ],
        ];

        return view('student.hostel.payments', compact('payments'));
    }
}

<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\HostelRoom;
use App\Models\HostelStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HostelController extends Controller
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

            // Get hostel statistics
            $stats = [
                'total_rooms' => HostelRoom::count(),
                'available_rooms' => HostelRoom::where('status', 'available')->count(),
                'occupied_rooms' => HostelRoom::where('status', 'occupied')->count(),
                'total_students' => HostelStudent::where('status', 'active')->count(),
            ];

            // Get student's hostel assignment
            $myAssignment = HostelStudent::with(['room'])
                ->where('student_id', $student->id)
                ->where('status', 'active')
                ->first();

            // Get available rooms
            $availableRooms = HostelRoom::where('status', 'available')
                ->orderBy('room_number')
                ->limit(10)
                ->get();

            // Get recent hostel activities
            $recentActivities = collect([
                [
                    'type' => 'assignment',
                    'description' => $myAssignment ? 'Assigned to Room ' . $myAssignment->room->room_number : 'No hostel assignment',
                    'date' => $myAssignment ? $myAssignment->created_at : now(),
                ],
                [
                    'type' => 'room_update',
                    'description' => 'Hostel rooms updated',
                    'date' => now()->subDays(1),
                ],
            ]);

            return view('student.hostel.index', compact(
                'stats', 
                'myAssignment', 
                'availableRooms', 
                'recentActivities'
            ));
        } catch (\Exception $e) {
            \Log::error('Student HostelController index error: ' . $e->getMessage());
            
            // Fallback data
            $stats = [
                'total_rooms' => 0,
                'available_rooms' => 0,
                'occupied_rooms' => 0,
                'total_students' => 0,
            ];
            $myAssignment = null;
            $availableRooms = collect();
            $recentActivities = collect();
            
            return view('student.hostel.index', compact(
                'stats', 
                'myAssignment', 
                'availableRooms', 
                'recentActivities'
            ));
        }
    }

    public function rooms()
    {
        try {
            $rooms = HostelRoom::orderBy('room_number')->get();

            $user = Auth::user();
            $student = $user->student;
            $myAssignment = null;
            
            if ($student) {
                $myAssignment = HostelStudent::with(['room'])
                    ->where('student_id', $student->id)
                    ->where('status', 'active')
                    ->first();
            }

            return view('student.hostel.rooms', compact('rooms', 'myAssignment'));
        } catch (\Exception $e) {
            \Log::error('Student HostelController rooms error: ' . $e->getMessage());
            
            $rooms = collect();
            $myAssignment = null;
            
            return view('student.hostel.rooms', compact('rooms', 'myAssignment'));
        }
    }

    public function show(HostelRoom $room)
    {
        try {
            $room->load(['students.user']);
            
            $user = Auth::user();
            $student = $user->student;
            $isAssigned = false;
            
            if ($student) {
                $assignment = HostelStudent::where('room_id', $room->id)
                    ->where('student_id', $student->id)
                    ->where('status', 'active')
                    ->first();
                $isAssigned = $assignment ? true : false;
            }

            return view('student.hostel.show', compact('room', 'isAssigned'));
        } catch (\Exception $e) {
            \Log::error('Student HostelController show error: ' . $e->getMessage());
            return redirect()->route('student.hostel.index')
                ->with('error', 'Room not found.');
        }
    }

    public function myRoom()
    {
        try {
            $user = Auth::user();
            $student = $user->student;
            
            if (!$student) {
                return redirect()->route('student.dashboard')
                    ->with('error', 'Student profile not found.');
            }

            $myAssignment = HostelStudent::with(['room'])
                ->where('student_id', $student->id)
                ->where('status', 'active')
                ->first();

            if (!$myAssignment) {
                return redirect()->route('student.hostel.index')
                    ->with('error', 'No hostel assignment found.');
            }

            // Get roommates
            $roommates = HostelStudent::with(['user'])
                ->where('room_id', $myAssignment->room_id)
                ->where('student_id', '!=', $student->id)
                ->where('status', 'active')
                ->get();

            return view('student.hostel.my-room', compact('myAssignment', 'roommates'));
        } catch (\Exception $e) {
            \Log::error('Student HostelController myRoom error: ' . $e->getMessage());
            
            return redirect()->route('student.hostel.index')
                ->with('error', 'Unable to load room details.');
        }
    }
}
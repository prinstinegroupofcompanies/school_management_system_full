<?php

namespace App\Http\Controllers;

use App\Models\Hostel;
use App\Models\HostelRoom;
use App\Models\HostelPayment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HostelController extends Controller
{
    public function index()
    {
        try {
            // Get hostel statistics
            $stats = [
                'total_hostels' => Hostel::count(),
                'active_hostels' => Hostel::where('status', 'active')->count(),
                'total_rooms' => HostelRoom::count(),
                'available_rooms' => HostelRoom::where('status', 'available')->count(),
                'occupied_rooms' => HostelRoom::where('status', 'occupied')->count(),
                'total_students' => Student::whereHas('hostelRoom')->count(),
                'total_capacity' => Hostel::sum('capacity'),
                'utilized_capacity' => Student::whereHas('hostelRoom')->count(),
                'total_revenue' => HostelPayment::where('status', 'paid')->sum('amount'),
                'pending_payments' => HostelPayment::where('status', 'pending')->sum('amount'),
            ];

            // Get recent hostel activities
            $recentPayments = HostelPayment::with(['student.user', 'hostelRoom.hostel'])
                ->latest()
                ->limit(10)
                ->get();

            // Get hostels by status
            $hostelsByStatus = Hostel::selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status');

            // Get rooms by status
            $roomsByStatus = HostelRoom::selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status');

            // Get popular hostels (most students)
            $popularHostels = Hostel::withCount('rooms')
                ->orderBy('rooms_count', 'desc')
                ->limit(5)
                ->get();

            // Get maintenance due rooms
            $maintenanceDue = HostelRoom::where('status', 'maintenance')
                ->limit(10)
                ->get();

            return view('hostel.index', compact(
                'stats',
                'recentPayments',
                'hostelsByStatus',
                'roomsByStatus',
                'popularHostels',
                'maintenanceDue'
            ));
        } catch (\Exception $e) {
            \Log::error('HostelController index error: ' . $e->getMessage());
            
            // Fallback data if database issues
            $stats = [
                'total_hostels' => 0,
                'active_hostels' => 0,
                'total_rooms' => 0,
                'available_rooms' => 0,
                'occupied_rooms' => 0,
                'total_students' => 0,
                'total_capacity' => 0,
                'utilized_capacity' => 0,
                'total_revenue' => 0,
                'pending_payments' => 0,
            ];
            
            $recentPayments = collect();
            $hostelsByStatus = collect();
            $roomsByStatus = collect();
            $popularHostels = collect();
            $maintenanceDue = collect();
            
            return view('hostel.index', compact(
                'stats',
                'recentPayments',
                'hostelsByStatus',
                'roomsByStatus',
                'popularHostels',
                'maintenanceDue'
            ));
        }
    }

    public function createHostel()
    {
        try {
            return view('hostel.create');
        } catch (\Exception $e) {
            \Log::error('HostelController createHostel error: ' . $e->getMessage());
            return view('hostel.create');
        }
    }

    public function storeHostel(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'nullable|string',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'warden_name' => 'nullable|string|max:255',
                'warden_phone' => 'nullable|string|max:20',
                'capacity' => 'required|integer|min:1',
                'description' => 'nullable|string',
                'status' => 'required|string|in:active,inactive,maintenance',
            ]);

            Hostel::create($request->all());

            return redirect()->route('hostel.index')->with('success', 'Hostel created successfully');
        } catch (\Exception $e) {
            \Log::error('HostelController storeHostel error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create hostel. Please try again.');
        }
    }

    public function rooms(Request $request)
    {
        try {
            $query = HostelRoom::with(['hostel']);

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('room_number', 'like', "%{$search}%")
                      ->orWhere('room_name', 'like', "%{$search}%")
                      ->orWhere('building', 'like', "%{$search}%")
                      ->orWhereHas('hostel', function($hostelQuery) use ($search) {
                          $hostelQuery->where('name', 'like', "%{$search}%");
                      });
                });
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by hostel
            if ($request->filled('hostel_id')) {
                $query->where('hostel_id', $request->hostel_id);
            }

            $rooms = $query->orderBy('room_number')->paginate(20);
            $hostels = Hostel::where('status', 'active')->get();

            // Get room statistics
            $roomStats = [
                'total_rooms' => HostelRoom::count(),
                'available_rooms' => HostelRoom::where('status', 'available')->count(),
                'occupied_rooms' => HostelRoom::where('status', 'occupied')->count(),
                'maintenance_rooms' => HostelRoom::where('status', 'maintenance')->count(),
                'total_capacity' => HostelRoom::sum('capacity'),
            ];

            return view('hostel.rooms.index', compact('rooms', 'hostels', 'roomStats'));
        } catch (\Exception $e) {
            \Log::error('HostelController rooms error: ' . $e->getMessage());
            $rooms = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
            $hostels = collect();
            $roomStats = [
                'total_rooms' => 0,
                'available_rooms' => 0,
                'occupied_rooms' => 0,
                'maintenance_rooms' => 0,
                'total_capacity' => 0,
            ];
            return view('hostel.rooms.index', compact('rooms', 'hostels', 'roomStats'));
        }
    }

    public function students(Request $request)
    {
        try {
            $query = Student::with(['user', 'hostelRoom.hostel', 'classRoom']);

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Filter by hostel
            if ($request->filled('hostel_id')) {
                $query->whereHas('hostelRoom', function($q) use ($request) {
                    $q->where('hostel_id', $request->hostel_id);
                });
            }

            // Filter by room
            if ($request->filled('room_id')) {
                $query->where('hostel_room_id', $request->room_id);
            }

            $students = $query->orderBy('created_at', 'desc')->paginate(20);
            $hostels = Hostel::where('status', 'active')->get();
            $rooms = HostelRoom::where('status', 'available')->get();

            // Get student statistics
            $studentStats = [
                'total_students' => Student::count(),
                'students_in_hostel' => Student::whereHas('hostelRoom')->count(),
                'students_without_hostel' => Student::whereDoesntHave('hostelRoom')->count(),
            ];

            return view('hostel.students.index', compact('students', 'hostels', 'rooms', 'studentStats'));
        } catch (\Exception $e) {
            \Log::error('HostelController students error: ' . $e->getMessage());
            $students = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
            $hostels = collect();
            $rooms = collect();
            $studentStats = [
                'total_students' => 0,
                'students_in_hostel' => 0,
                'students_without_hostel' => 0,
            ];
            return view('hostel.students.index', compact('students', 'hostels', 'rooms', 'studentStats'));
        }
    }

    public function facilities(Request $request)
    {
        try {
            $query = Hostel::with(['rooms']);

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where('name', 'like', "%{$search}%");
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $hostels = $query->orderBy('name')->paginate(20);

            // Get facility statistics
            $facilityStats = [
                'total_hostels' => Hostel::count(),
                'active_hostels' => Hostel::where('status', 'active')->count(),
                'total_rooms' => HostelRoom::count(),
                'total_capacity' => Hostel::sum('capacity'),
            ];

            return view('hostel.facilities', compact('hostels', 'facilityStats'));
        } catch (\Exception $e) {
            \Log::error('HostelController facilities error: ' . $e->getMessage());
            $hostels = collect()->paginate(20);
            $facilityStats = [
                'total_hostels' => 0,
                'active_hostels' => 0,
                'total_rooms' => 0,
                'total_capacity' => 0,
            ];
            return view('hostel.facilities', compact('hostels', 'facilityStats'));
        }
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\HostelRoom;
use App\Models\RoomType;
use App\Models\Student;
use App\Models\Hostel;
use App\Models\HostelAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HostelController extends Controller
{
    public function index()
    {
        $hostels = Hostel::withCount(['rooms', 'students'])->get();
        $rooms = HostelRoom::with(['hostel', 'roomType', 'students'])->paginate(15);
        return view('hostel.index', compact('hostels', 'rooms'));
    }

    public function hostels()
    {
        $hostels = Hostel::withCount(['rooms', 'students'])->get();
        return view('hostel.hostels', compact('hostels'));
    }

    public function createHostel()
    {
        return view('hostel.create-hostel');
    }

    public function storeHostel(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'warden_name' => 'required|string|max:255',
            'warden_phone' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Hostel::create($request->all());

        return redirect()->route('hostel.hostels')
            ->with('success', 'Hostel created successfully.');
    }

    public function showHostel(Hostel $hostel)
    {
        $hostel->load(['rooms.roomType', 'rooms.students', 'students']);
        return view('hostel.show-hostel', compact('hostel'));
    }

    public function editHostel(Hostel $hostel)
    {
        return view('hostel.edit-hostel', compact('hostel'));
    }

    public function updateHostel(Request $request, Hostel $hostel)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'warden_name' => 'required|string|max:255',
            'warden_phone' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $hostel->update($request->all());

        return redirect()->route('hostel.hostels')
            ->with('success', 'Hostel updated successfully.');
    }

    public function destroyHostel(Hostel $hostel)
    {
        if ($hostel->students()->count() > 0) {
            return redirect()->route('hostel.hostels')
                ->with('error', 'Cannot delete hostel with allocated students.');
        }

        $hostel->delete();

        return redirect()->route('hostel.hostels')
            ->with('success', 'Hostel deleted successfully.');
    }

    public function rooms()
    {
        $rooms = HostelRoom::with(['hostel', 'roomType', 'students'])->paginate(15);
        $hostels = Hostel::all();
        $roomTypes = RoomType::all();
        return view('hostel.rooms', compact('rooms', 'hostels', 'roomTypes'));
    }

    public function createRoom()
    {
        $hostels = Hostel::all();
        $roomTypes = RoomType::all();
        return view('hostel.create-room', compact('hostels', 'roomTypes'));
    }

    public function storeRoom(Request $request)
    {
        $request->validate([
            'hostel_id' => 'required|exists:hostels,id',
            'room_type_id' => 'required|exists:room_types,id',
            'room_number' => 'required|string|max:255',
            'floor' => 'required|integer|min:1',
            'capacity' => 'required|integer|min:1',
            'rent' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_available' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Check if room number already exists in the same hostel
        $existingRoom = HostelRoom::where('hostel_id', $request->hostel_id)
            ->where('room_number', $request->room_number)
            ->first();

        if ($existingRoom) {
            return back()->withErrors(['room_number' => 'Room number already exists in this hostel.']);
        }

        HostelRoom::create($request->all());

        return redirect()->route('hostel.rooms')
            ->with('success', 'Room created successfully.');
    }

    public function showRoom(HostelRoom $room)
    {
        $room->load(['hostel', 'roomType', 'students.user']);
        return view('hostel.show-room', compact('room'));
    }

    public function editRoom(HostelRoom $room)
    {
        $hostels = Hostel::all();
        $roomTypes = RoomType::all();
        return view('hostel.edit-room', compact('room', 'hostels', 'roomTypes'));
    }

    public function updateRoom(Request $request, HostelRoom $room)
    {
        $request->validate([
            'hostel_id' => 'required|exists:hostels,id',
            'room_type_id' => 'required|exists:room_types,id',
            'room_number' => 'required|string|max:255',
            'floor' => 'required|integer|min:1',
            'capacity' => 'required|integer|min:1',
            'rent' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_available' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Check if room number already exists in the same hostel (excluding current room)
        $existingRoom = HostelRoom::where('hostel_id', $request->hostel_id)
            ->where('room_number', $request->room_number)
            ->where('id', '!=', $room->id)
            ->first();

        if ($existingRoom) {
            return back()->withErrors(['room_number' => 'Room number already exists in this hostel.']);
        }

        $room->update($request->all());

        return redirect()->route('hostel.rooms')
            ->with('success', 'Room updated successfully.');
    }

    public function destroyRoom(HostelRoom $room)
    {
        if ($room->students()->count() > 0) {
            return redirect()->route('hostel.rooms')
                ->with('error', 'Cannot delete room with allocated students.');
        }

        $room->delete();

        return redirect()->route('hostel.rooms')
            ->with('success', 'Room deleted successfully.');
    }

    public function roomTypes()
    {
        $roomTypes = RoomType::withCount('rooms')->get();
        return view('hostel.room-types', compact('roomTypes'));
    }

    public function createRoomType()
    {
        return view('hostel.create-room-type');
    }

    public function storeRoomType(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:room_types',
            'description' => 'nullable|string',
            'base_rent' => 'required|numeric|min:0',
            'max_capacity' => 'required|integer|min:1',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string',
        ]);

        RoomType::create($request->all());

        return redirect()->route('hostel.room-types')
            ->with('success', 'Room type created successfully.');
    }

    public function editRoomType(RoomType $roomType)
    {
        return view('hostel.edit-room-type', compact('roomType'));
    }

    public function updateRoomType(Request $request, RoomType $roomType)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:room_types,name,' . $roomType->id,
            'description' => 'nullable|string',
            'base_rent' => 'required|numeric|min:0',
            'max_capacity' => 'required|integer|min:1',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string',
        ]);

        $roomType->update($request->all());

        return redirect()->route('hostel.room-types')
            ->with('success', 'Room type updated successfully.');
    }

    public function destroyRoomType(RoomType $roomType)
    {
        if ($roomType->rooms()->count() > 0) {
            return redirect()->route('hostel.room-types')
                ->with('error', 'Cannot delete room type with existing rooms.');
        }

        $roomType->delete();

        return redirect()->route('hostel.room-types')
            ->with('success', 'Room type deleted successfully.');
    }

    public function allocations()
    {
        $allocations = HostelAllocation::with(['student.user', 'room.hostel', 'room.roomType'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('hostel.allocations', compact('allocations'));
    }

    public function createAllocation()
    {
        $students = Student::doesntHave('hostelAllocation')->get();
        $rooms = HostelRoom::where('is_available', true)
            ->where('is_active', true)
            ->with(['hostel', 'roomType'])
            ->get();
        return view('hostel.create-allocation', compact('students', 'rooms'));
    }

    public function storeAllocation(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'room_id' => 'required|exists:hostel_rooms,id',
            'check_in_date' => 'required|date|before_or_equal:today',
            'check_out_date' => 'nullable|date|after:check_in_date',
            'rent_amount' => 'required|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Check if student is already allocated
        $existingAllocation = HostelAllocation::where('student_id', $request->student_id)
            ->where('is_active', true)
            ->first();

        if ($existingAllocation) {
            return back()->withErrors(['student_id' => 'Student is already allocated to a hostel room.']);
        }

        // Check room availability
        $room = HostelRoom::findOrFail($request->room_id);
        $currentOccupancy = $room->students()->where('is_active', true)->count();
        
        if ($currentOccupancy >= $room->capacity) {
            return back()->withErrors(['room_id' => 'Room has reached maximum capacity.']);
        }

        DB::transaction(function () use ($request, $room) {
            HostelAllocation::create($request->all());
            
            // Update room availability if full
            if (($room->students()->where('is_active', true)->count() + 1) >= $room->capacity) {
                $room->update(['is_available' => false]);
            }
        });

        return redirect()->route('hostel.allocations')
            ->with('success', 'Student allocated to hostel room successfully.');
    }

    public function showAllocation(HostelAllocation $allocation)
    {
        $allocation->load(['student.user', 'room.hostel', 'room.roomType']);
        return view('hostel.show-allocation', compact('allocation'));
    }

    public function editAllocation(HostelAllocation $allocation)
    {
        $students = Student::all();
        $rooms = HostelRoom::with(['hostel', 'roomType'])->get();
        return view('hostel.edit-allocation', compact('allocation', 'students', 'rooms'));
    }

    public function updateAllocation(Request $request, HostelAllocation $allocation)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'room_id' => 'required|exists:hostel_rooms,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'nullable|date|after:check_in_date',
            'rent_amount' => 'required|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $oldRoomId = $allocation->room_id;
        $newRoomId = $request->room_id;

        DB::transaction(function () use ($request, $allocation, $oldRoomId, $newRoomId) {
            $allocation->update($request->all());

            // Handle room changes
            if ($oldRoomId != $newRoomId) {
                $oldRoom = HostelRoom::find($oldRoomId);
                $newRoom = HostelRoom::find($newRoomId);

                // Update old room availability
                if ($oldRoom) {
                    $oldRoomOccupancy = $oldRoom->students()->where('is_active', true)->count();
                    if ($oldRoomOccupancy < $oldRoom->capacity) {
                        $oldRoom->update(['is_available' => true]);
                    }
                }

                // Update new room availability
                if ($newRoom) {
                    $newRoomOccupancy = $newRoom->students()->where('is_active', true)->count();
                    if ($newRoomOccupancy >= $newRoom->capacity) {
                        $newRoom->update(['is_available' => false]);
                    }
                }
            }
        });

        return redirect()->route('hostel.allocations')
            ->with('success', 'Allocation updated successfully.');
    }

    public function destroyAllocation(HostelAllocation $allocation)
    {
        DB::transaction(function () use ($allocation) {
            $room = $allocation->room;
            $allocation->delete();

            // Update room availability
            $currentOccupancy = $room->students()->where('is_active', true)->count();
            if ($currentOccupancy < $room->capacity) {
                $room->update(['is_available' => true]);
            }
        });

        return redirect()->route('hostel.allocations')
            ->with('success', 'Allocation deleted successfully.');
    }

    public function checkOut(HostelAllocation $allocation)
    {
        if (!$allocation->is_active) {
            return redirect()->route('hostel.allocations')
                ->with('error', 'Allocation is already inactive.');
        }

        DB::transaction(function () use ($allocation) {
            $allocation->update([
                'is_active' => false,
                'check_out_date' => now(),
            ]);

            // Update room availability
            $room = $allocation->room;
            $currentOccupancy = $room->students()->where('is_active', true)->count();
            if ($currentOccupancy < $room->capacity) {
                $room->update(['is_available' => true]);
            }
        });

        return redirect()->route('hostel.allocations')
            ->with('success', 'Student checked out successfully.');
    }

    public function reports()
    {
        $totalHostels = Hostel::count();
        $totalRooms = HostelRoom::count();
        $totalStudents = HostelAllocation::where('is_active', true)->count();
        $availableRooms = HostelRoom::where('is_available', true)->count();
        
        $hostelStats = Hostel::withCount(['rooms', 'students'])
            ->orderBy('students_count', 'desc')
            ->get();
        
        $roomTypeStats = RoomType::withCount('rooms')
            ->orderBy('rooms_count', 'desc')
            ->get();
        
        $occupancyRate = $totalRooms > 0 ? round(($totalStudents / $totalRooms) * 100, 2) : 0;
        
        $monthlyAllocations = HostelAllocation::selectRaw('strftime("%m", created_at) as month, COUNT(*) as total')
            ->whereRaw('strftime("%Y", created_at) = ?', [date('Y')])
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        return view('hostel.reports', compact(
            'totalHostels',
            'totalRooms',
            'totalStudents',
            'availableRooms',
            'hostelStats',
            'roomTypeStats',
            'occupancyRate',
            'monthlyAllocations'
        ));
    }

    public function search(Request $request)
    {
        $query = $request->get('query');
        $hostel = $request->get('hostel');
        $roomType = $request->get('room_type');
        $status = $request->get('status');

        $rooms = HostelRoom::query()
            ->when($query, function ($q) use ($query) {
                $q->where('room_number', 'like', "%{$query}%");
            })
            ->when($hostel, function ($q) use ($hostel) {
                $q->where('hostel_id', $hostel);
            })
            ->when($roomType, function ($q) use ($roomType) {
                $q->where('room_type_id', $roomType);
            })
            ->when($status, function ($q) use ($status) {
                if ($status === 'available') {
                    $q->where('is_available', true);
                } elseif ($status === 'unavailable') {
                    $q->where('is_available', false);
                }
            })
            ->with(['hostel', 'roomType', 'students'])
            ->paginate(15);

        $hostels = Hostel::all();
        $roomTypes = RoomType::all();
        
        return view('hostel.search', compact('rooms', 'hostels', 'roomTypes', 'query', 'hostel', 'roomType', 'status'));
    }
}

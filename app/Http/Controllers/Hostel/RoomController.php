<?php

namespace App\Http\Controllers\Hostel;

use App\Http\Controllers\Controller;
use App\Models\HostelRoom;
use App\Models\Hostel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = HostelRoom::with(['hostel', 'students.user']);

            // Search functionality
            if ($request->has('search') && $request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('room_number', 'like', '%' . $request->search . '%')
                      ->orWhere('floor', 'like', '%' . $request->search . '%')
                      ->orWhereHas('hostel', function($hostelQuery) use ($request) {
                          $hostelQuery->where('name', 'like', '%' . $request->search . '%');
                      });
                });
            }

            // Filter by hostel
            if ($request->has('hostel_id') && $request->hostel_id) {
                $query->where('hostel_id', $request->hostel_id);
            }

            // Filter by status
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            $rooms = $query->orderBy('created_at', 'desc')->paginate(15);
            $hostels = Hostel::where('status', 'active')->get();

            return view('hostel.rooms', compact('rooms', 'hostels'));
        } catch (\Exception $e) {
            \Log::error('HostelRoomController index error: ' . $e->getMessage());
            
            // Fallback data if database issues
            $rooms = new \Illuminate\Pagination\LengthAwarePaginator(
                collect(),
                0,
                15,
                1,
                ['path' => request()->url()]
            );
            $hostels = collect();
            
            return view('hostel.rooms', compact('rooms', 'hostels'));
        }
    }

    public function create()
    {
        try {
            $hostels = Hostel::where('status', 'active')->get();
            return view('hostel.rooms.create', compact('hostels'));
        } catch (\Exception $e) {
            \Log::error('HostelRoomController create error: ' . $e->getMessage());
            $hostels = collect();
            return view('hostel.rooms.create', compact('hostels'));
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'room_number' => 'required|string|max:50',
                'hostel_id' => 'required|exists:hostels,id',
                'floor' => 'required|integer|min:0|max:50',
                'room_type' => 'required|in:single,double,triple,quad,dormitory',
                'capacity' => 'required|integer|min:1|max:20',
                'rent_amount' => 'required|numeric|min:0',
                'currency' => 'required|string|max:3',
                'status' => 'required|in:available,occupied,maintenance',
                'description' => 'nullable|string',
                'amenities' => 'nullable|string',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $data = $request->all();
            $data['current_occupancy'] = 0;

            // Handle photo upload
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $filename = time() . '_' . $photo->getClientOriginalName();
                $path = $photo->storeAs('hostel/rooms', $filename, 'public');
                $data['photo'] = $path;
            }

            HostelRoom::create($data);

            return redirect()->route('hostel.rooms')
                ->with('success', 'Room added successfully.');
        } catch (\Exception $e) {
            \Log::error('HostelRoomController store error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to add room. Please try again.')
                ->withInput();
        }
    }

    public function show(HostelRoom $room)
    {
        try {
            $room->load(['hostel', 'students.user.classRoom']);
            
            return view('hostel.rooms.show', compact('room'));
        } catch (\Exception $e) {
            \Log::error('HostelRoomController show error: ' . $e->getMessage());
            return redirect()->route('hostel.rooms')
                ->with('error', 'Room not found.');
        }
    }

    public function edit(HostelRoom $room)
    {
        try {
            $hostels = Hostel::where('status', 'active')->get();
            return view('hostel.rooms.edit', compact('room', 'hostels'));
        } catch (\Exception $e) {
            \Log::error('HostelRoomController edit error: ' . $e->getMessage());
            return redirect()->route('hostel.rooms')
                ->with('error', 'Room not found.');
        }
    }

    public function update(Request $request, HostelRoom $room)
    {
        try {
            $request->validate([
                'room_number' => 'required|string|max:50',
                'hostel_id' => 'required|exists:hostels,id',
                'floor' => 'required|integer|min:0|max:50',
                'room_type' => 'required|in:single,double,triple,quad,dormitory',
                'capacity' => 'required|integer|min:1|max:20',
                'rent_amount' => 'required|numeric|min:0',
                'currency' => 'required|string|max:3',
                'status' => 'required|in:available,occupied,maintenance',
                'description' => 'nullable|string',
                'amenities' => 'nullable|string',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $data = $request->all();

            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo
                if ($room->photo) {
                    Storage::disk('public')->delete($room->photo);
                }
                
                $photo = $request->file('photo');
                $filename = time() . '_' . $photo->getClientOriginalName();
                $path = $photo->storeAs('hostel/rooms', $filename, 'public');
                $data['photo'] = $path;
            }

            $room->update($data);

            return redirect()->route('hostel.rooms')
                ->with('success', 'Room updated successfully.');
        } catch (\Exception $e) {
            \Log::error('HostelRoomController update error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update room. Please try again.')
                ->withInput();
        }
    }

    public function destroy(HostelRoom $room)
    {
        try {
            // Check if room has assigned students
            if ($room->students()->exists()) {
                return redirect()->back()
                    ->with('error', 'Cannot delete room with assigned students. Please reassign students first.');
            }

            // Delete photo
            if ($room->photo) {
                Storage::disk('public')->delete($room->photo);
            }

            $room->delete();

            return redirect()->route('hostel.rooms')
                ->with('success', 'Room deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('HostelRoomController destroy error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete room. Please try again.');
        }
    }
}

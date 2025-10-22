<?php

namespace App\Http\Controllers\Hostel;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\HostelRoom;
use App\Models\Hostel;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Student::with(['user', 'classRoom', 'hostelRoom.hostel']);

            // Search functionality
            if ($request->has('search') && $request->search) {
                $query->whereHas('user', function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('email', 'like', '%' . $request->search . '%');
                });
            }

            // Filter by hostel
            if ($request->has('hostel_id') && $request->hostel_id) {
                $query->whereHas('hostelRoom', function($q) use ($request) {
                    $q->where('hostel_id', $request->hostel_id);
                });
            }

            // Filter by status
            if ($request->has('status') && $request->status) {
                if ($request->status === 'active') {
                    $query->whereNotNull('hostel_room_id');
                } else {
                    $query->whereNull('hostel_room_id');
                }
            }

            $students = $query->orderBy('created_at', 'desc')->paginate(15);
            $hostels = Hostel::where('status', 'active')->get();

            return view('hostel.students', compact('students', 'hostels'));
        } catch (\Exception $e) {
            \Log::error('HostelStudentController index error: ' . $e->getMessage());
            
            // Fallback data if database issues
            $students = new \Illuminate\Pagination\LengthAwarePaginator(
                collect(),
                0,
                15,
                1,
                ['path' => request()->url()]
            );
            $hostels = collect();
            
            return view('hostel.students', compact('students', 'hostels'));
        }
    }

    public function assign(Request $request)
    {
        try {
            $students = Student::with(['user', 'classRoom'])->whereNull('hostel_room_id')->get();
            $rooms = HostelRoom::where('status', 'available')->with('hostel')->get();
            $hostels = Hostel::where('status', 'active')->get();
            $classes = ClassRoom::with('students')->get();

            // If specific student is requested
            if ($request->has('student_id')) {
                $selectedStudent = Student::with(['user', 'classRoom'])->find($request->student_id);
            } else {
                $selectedStudent = null;
            }

            return view('hostel.students.assign', compact('students', 'rooms', 'hostels', 'classes', 'selectedStudent'));
        } catch (\Exception $e) {
            \Log::error('HostelStudentController assign error: ' . $e->getMessage());
            $students = collect();
            $rooms = collect();
            $hostels = collect();
            $classes = collect();
            $selectedStudent = null;
            return view('hostel.students.assign', compact('students', 'rooms', 'hostels', 'classes', 'selectedStudent'));
        }
    }

    public function storeAssignment(Request $request)
    {
        try {
            $request->validate([
                'student_id' => 'required|exists:students,id',
                'room_id' => 'required|exists:hostel_rooms,id',
                'check_in_date' => 'required|date',
                'rent_amount' => 'required|numeric|min:0',
                'currency' => 'required|string|max:3',
            ]);

            $student = Student::findOrFail($request->student_id);
            $room = HostelRoom::findOrFail($request->room_id);

            // Check if student already has a room assigned
            if ($student->hostel_room_id) {
                return redirect()->back()
                    ->with('error', 'Student already has a hostel room assigned.');
            }

            // Check if room has capacity
            if ($room->current_occupancy >= $room->capacity) {
                return redirect()->back()
                    ->with('error', 'Room is at maximum capacity.');
            }

            // Check if room is available
            if ($room->status !== 'available') {
                return redirect()->back()
                    ->with('error', 'Room is not available for assignment.');
            }

            DB::beginTransaction();

            // Assign student to room
            $student->update([
                'hostel_room_id' => $room->id,
                'check_in_date' => $request->check_in_date,
                'rent_amount' => $request->rent_amount,
                'currency' => $request->currency,
            ]);

            // Update room occupancy
            $room->increment('current_occupancy');

            // Update room status if full
            if ($room->current_occupancy >= $room->capacity) {
                $room->update(['status' => 'occupied']);
            }

            DB::commit();

            return redirect()->route('hostel.students')
                ->with('success', 'Student assigned to hostel room successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('HostelStudentController storeAssignment error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to assign student to room. Please try again.');
        }
    }

    public function show(Student $student)
    {
        try {
            $student->load(['user', 'classRoom', 'hostelRoom.hostel']);
            
            return view('hostel.students.show', compact('student'));
        } catch (\Exception $e) {
            \Log::error('HostelStudentController show error: ' . $e->getMessage());
            return redirect()->route('hostel.students')
                ->with('error', 'Student not found.');
        }
    }

    public function edit(Student $student)
    {
        try {
            if (!$student->hostel_room_id) {
                return redirect()->route('hostel.students')
                    ->with('error', 'Student does not have a hostel room assigned.');
            }

            $rooms = HostelRoom::where('status', 'available')->orWhere('id', $student->hostel_room_id)->with('hostel')->get();
            $hostels = Hostel::where('status', 'active')->get();
            
            return view('hostel.students.edit', compact('student', 'rooms', 'hostels'));
        } catch (\Exception $e) {
            \Log::error('HostelStudentController edit error: ' . $e->getMessage());
            return redirect()->route('hostel.students')
                ->with('error', 'Student not found.');
        }
    }

    public function update(Request $request, Student $student)
    {
        try {
            if (!$student->hostel_room_id) {
                return redirect()->route('hostel.students')
                    ->with('error', 'Student does not have a hostel room assigned.');
            }

            $request->validate([
                'room_id' => 'required|exists:hostel_rooms,id',
                'check_in_date' => 'required|date',
                'rent_amount' => 'required|numeric|min:0',
                'currency' => 'required|string|max:3',
            ]);

            $newRoom = HostelRoom::findOrFail($request->room_id);

            // Check if room has capacity
            if ($newRoom->current_occupancy >= $newRoom->capacity) {
                return redirect()->back()
                    ->with('error', 'New room is at maximum capacity.');
            }

            // Check if room is available
            if ($newRoom->status !== 'available' && $newRoom->id !== $student->hostel_room_id) {
                return redirect()->back()
                    ->with('error', 'New room is not available for assignment.');
            }

            DB::beginTransaction();

            // Update old room occupancy
            if ($student->hostel_room_id) {
                $oldRoom = HostelRoom::find($student->hostel_room_id);
                if ($oldRoom) {
                    $oldRoom->decrement('current_occupancy');
                    
                    // Update old room status if it becomes available
                    if ($oldRoom->current_occupancy < $oldRoom->capacity) {
                        $oldRoom->update(['status' => 'available']);
                    }
                }
            }

            // Assign student to new room
            $student->update([
                'hostel_room_id' => $newRoom->id,
                'check_in_date' => $request->check_in_date,
                'rent_amount' => $request->rent_amount,
                'currency' => $request->currency,
            ]);

            // Update new room occupancy
            $newRoom->increment('current_occupancy');

            // Update new room status if full
            if ($newRoom->current_occupancy >= $newRoom->capacity) {
                $newRoom->update(['status' => 'occupied']);
            }

            DB::commit();

            return redirect()->route('hostel.students')
                ->with('success', 'Student hostel assignment updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('HostelStudentController update error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update student assignment. Please try again.');
        }
    }

    public function removeAssignment(Student $student)
    {
        try {
            if (!$student->hostel_room_id) {
                return redirect()->route('hostel.students')
                    ->with('error', 'Student does not have a hostel room assigned.');
            }

            DB::beginTransaction();

            // Update room occupancy
            $room = HostelRoom::find($student->hostel_room_id);
            if ($room) {
                $room->decrement('current_occupancy');
                
                // Update room status if it becomes available
                if ($room->current_occupancy < $room->capacity) {
                    $room->update(['status' => 'available']);
                }
            }

            // Remove student assignment
            $student->update([
                'hostel_room_id' => null,
                'check_in_date' => null,
                'rent_amount' => null,
                'currency' => null,
            ]);

            DB::commit();

            return redirect()->route('hostel.students')
                ->with('success', 'Student hostel assignment removed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('HostelStudentController removeAssignment error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to remove student assignment. Please try again.');
        }
    }
}

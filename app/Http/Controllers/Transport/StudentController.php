<?php

namespace App\Http\Controllers\Transport;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\TransportRoute;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Student::with(['user', 'classRoom', 'transportRoute']);

            // Search functionality
            if ($request->has('search') && $request->search) {
                $query->whereHas('user', function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('email', 'like', '%' . $request->search . '%');
                });
            }

            // Filter by route
            if ($request->has('route_id') && $request->route_id) {
                $query->where('transport_route_id', $request->route_id);
            }

            // Filter by status
            if ($request->has('status') && $request->status) {
                if ($request->status === 'active') {
                    $query->whereNotNull('transport_route_id');
                } else {
                    $query->whereNull('transport_route_id');
                }
            }

            $students = $query->orderBy('created_at', 'desc')->paginate(15);
            $routes = TransportRoute::where('is_active', true)->get();

            return view('transport.students.index', compact('students', 'routes'));
        } catch (\Exception $e) {
            \Log::error('TransportStudentController index error: ' . $e->getMessage());
            
            // Fallback data if database issues
            $students = new \Illuminate\Pagination\LengthAwarePaginator(
                collect(),
                0,
                15,
                1,
                ['path' => request()->url()]
            );
            $routes = collect();
            
            return view('transport.students.index', compact('students', 'routes'));
        }
    }

    public function assign(Request $request)
    {
        try {
            $students = Student::with(['user', 'classRoom'])->whereNull('transport_route_id')->get();
            $routes = TransportRoute::where('is_active', true)->with('transport')->get();
            $classes = ClassRoom::with('students')->get();

            // If specific student is requested
            if ($request->has('student_id')) {
                $selectedStudent = Student::with(['user', 'classRoom'])->find($request->student_id);
            } else {
                $selectedStudent = null;
            }

            return view('transport.students.assign', compact('students', 'routes', 'classes', 'selectedStudent'));
        } catch (\Exception $e) {
            \Log::error('TransportStudentController assign error: ' . $e->getMessage());
            $students = collect();
            $routes = collect();
            $classes = collect();
            $selectedStudent = null;
            return view('transport.students.assign', compact('students', 'routes', 'classes', 'selectedStudent'));
        }
    }

    public function storeAssignment(Request $request)
    {
        try {
            $request->validate([
                'student_id' => 'required|exists:students,id',
                'route_id' => 'required|exists:transport_routes,id',
            ]);

            $student = Student::findOrFail($request->student_id);
            $route = TransportRoute::findOrFail($request->route_id);

            // Check if student already has a route assigned
            if ($student->transport_route_id) {
                return redirect()->back()
                    ->with('error', 'Student already has a transport route assigned.');
            }

            // Check if route has capacity
            if ($route->current_passengers >= $route->max_capacity) {
                return redirect()->back()
                    ->with('error', 'Route is at maximum capacity.');
            }

            DB::beginTransaction();

            // Assign student to route
            $student->update(['transport_route_id' => $route->id]);

            // Update route passenger count
            $route->increment('current_passengers');

            DB::commit();

            return redirect()->route('transport.students.index')
                ->with('success', 'Student assigned to transport route successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('TransportStudentController storeAssignment error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to assign student to route. Please try again.');
        }
    }

    public function show(Student $student)
    {
        try {
            $student->load(['user', 'classRoom', 'transportRoute.transport']);
            
            return view('transport.students.show', compact('student'));
        } catch (\Exception $e) {
            \Log::error('TransportStudentController show error: ' . $e->getMessage());
            return redirect()->route('transport.students.index')
                ->with('error', 'Student not found.');
        }
    }

    public function edit(Student $student)
    {
        try {
            if (!$student->transport_route_id) {
                return redirect()->route('transport.students.index')
                    ->with('error', 'Student does not have a transport route assigned.');
            }

            $routes = TransportRoute::where('is_active', true)->with('transport')->get();
            
            return view('transport.students.edit', compact('student', 'routes'));
        } catch (\Exception $e) {
            \Log::error('TransportStudentController edit error: ' . $e->getMessage());
            return redirect()->route('transport.students.index')
                ->with('error', 'Student not found.');
        }
    }

    public function update(Request $request, Student $student)
    {
        try {
            if (!$student->transport_route_id) {
                return redirect()->route('transport.students.index')
                    ->with('error', 'Student does not have a transport route assigned.');
            }

            $request->validate([
                'route_id' => 'required|exists:transport_routes,id',
            ]);

            $newRoute = TransportRoute::findOrFail($request->route_id);

            // Check if route has capacity
            if ($newRoute->current_passengers >= $newRoute->max_capacity) {
                return redirect()->back()
                    ->with('error', 'New route is at maximum capacity.');
            }

            DB::beginTransaction();

            // Update old route passenger count
            if ($student->transport_route_id) {
                $oldRoute = TransportRoute::find($student->transport_route_id);
                if ($oldRoute) {
                    $oldRoute->decrement('current_passengers');
                }
            }

            // Assign student to new route
            $student->update(['transport_route_id' => $newRoute->id]);

            // Update new route passenger count
            $newRoute->increment('current_passengers');

            DB::commit();

            return redirect()->route('transport.students.index')
                ->with('success', 'Student transport assignment updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('TransportStudentController update error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update student assignment. Please try again.');
        }
    }

    public function removeAssignment(Student $student)
    {
        try {
            if (!$student->transport_route_id) {
                return redirect()->route('transport.students.index')
                    ->with('error', 'Student does not have a transport route assigned.');
            }

            DB::beginTransaction();

            // Update route passenger count
            $route = TransportRoute::find($student->transport_route_id);
            if ($route) {
                $route->decrement('current_passengers');
            }

            // Remove student assignment
            $student->update(['transport_route_id' => null]);

            DB::commit();

            return redirect()->route('transport.students.index')
                ->with('success', 'Student transport assignment removed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('TransportStudentController removeAssignment error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to remove student assignment. Please try again.');
        }
    }
}

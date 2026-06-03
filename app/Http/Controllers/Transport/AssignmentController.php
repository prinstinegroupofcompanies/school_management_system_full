<?php

namespace App\Http\Controllers\Transport;

use App\Http\Controllers\Controller;
use App\Models\TransportAssignment;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\TransportRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssignmentController extends Controller
{
    /**
     * Display a listing of assignments.
     */
    public function index(Request $request)
    {
        $query = TransportAssignment::with(['user', 'vehicle', 'route']);

        // Filter by user if conductor/driver viewing their own
        if (auth()->user()->hasRole('conductor_driver')) {
            $query->where('user_id', auth()->id());
        } elseif ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by active status
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        } else {
            $query->active(); // Default to active
        }

        $assignments = $query->orderBy('assigned_from', 'desc')->paginate(15);
        $drivers = User::role('conductor_driver')->get();
        $vehicles = Vehicle::active()->get();
        $routes = TransportRoute::where('is_active', true)->get();

        return view('transport.assignments.index', compact('assignments', 'drivers', 'vehicles', 'routes'));
    }

    /**
     * Show the form for creating a new assignment.
     */
    public function create()
    {
        $drivers = User::role('conductor_driver')->get();
        $vehicles = Vehicle::active()->get();
        $routes = TransportRoute::where('is_active', true)->get();

        return view('transport.assignments.create', compact('drivers', 'vehicles', 'routes'));
    }

    /**
     * Store a newly created assignment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'route_id' => 'nullable|exists:transport_routes,id',
            'assigned_from' => 'required|date',
            'assigned_to' => 'nullable|date|after:assigned_from',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Check if user has conductor_driver role
        $user = User::findOrFail($request->user_id);
        if (!$user->hasRole('conductor_driver')) {
            return back()->withErrors(['user_id' => 'User must have conductor/driver role.']);
        }

        DB::transaction(function () use ($request) {
            // Deactivate any existing active assignments for this user
            TransportAssignment::where('user_id', $request->user_id)
                ->where('is_active', true)
                ->update(['is_active' => false, 'assigned_to' => now()]);

            // Create new assignment
            TransportAssignment::create([
                'user_id' => $request->user_id,
                'vehicle_id' => $request->vehicle_id,
                'route_id' => $request->route_id,
                'assigned_from' => $request->assigned_from,
                'assigned_to' => $request->assigned_to,
                'is_active' => true,
                'notes' => $request->notes,
            ]);
        });

        return redirect()->route('transport.assignments.index')
            ->with('success', 'Transport assignment created successfully.');
    }

    /**
     * Display the specified assignment.
     */
    public function show(TransportAssignment $assignment)
    {
        $assignment->load(['user', 'vehicle', 'route']);
        return view('transport.assignments.show', compact('assignment'));
    }

    /**
     * Show the form for editing the specified assignment.
     */
    public function edit(TransportAssignment $assignment)
    {
        $drivers = User::role('conductor_driver')->get();
        $vehicles = Vehicle::active()->get();
        $routes = TransportRoute::where('is_active', true)->get();

        return view('transport.assignments.edit', compact('assignment', 'drivers', 'vehicles', 'routes'));
    }

    /**
     * Update the specified assignment.
     */
    public function update(Request $request, TransportAssignment $assignment)
    {
        $request->validate([
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'route_id' => 'nullable|exists:transport_routes,id',
            'assigned_from' => 'required|date',
            'assigned_to' => 'nullable|date|after:assigned_from',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        $assignment->update($request->all());

        return redirect()->route('transport.assignments.index')
            ->with('success', 'Transport assignment updated successfully.');
    }

    /**
     * Remove the specified assignment.
     */
    public function destroy(TransportAssignment $assignment)
    {
        $assignment->update(['is_active' => false, 'assigned_to' => now()]);

        return redirect()->route('transport.assignments.index')
            ->with('success', 'Transport assignment deactivated successfully.');
    }

    /**
     * Dashboard for conductor/driver to view their assignments.
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        if (!$user->hasRole('conductor_driver')) {
            abort(403, 'Unauthorized access.');
        }

        $currentAssignment = TransportAssignment::where('user_id', $user->id)
            ->active()
            ->with(['vehicle', 'route'])
            ->latest('assigned_from')
            ->first();

        $assignmentHistory = TransportAssignment::where('user_id', $user->id)
            ->with(['vehicle', 'route'])
            ->orderBy('assigned_from', 'desc')
            ->paginate(10);

        return view('transport.driver.dashboard', compact('currentAssignment', 'assignmentHistory'));
    }
}

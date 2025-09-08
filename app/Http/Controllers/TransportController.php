<?php

namespace App\Http\Controllers;

use App\Models\TransportRoute;
use App\Models\Vehicle;
use App\Models\Student;
use App\Models\Driver;
use App\Models\RouteStop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransportController extends Controller
{
    public function index()
    {
        $routes = TransportRoute::with(['vehicle', 'driver'])->paginate(15);
        $vehicles = Vehicle::with(['driver', 'route'])->paginate(15);
        return view('transport.index', compact('routes', 'vehicles'));
    }

    public function routes()
    {
        $routes = TransportRoute::with(['vehicle', 'driver', 'stops'])->paginate(15);
        return view('transport.routes', compact('routes'));
    }

    public function createRoute()
    {
        $vehicles = Vehicle::whereDoesntHave('route')->get();
        $drivers = Driver::whereDoesntHave('route')->get();
        return view('transport.create-route', compact('vehicles', 'drivers'));
    }

    public function storeRoute(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'required|exists:drivers,id',
            'start_location' => 'required|string|max:255',
            'end_location' => 'required|string|max:255',
            'distance' => 'required|numeric|min:0',
            'estimated_time' => 'required|string|max:255',
            'fare' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        TransportRoute::create($request->all());

        return redirect()->route('transport.routes')
            ->with('success', 'Transport route created successfully.');
    }

    public function showRoute(TransportRoute $route)
    {
        $route->load(['vehicle', 'driver', 'stops', 'students']);
        return view('transport.show-route', compact('route'));
    }

    public function editRoute(TransportRoute $route)
    {
        $vehicles = Vehicle::all();
        $drivers = Driver::all();
        return view('transport.edit-route', compact('route', 'vehicles', 'drivers'));
    }

    public function updateRoute(Request $request, TransportRoute $route)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'required|exists:drivers,id',
            'start_location' => 'required|string|max:255',
            'end_location' => 'required|string|max:255',
            'distance' => 'required|numeric|min:0',
            'estimated_time' => 'required|string|max:255',
            'fare' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $route->update($request->all());

        return redirect()->route('transport.routes')
            ->with('success', 'Transport route updated successfully.');
    }

    public function destroyRoute(TransportRoute $route)
    {
        if ($route->students()->count() > 0) {
            return redirect()->route('transport.routes')
                ->with('error', 'Cannot delete route with assigned students.');
        }

        $route->delete();

        return redirect()->route('transport.routes')
            ->with('success', 'Transport route deleted successfully.');
    }

    public function vehicles()
    {
        $vehicles = Vehicle::with(['driver', 'route'])->paginate(15);
        return view('transport.vehicles', compact('vehicles'));
    }

    public function createVehicle()
    {
        $drivers = Driver::whereDoesntHave('vehicle')->get();
        return view('transport.create-vehicle', compact('drivers'));
    }

    public function storeVehicle(Request $request)
    {
        $request->validate([
            'vehicle_number' => 'required|string|max:255|unique:vehicles',
            'model' => 'required|string|max:255',
            'make' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'capacity' => 'required|integer|min:1',
            'driver_id' => 'nullable|exists:drivers,id',
            'color' => 'nullable|string|max:255',
            'registration_number' => 'required|string|max:255|unique:vehicles',
            'insurance_expiry' => 'required|date|after:today',
            'fitness_expiry' => 'required|date|after:today',
            'permit_expiry' => 'required|date|after:today',
            'status' => 'required|in:active,maintenance,inactive',
            'notes' => 'nullable|string',
        ]);

        Vehicle::create($request->all());

        return redirect()->route('transport.vehicles')
            ->with('success', 'Vehicle added successfully.');
    }

    public function showVehicle(Vehicle $vehicle)
    {
        $vehicle->load(['driver', 'route', 'students']);
        return view('transport.show-vehicle', compact('vehicle'));
    }

    public function editVehicle(Vehicle $vehicle)
    {
        $drivers = Driver::all();
        return view('transport.edit-vehicle', compact('vehicle', 'drivers'));
    }

    public function updateVehicle(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'vehicle_number' => 'required|string|max:255|unique:vehicles,vehicle_number,' . $vehicle->id,
            'model' => 'required|string|max:255',
            'make' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'capacity' => 'required|integer|min:1',
            'driver_id' => 'nullable|exists:drivers,id',
            'color' => 'nullable|string|max:255',
            'registration_number' => 'required|string|max:255|unique:vehicles,registration_number,' . $vehicle->id,
            'insurance_expiry' => 'required|date',
            'fitness_expiry' => 'required|date',
            'permit_expiry' => 'required|date',
            'status' => 'required|in:active,maintenance,inactive',
            'notes' => 'nullable|string',
        ]);

        $vehicle->update($request->all());

        return redirect()->route('transport.vehicles')
            ->with('success', 'Vehicle updated successfully.');
    }

    public function destroyVehicle(Vehicle $vehicle)
    {
        if ($vehicle->route) {
            return redirect()->route('transport.vehicles')
                ->with('error', 'Cannot delete vehicle assigned to a route.');
        }

        $vehicle->delete();

        return redirect()->route('transport.vehicles')
            ->with('success', 'Vehicle deleted successfully.');
    }

    public function drivers()
    {
        $drivers = Driver::with(['vehicle', 'route'])->paginate(15);
        return view('transport.drivers', compact('drivers'));
    }

    public function createDriver()
    {
        return view('transport.create-driver');
    }

    public function storeDriver(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'license_number' => 'required|string|max:255|unique:drivers',
            'phone' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'license_expiry' => 'required|date|after:today',
            'experience_years' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive,suspended',
            'notes' => 'nullable|string',
        ]);

        Driver::create($request->all());

        return redirect()->route('transport.drivers')
            ->with('success', 'Driver added successfully.');
    }

    public function showDriver(Driver $driver)
    {
        $driver->load(['vehicle', 'route', 'route.students']);
        return view('transport.show-driver', compact('driver'));
    }

    public function editDriver(Driver $driver)
    {
        return view('transport.edit-driver', compact('driver'));
    }

    public function updateDriver(Request $request, Driver $driver)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'license_number' => 'required|string|max:255|unique:drivers,license_number,' . $driver->id,
            'phone' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'license_expiry' => 'required|date',
            'experience_years' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive,suspended',
            'notes' => 'nullable|string',
        ]);

        $driver->update($request->all());

        return redirect()->route('transport.drivers')
            ->with('success', 'Driver updated successfully.');
    }

    public function destroyDriver(Driver $driver)
    {
        if ($driver->vehicle || $driver->route) {
            return redirect()->route('transport.drivers')
                ->with('error', 'Cannot delete driver assigned to vehicle or route.');
        }

        $driver->delete();

        return redirect()->route('transport.drivers')
            ->with('success', 'Driver deleted successfully.');
    }

    public function assignStudent(Request $request, TransportRoute $route)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'pickup_location' => 'required|string|max:255',
            'dropoff_location' => 'required|string|max:255',
            'fare' => 'required|numeric|min:0',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        // Check if student is already assigned to another route
        $existingAssignment = DB::table('student_transport_assignments')
            ->where('student_id', $request->student_id)
            ->where('is_active', true)
            ->first();

        if ($existingAssignment) {
            return back()->withErrors(['student_id' => 'Student is already assigned to another transport route.']);
        }

        // Check vehicle capacity
        $currentStudents = $route->students()->where('is_active', true)->count();
        if ($currentStudents >= $route->vehicle->capacity) {
            return back()->withErrors(['route_id' => 'Vehicle has reached maximum capacity.']);
        }

        DB::table('student_transport_assignments')->insert([
            'student_id' => $request->student_id,
            'transport_route_id' => $route->id,
            'pickup_location' => $request->pickup_location,
            'dropoff_location' => $request->dropoff_location,
            'fare' => $request->fare,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->is_active ?? true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('transport.routes.show', $route)
            ->with('success', 'Student assigned to transport route successfully.');
    }

    public function removeStudent(Request $request, TransportRoute $route)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        DB::table('student_transport_assignments')
            ->where('student_id', $request->student_id)
            ->where('transport_route_id', $route->id)
            ->update(['is_active' => false, 'end_date' => now()]);

        return redirect()->route('transport.routes.show', $route)
            ->with('success', 'Student removed from transport route successfully.');
    }

    public function stops(TransportRoute $route)
    {
        $stops = $route->stops()->orderBy('sequence')->get();
        return view('transport.stops', compact('route', 'stops'));
    }

    public function addStop(Request $request, TransportRoute $route)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'sequence' => 'required|integer|min:1',
            'pickup_time' => 'required|date_format:H:i',
            'dropoff_time' => 'required|date_format:H:i',
            'description' => 'nullable|string',
        ]);

        RouteStop::create([
            'transport_route_id' => $route->id,
            'name' => $request->name,
            'location' => $request->location,
            'sequence' => $request->sequence,
            'pickup_time' => $request->pickup_time,
            'dropoff_time' => $request->dropoff_time,
            'description' => $request->description,
        ]);

        return redirect()->route('transport.stops', $route)
            ->with('success', 'Stop added successfully.');
    }

    public function removeStop(RouteStop $stop)
    {
        $route = $stop->route;
        $stop->delete();

        return redirect()->route('transport.stops', $route)
            ->with('success', 'Stop removed successfully.');
    }

    public function reports()
    {
        $totalRoutes = TransportRoute::count();
        $totalVehicles = Vehicle::count();
        $totalDrivers = Driver::count();
        $totalStudents = DB::table('student_transport_assignments')
            ->where('is_active', true)
            ->count();
        
        $routeStats = TransportRoute::withCount('students')
            ->orderBy('students_count', 'desc')
            ->get();
        
        $vehicleStats = Vehicle::withCount('route')
            ->orderBy('route_count', 'desc')
            ->get();
        
        $expiringDocuments = collect();
        
        // Check expiring documents
        $expiringVehicles = Vehicle::where('insurance_expiry', '<=', now()->addDays(30))
            ->orWhere('fitness_expiry', '<=', now()->addDays(30))
            ->orWhere('permit_expiry', '<=', now()->addDays(30))
            ->get();
        
        $expiringDrivers = Driver::where('license_expiry', '<=', now()->addDays(30))->get();
        
        return view('transport.reports', compact(
            'totalRoutes',
            'totalVehicles',
            'totalDrivers',
            'totalStudents',
            'routeStats',
            'vehicleStats',
            'expiringVehicles',
            'expiringDrivers'
        ));
    }

    public function maintenance()
    {
        $vehicles = Vehicle::where('status', 'maintenance')->get();
        $expiringDocuments = collect();
        
        // Get vehicles with expiring documents
        $expiringInsurance = Vehicle::where('insurance_expiry', '<=', now()->addDays(30))->get();
        $expiringFitness = Vehicle::where('fitness_expiry', '<=', now()->addDays(30))->get();
        $expiringPermit = Vehicle::where('permit_expiry', '<=', now()->addDays(30))->get();
        
        return view('transport.maintenance', compact(
            'vehicles',
            'expiringInsurance',
            'expiringFitness',
            'expiringPermit'
        ));
    }

    public function updateVehicleStatus(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'status' => 'required|in:active,maintenance,inactive',
            'notes' => 'nullable|string',
        ]);

        $vehicle->update($request->all());

        return redirect()->route('transport.vehicles')
            ->with('success', 'Vehicle status updated successfully.');
    }
}

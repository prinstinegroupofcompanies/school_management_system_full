<?php

namespace App\Http\Controllers\Transport;

use App\Http\Controllers\Controller;
use App\Models\TransportRoute;
use App\Models\Transport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RouteController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = TransportRoute::with(['transport', 'students.user']);

            // Search functionality
            if ($request->has('search') && $request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('route_name', 'like', '%' . $request->search . '%')
                      ->orWhere('start_location', 'like', '%' . $request->search . '%')
                      ->orWhere('end_location', 'like', '%' . $request->search . '%')
                      ->orWhere('route_code', 'like', '%' . $request->search . '%');
                });
            }

            // Filter by status
            if ($request->has('status') && $request->status) {
                $query->where('is_active', $request->status === 'active');
            }

            // Filter by vehicle
            if ($request->has('vehicle_id') && $request->vehicle_id) {
                $query->where('transport_id', $request->vehicle_id);
            }

            $routes = $query->orderBy('created_at', 'desc')->paginate(15);
            $vehicles = Transport::where('status', 'active')->get();

            return view('transport.routes', compact('routes', 'vehicles'));
        } catch (\Exception $e) {
            \Log::error('RouteController index error: ' . $e->getMessage());
            
            // Fallback data if database issues
            $routes = new \Illuminate\Pagination\LengthAwarePaginator(
                collect(),
                0,
                15,
                1,
                ['path' => request()->url()]
            );
            $vehicles = collect();
            
            return view('transport.routes', compact('routes', 'vehicles'));
        }
    }

    public function create()
    {
        try {
            $vehicles = Transport::where('status', 'active')->get();
            return view('transport.routes.create', compact('vehicles'));
        } catch (\Exception $e) {
            \Log::error('RouteController create error: ' . $e->getMessage());
            $vehicles = collect();
            return view('transport.routes.create', compact('vehicles'));
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'route_name' => 'required|string|max:255',
                'route_code' => 'required|string|max:20|unique:transport_routes,route_code',
                'start_location' => 'required|string|max:255',
                'end_location' => 'required|string|max:255',
                'distance_km' => 'required|numeric|min:0.1',
                'fare_amount' => 'required|numeric|min:0',
                'currency' => 'required|string|max:3',
                'transport_id' => 'nullable|exists:transports,id',
                'morning_pickup_time' => 'required|date_format:H:i',
                'afternoon_dropoff_time' => 'required|date_format:H:i',
                'max_capacity' => 'required|integer|min:1|max:100',
                'description' => 'nullable|string',
                'is_active' => 'boolean',
            ]);

            $data = $request->all();
            $data['is_active'] = $request->boolean('is_active');
            $data['current_passengers'] = 0;

            TransportRoute::create($data);

            return redirect()->route('transport.routes')
                ->with('success', 'Route created successfully.');
        } catch (\Exception $e) {
            \Log::error('RouteController store error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create route. Please try again.')
                ->withInput();
        }
    }

    public function show(TransportRoute $route)
    {
        try {
            $route->load(['transport', 'students.user.classRoom']);
            
            return view('transport.routes.show', compact('route'));
        } catch (\Exception $e) {
            \Log::error('RouteController show error: ' . $e->getMessage());
            return redirect()->route('transport.routes')
                ->with('error', 'Route not found.');
        }
    }

    public function edit(TransportRoute $route)
    {
        try {
            $vehicles = Transport::where('status', 'active')->get();
            return view('transport.routes.edit', compact('route', 'vehicles'));
        } catch (\Exception $e) {
            \Log::error('RouteController edit error: ' . $e->getMessage());
            return redirect()->route('transport.routes')
                ->with('error', 'Route not found.');
        }
    }

    public function update(Request $request, TransportRoute $route)
    {
        try {
            $request->validate([
                'route_name' => 'required|string|max:255',
                'route_code' => 'required|string|max:20|unique:transport_routes,route_code,' . $route->id,
                'start_location' => 'required|string|max:255',
                'end_location' => 'required|string|max:255',
                'distance_km' => 'required|numeric|min:0.1',
                'fare_amount' => 'required|numeric|min:0',
                'currency' => 'required|string|max:3',
                'transport_id' => 'nullable|exists:transports,id',
                'morning_pickup_time' => 'required|date_format:H:i',
                'afternoon_dropoff_time' => 'required|date_format:H:i',
                'max_capacity' => 'required|integer|min:1|max:100',
                'description' => 'nullable|string',
                'is_active' => 'boolean',
            ]);

            $data = $request->all();
            $data['is_active'] = $request->boolean('is_active');

            $route->update($data);

            return redirect()->route('transport.routes')
                ->with('success', 'Route updated successfully.');
        } catch (\Exception $e) {
            \Log::error('RouteController update error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update route. Please try again.')
                ->withInput();
        }
    }

    public function destroy(TransportRoute $route)
    {
        try {
            // Check if route has assigned students
            if ($route->students()->exists()) {
                return redirect()->back()
                    ->with('error', 'Cannot delete route with assigned students. Please reassign students first.');
            }

            $route->delete();

            return redirect()->route('transport.routes')
                ->with('success', 'Route deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('RouteController destroy error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete route. Please try again.');
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transport;
use App\Models\TransportRoute;
use Illuminate\Http\Request;

class TransportController extends Controller
{
    public function index()
    {
        $transports = Transport::with('routes')->latest()->paginate(15);
        return view('admin.transport.index', compact('transports'));
    }

    public function create()
    {
        return view('admin.transport.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:bus,van,car',
            'capacity' => 'required|integer|min:1',
            'driver_name' => 'nullable|string|max:255',
            'driver_phone' => 'nullable|string|max:20',
            'vehicle_number' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        Transport::create($request->all());

        return redirect()->route('admin.transport.index')
            ->with('success', 'Transport vehicle created successfully.');
    }

    public function show(Transport $transport)
    {
        $transport->load('routes');
        return view('admin.transport.show', compact('transport'));
    }

    public function edit(Transport $transport)
    {
        return view('admin.transport.edit', compact('transport'));
    }

    public function update(Request $request, Transport $transport)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:bus,van,car',
            'capacity' => 'required|integer|min:1',
            'driver_name' => 'nullable|string|max:255',
            'driver_phone' => 'nullable|string|max:20',
            'vehicle_number' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        $transport->update($request->all());

        return redirect()->route('admin.transport.index')
            ->with('success', 'Transport vehicle updated successfully.');
    }

    public function destroy(Transport $transport)
    {
        $transport->delete();

        return redirect()->route('admin.transport.index')
            ->with('success', 'Transport vehicle deleted successfully.');
    }

    // Transport Routes Management
    public function routes()
    {
        $routes = TransportRoute::with('transport')->latest()->paginate(15);
        return view('admin.transport.routes', compact('routes'));
    }

    public function createRoute()
    {
        $transports = Transport::where('status', 'active')->get();
        return view('admin.transport.create-route', compact('transports'));
    }

    public function storeRoute(Request $request)
    {
        $request->validate([
            'transport_id' => 'nullable|exists:transports,id',
            'route_name' => 'required|string|max:255',
            'route_code' => 'required|string|max:50|unique:transport_routes,route_code',
            'description' => 'nullable|string',
            'start_location' => 'required|string|max:255',
            'end_location' => 'required|string|max:255',
            'distance_km' => 'required|numeric|min:0',
            'estimated_duration_minutes' => 'required|integer|min:1',
            'morning_pickup_time' => 'required|date_format:H:i',
            'morning_dropoff_time' => 'required|date_format:H:i',
            'afternoon_pickup_time' => 'required|date_format:H:i',
            'afternoon_dropoff_time' => 'required|date_format:H:i',
            'fare_amount' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'fare_type' => 'required|string|in:monthly,quarterly,semester,annual',
            'max_capacity' => 'required|integer|min:1',
            'status' => 'required|string|in:active,inactive,suspended',
            'route_details' => 'nullable|array',
            'route_details.*' => 'string|max:255',
        ]);

        $routeData = $request->all();
        $routeData['route_details'] = json_encode($request->route_details ?? []);
        $routeData['is_active'] = $request->status === 'active';

        TransportRoute::create($routeData);

        return redirect()->route('admin.transport.routes')
            ->with('success', 'Transport route created successfully.');
    }

    public function editRoute(TransportRoute $route)
    {
        $transports = Transport::where('status', 'active')->get();
        return view('admin.transport.edit-route', compact('route', 'transports'));
    }

    public function updateRoute(Request $request, TransportRoute $route)
    {
        $request->validate([
            'transport_id' => 'nullable|exists:transports,id',
            'route_name' => 'required|string|max:255',
            'route_code' => 'required|string|max:50|unique:transport_routes,route_code,' . $route->id,
            'description' => 'nullable|string',
            'start_location' => 'required|string|max:255',
            'end_location' => 'required|string|max:255',
            'distance_km' => 'required|numeric|min:0',
            'estimated_duration_minutes' => 'required|integer|min:1',
            'morning_pickup_time' => 'required|date_format:H:i',
            'morning_dropoff_time' => 'required|date_format:H:i',
            'afternoon_pickup_time' => 'required|date_format:H:i',
            'afternoon_dropoff_time' => 'required|date_format:H:i',
            'fare_amount' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'fare_type' => 'required|string|in:monthly,quarterly,semester,annual',
            'max_capacity' => 'required|integer|min:1',
            'status' => 'required|string|in:active,inactive,suspended',
            'route_details' => 'nullable|array',
            'route_details.*' => 'string|max:255',
        ]);

        $routeData = $request->all();
        $routeData['route_details'] = json_encode($request->route_details ?? []);
        $routeData['is_active'] = $request->status === 'active';

        $route->update($routeData);

        return redirect()->route('admin.transport.routes')
            ->with('success', 'Transport route updated successfully.');
    }

    public function destroyRoute(TransportRoute $route)
    {
        $route->delete();

        return redirect()->route('admin.transport.routes')
            ->with('success', 'Transport route deleted successfully.');
    }
}
<?php

namespace App\Http\Controllers\Transport;

use App\Http\Controllers\Controller;
use App\Models\Transport;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Transport::with(['driver', 'routes']);

            // Search functionality
            if ($request->has('search') && $request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('vehicle_number', 'like', '%' . $request->search . '%')
                      ->orWhere('driver_name', 'like', '%' . $request->search . '%');
                });
            }

            // Filter by type
            if ($request->has('type') && $request->type) {
                $query->where('type', $request->type);
            }

            // Filter by status
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            $vehicles = $query->orderBy('created_at', 'desc')->paginate(15);

            return view('transport.vehicles.index', compact('vehicles'));
        } catch (\Exception $e) {
            \Log::error('VehicleController index error: ' . $e->getMessage());
            
            // Fallback data if database issues
            $vehicles = new \Illuminate\Pagination\LengthAwarePaginator(
                collect(),
                0,
                15,
                1,
                ['path' => request()->url()]
            );
            
            return view('transport.vehicles.index', compact('vehicles'));
        }
    }

    public function create()
    {
        try {
            $drivers = Driver::where('status', 'active')->get();
            return view('transport.vehicles.create', compact('drivers'));
        } catch (\Exception $e) {
            \Log::error('VehicleController create error: ' . $e->getMessage());
            $drivers = collect();
            return view('transport.vehicles.create', compact('drivers'));
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'vehicle_number' => 'required|string|max:50|unique:transports,vehicle_number',
                'type' => 'required|in:bus,van,car,truck',
                'capacity' => 'required|integer|min:1|max:100',
                'driver_id' => 'nullable|exists:drivers,id',
                'driver_name' => 'required|string|max:255',
                'driver_phone' => 'required|string|max:20',
                'driver_license' => 'nullable|string|max:50',
                'status' => 'required|in:active,maintenance,inactive',
                'purchase_date' => 'nullable|date',
                'purchase_price' => 'nullable|numeric|min:0',
                'insurance_expiry' => 'nullable|date',
                'next_maintenance_date' => 'nullable|date',
                'description' => 'nullable|string',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $data = $request->all();

            // Handle photo upload
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $filename = time() . '_' . $photo->getClientOriginalName();
                $path = $photo->storeAs('transport/vehicles', $filename, 'public');
                $data['photo'] = $path;
            }

            Transport::create($data);

            return redirect()->route('transport.vehicles.index')
                ->with('success', 'Vehicle added successfully.');
        } catch (\Exception $e) {
            \Log::error('VehicleController store error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to add vehicle. Please try again.')
                ->withInput();
        }
    }

    public function show(Transport $vehicle)
    {
        try {
            $vehicle->load(['driver', 'routes.students.user']);
            
            // Get utilization statistics
            $totalCapacity = $vehicle->capacity;
            $currentPassengers = $vehicle->routes->sum('current_passengers');
            $utilization = $totalCapacity > 0 ? round(($currentPassengers / $totalCapacity) * 100, 1) : 0;

            return view('transport.vehicles.show', compact('vehicle', 'utilization'));
        } catch (\Exception $e) {
            \Log::error('VehicleController show error: ' . $e->getMessage());
            return redirect()->route('transport.vehicles.index')
                ->with('error', 'Vehicle not found.');
        }
    }

    public function edit(Transport $vehicle)
    {
        try {
            $drivers = Driver::where('status', 'active')->get();
            return view('transport.vehicles.edit', compact('vehicle', 'drivers'));
        } catch (\Exception $e) {
            \Log::error('VehicleController edit error: ' . $e->getMessage());
            return redirect()->route('transport.vehicles.index')
                ->with('error', 'Vehicle not found.');
        }
    }

    public function update(Request $request, Transport $vehicle)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'vehicle_number' => 'required|string|max:50|unique:transports,vehicle_number,' . $vehicle->id,
                'type' => 'required|in:bus,van,car,truck',
                'capacity' => 'required|integer|min:1|max:100',
                'driver_id' => 'nullable|exists:drivers,id',
                'driver_name' => 'required|string|max:255',
                'driver_phone' => 'required|string|max:20',
                'driver_license' => 'nullable|string|max:50',
                'status' => 'required|in:active,maintenance,inactive',
                'purchase_date' => 'nullable|date',
                'purchase_price' => 'nullable|numeric|min:0',
                'insurance_expiry' => 'nullable|date',
                'next_maintenance_date' => 'nullable|date',
                'description' => 'nullable|string',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $data = $request->all();

            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo
                if ($vehicle->photo) {
                    Storage::disk('public')->delete($vehicle->photo);
                }
                
                $photo = $request->file('photo');
                $filename = time() . '_' . $photo->getClientOriginalName();
                $path = $photo->storeAs('transport/vehicles', $filename, 'public');
                $data['photo'] = $path;
            }

            $vehicle->update($data);

            return redirect()->route('transport.vehicles.index')
                ->with('success', 'Vehicle updated successfully.');
        } catch (\Exception $e) {
            \Log::error('VehicleController update error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update vehicle. Please try again.')
                ->withInput();
        }
    }

    public function destroy(Transport $vehicle)
    {
        try {
            // Check if vehicle has active routes
            if ($vehicle->routes()->where('is_active', true)->exists()) {
                return redirect()->back()
                    ->with('error', 'Cannot delete vehicle with active routes. Please deactivate routes first.');
            }

            // Delete photo
            if ($vehicle->photo) {
                Storage::disk('public')->delete($vehicle->photo);
            }

            $vehicle->delete();

            return redirect()->route('transport.vehicles.index')
                ->with('success', 'Vehicle deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('VehicleController destroy error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete vehicle. Please try again.');
        }
    }
}

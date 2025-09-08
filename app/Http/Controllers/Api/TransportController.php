<?php

namespace App\Http\Controllers\Api;

use App\Models\TransportRoute;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransportController extends BaseController
{
    public function routes(Request $request): JsonResponse
    {
        try {
            if (!$this->checkPermission('view transport routes')) {
                return $this->forbiddenResponse('You do not have permission to view transport routes');
            }

            $query = TransportRoute::with(['vehicle']);

            if ($search = $this->getSearchQuery($request)) {
                $query->where('name', 'like', "%{$search}%");
            }

            $perPage = $this->getPerPage($request);
            $routes = $query->paginate($perPage);

            return $this->paginatedResponse($routes, 'Transport routes retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve transport routes: ' . $e->getMessage());
        }
    }

    public function storeRoute(Request $request): JsonResponse
    {
        try {
            if (!$this->checkPermission('create transport routes')) {
                return $this->forbiddenResponse('You do not have permission to create transport routes');
            }

            $rules = [
                'name' => 'required|string|max:255',
                'code' => 'required|string|unique:transport_routes,code',
                'description' => 'nullable|string',
                'start_location' => 'required|string|max:255',
                'end_location' => 'required|string|max:255',
                'vehicle_id' => 'nullable|exists:vehicles,id',
                'fare_amount' => 'required|numeric|min:0',
                'currency' => 'required|string|max:3',
                'distance_km' => 'nullable|numeric|min:0',
                'estimated_duration_minutes' => 'nullable|integer|min:1',
                'route_type' => 'required|in:pickup,drop,round_trip',
                'is_active' => 'boolean',
            ];

            $validated = $this->validateRequest($request, $rules);

            $route = TransportRoute::create([
                'name' => $validated['name'],
                'code' => $validated['code'],
                'description' => $validated['description'] ?? null,
                'start_location' => $validated['start_location'],
                'end_location' => $validated['end_location'],
                'vehicle_id' => $validated['vehicle_id'] ?? null,
                'fare_amount' => $validated['fare_amount'],
                'currency' => $validated['currency'],
                'distance_km' => $validated['distance_km'] ?? null,
                'estimated_duration_minutes' => $validated['estimated_duration_minutes'] ?? null,
                'route_type' => $validated['route_type'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            $route->load(['vehicle']);

            return $this->successResponse($route, 'Transport route created successfully', 201);
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to create transport route: ' . $e->getMessage());
        }
    }

    public function showRoute(int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('view transport routes')) {
                return $this->forbiddenResponse('You do not have permission to view transport routes');
            }

            $route = TransportRoute::with(['vehicle'])->find($id);

            if (!$route) {
                return $this->notFoundResponse('Transport route not found');
            }

            return $this->successResponse($route, 'Transport route retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve transport route: ' . $e->getMessage());
        }
    }

    public function updateRoute(Request $request, int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('edit transport routes')) {
                return $this->forbiddenResponse('You do not have permission to edit transport routes');
            }

            $route = TransportRoute::find($id);

            if (!$route) {
                return $this->notFoundResponse('Transport route not found');
            }

            $rules = [
                'name' => 'sometimes|required|string|max:255',
                'code' => 'sometimes|required|string|unique:transport_routes,code,' . $id,
                'description' => 'nullable|string',
                'start_location' => 'sometimes|required|string|max:255',
                'end_location' => 'sometimes|required|string|max:255',
                'vehicle_id' => 'nullable|exists:vehicles,id',
                'fare_amount' => 'sometimes|required|numeric|min:0',
                'currency' => 'sometimes|required|string|max:3',
                'distance_km' => 'nullable|numeric|min:0',
                'estimated_duration_minutes' => 'nullable|integer|min:1',
                'route_type' => 'sometimes|required|in:pickup,drop,round_trip',
                'is_active' => 'boolean',
            ];

            $validated = $this->validateRequest($request, $rules);

            $route->update(array_filter([
                'name' => $validated['name'] ?? null,
                'code' => $validated['code'] ?? null,
                'description' => $validated['description'] ?? null,
                'start_location' => $validated['start_location'] ?? null,
                'end_location' => $validated['end_location'] ?? null,
                'vehicle_id' => $validated['vehicle_id'] ?? null,
                'fare_amount' => $validated['fare_amount'] ?? null,
                'currency' => $validated['currency'] ?? null,
                'distance_km' => $validated['distance_km'] ?? null,
                'estimated_duration_minutes' => $validated['estimated_duration_minutes'] ?? null,
                'route_type' => $validated['route_type'] ?? null,
                'is_active' => $validated['is_active'] ?? null,
            ]));

            $route->load(['vehicle']);

            return $this->successResponse($route, 'Transport route updated successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to update transport route: ' . $e->getMessage());
        }
    }

    public function destroyRoute(int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('delete transport routes')) {
                return $this->forbiddenResponse('You do not have permission to delete transport routes');
            }

            $route = TransportRoute::find($id);

            if (!$route) {
                return $this->notFoundResponse('Transport route not found');
            }

            $route->delete();

            return $this->successResponse(null, 'Transport route deleted successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to delete transport route: ' . $e->getMessage());
        }
    }

    public function vehicles(Request $request): JsonResponse
    {
        try {
            if (!$this->checkPermission('view vehicles')) {
                return $this->forbiddenResponse('You do not have permission to view vehicles');
            }

            $query = Vehicle::query();

            if ($search = $this->getSearchQuery($request)) {
                $query->where(function ($q) use ($search) {
                    $q->where('vehicle_number', 'like', "%{$search}%")
                      ->orWhere('model', 'like', "%{$search}%");
                });
            }

            $perPage = $this->getPerPage($request);
            $vehicles = $query->paginate($perPage);

            return $this->paginatedResponse($vehicles, 'Vehicles retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve vehicles: ' . $e->getMessage());
        }
    }

    public function storeVehicle(Request $request): JsonResponse
    {
        try {
            if (!$this->checkPermission('create vehicles')) {
                return $this->forbiddenResponse('You do not have permission to create vehicles');
            }

            $rules = [
                'vehicle_number' => 'required|string|unique:vehicles,vehicle_number',
                'model' => 'required|string|max:255',
                'make' => 'required|string|max:255',
                'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
                'color' => 'nullable|string|max:100',
                'capacity' => 'required|integer|min:1',
                'driver_name' => 'required|string|max:255',
                'driver_phone' => 'required|string|max:20',
                'driver_license' => 'required|string|max:100',
                'insurance_number' => 'nullable|string|max:100',
                'registration_number' => 'nullable|string|max:100',
                'fuel_type' => 'required|in:petrol,diesel,electric,hybrid',
                'is_active' => 'boolean',
            ];

            $validated = $this->validateRequest($request, $rules);

            $vehicle = Vehicle::create([
                'vehicle_number' => $validated['vehicle_number'],
                'model' => $validated['model'],
                'make' => $validated['make'],
                'year' => $validated['year'],
                'color' => $validated['color'] ?? null,
                'capacity' => $validated['capacity'],
                'driver_name' => $validated['driver_name'],
                'driver_phone' => $validated['driver_phone'],
                'driver_license' => $validated['driver_license'],
                'insurance_number' => $validated['insurance_number'] ?? null,
                'registration_number' => $validated['registration_number'] ?? null,
                'fuel_type' => $validated['fuel_type'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            return $this->successResponse($vehicle, 'Vehicle created successfully', 201);
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to create vehicle: ' . $e->getMessage());
        }
    }

    public function showVehicle(int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('view vehicles')) {
                return $this->forbiddenResponse('You do not have permission to view vehicles');
            }

            $vehicle = Vehicle::find($id);

            if (!$vehicle) {
                return $this->notFoundResponse('Vehicle not found');
            }

            return $this->successResponse($vehicle, 'Vehicle retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve vehicle: ' . $e->getMessage());
        }
    }

    public function updateVehicle(Request $request, int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('edit vehicles')) {
                return $this->forbiddenResponse('You do not have permission to edit vehicles');
            }

            $vehicle = Vehicle::find($id);

            if (!$vehicle) {
                return $this->notFoundResponse('Vehicle not found');
            }

            $rules = [
                'vehicle_number' => 'sometimes|required|string|unique:vehicles,vehicle_number,' . $id,
                'model' => 'sometimes|required|string|max:255',
                'make' => 'sometimes|required|string|max:255',
                'year' => 'sometimes|required|integer|min:1900|max:' . (date('Y') + 1),
                'color' => 'nullable|string|max:100',
                'capacity' => 'sometimes|required|integer|min:1',
                'driver_name' => 'sometimes|required|string|max:255',
                'driver_phone' => 'sometimes|required|string|max:20',
                'driver_license' => 'sometimes|required|string|max:100',
                'insurance_number' => 'nullable|string|max:100',
                'registration_number' => 'nullable|string|max:100',
                'fuel_type' => 'sometimes|required|in:petrol,diesel,electric,hybrid',
                'is_active' => 'boolean',
            ];

            $validated = $this->validateRequest($request, $rules);

            $vehicle->update(array_filter([
                'vehicle_number' => $validated['vehicle_number'] ?? null,
                'model' => $validated['model'] ?? null,
                'make' => $validated['make'] ?? null,
                'year' => $validated['year'] ?? null,
                'color' => $validated['color'] ?? null,
                'capacity' => $validated['capacity'] ?? null,
                'driver_name' => $validated['driver_name'] ?? null,
                'driver_phone' => $validated['driver_phone'] ?? null,
                'driver_license' => $validated['driver_license'] ?? null,
                'insurance_number' => $validated['insurance_number'] ?? null,
                'registration_number' => $validated['registration_number'] ?? null,
                'fuel_type' => $validated['fuel_type'] ?? null,
                'is_active' => $validated['is_active'] ?? null,
            ]));

            return $this->successResponse($vehicle, 'Vehicle updated successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to update vehicle: ' . $e->getMessage());
        }
    }

    public function destroyVehicle(int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('delete vehicles')) {
                return $this->forbiddenResponse('You do not have permission to delete vehicles');
            }

            $vehicle = Vehicle::find($id);

            if (!$vehicle) {
                return $this->notFoundResponse('Vehicle not found');
            }

            $vehicle->delete();

            return $this->successResponse(null, 'Vehicle deleted successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to delete vehicle: ' . $e->getMessage());
        }
    }

    public function statistics(): JsonResponse
    {
        try {
            if (!$this->checkPermission('view transport reports')) {
                return $this->forbiddenResponse('You do not have permission to view transport reports');
            }

            $stats = [
                'total_routes' => TransportRoute::count(),
                'active_routes' => TransportRoute::where('is_active', true)->count(),
                'total_vehicles' => Vehicle::count(),
                'active_vehicles' => Vehicle::where('is_active', true)->count(),
                'routes_by_type' => TransportRoute::selectRaw('route_type, count(*) as count')
                    ->groupBy('route_type')
                    ->get(),
                'vehicles_by_fuel_type' => Vehicle::selectRaw('fuel_type, count(*) as count')
                    ->groupBy('fuel_type')
                    ->get(),
            ];

            return $this->successResponse($stats, 'Transport statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve transport statistics: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Models\HostelRoom;
use App\Models\RoomType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HostelController extends BaseController
{
    public function rooms(Request $request): JsonResponse
    {
        try {
            if (!$this->checkPermission('view hostel rooms')) {
                return $this->forbiddenResponse('You do not have permission to view hostel rooms');
            }

            $query = HostelRoom::with(['roomType']);

            if ($search = $this->getSearchQuery($request)) {
                $query->where(function ($q) use ($search) {
                    $q->where('room_number', 'like', "%{$search}%")
                      ->orWhere('building', 'like', "%{$search}%");
                });
            }

            $perPage = $this->getPerPage($request);
            $rooms = $query->paginate($perPage);

            return $this->paginatedResponse($rooms, 'Hostel rooms retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve hostel rooms: ' . $e->getMessage());
        }
    }

    public function storeRoom(Request $request): JsonResponse
    {
        try {
            if (!$this->checkPermission('create hostel rooms')) {
                return $this->forbiddenResponse('You do not have permission to create hostel rooms');
            }

            $rules = [
                'room_number' => 'required|string|unique:hostel_rooms,room_number',
                'room_type_id' => 'required|exists:room_types,id',
                'building' => 'required|string|max:100',
                'floor' => 'required|string|max:20',
                'wing' => 'nullable|string|max:50',
                'capacity' => 'required|integer|min:1',
                'current_occupancy' => 'required|integer|min:0',
                'rent_amount' => 'required|numeric|min:0',
                'currency' => 'required|string|max:3',
                'facilities' => 'nullable|array',
                'facilities.*' => 'string',
                'description' => 'nullable|string',
                'is_active' => 'boolean',
            ];

            $validated = $this->validateRequest($request, $rules);

            $room = HostelRoom::create([
                'room_number' => $validated['room_number'],
                'room_type_id' => $validated['room_type_id'],
                'building' => $validated['building'],
                'floor' => $validated['floor'],
                'wing' => $validated['wing'] ?? null,
                'capacity' => $validated['capacity'],
                'current_occupancy' => $validated['current_occupancy'],
                'rent_amount' => $validated['rent_amount'],
                'currency' => $validated['currency'],
                'facilities' => $validated['facilities'] ?? [],
                'description' => $validated['description'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            $room->load(['roomType']);

            return $this->successResponse($room, 'Hostel room created successfully', 201);
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to create hostel room: ' . $e->getMessage());
        }
    }

    public function showRoom(int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('view hostel rooms')) {
                return $this->forbiddenResponse('You do not have permission to view hostel rooms');
            }

            $room = HostelRoom::with(['roomType'])->find($id);

            if (!$room) {
                return $this->notFoundResponse('Hostel room not found');
            }

            return $this->successResponse($room, 'Hostel room retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve hostel room: ' . $e->getMessage());
        }
    }

    public function updateRoom(Request $request, int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('edit hostel rooms')) {
                return $this->forbiddenResponse('You do not have permission to edit hostel rooms');
            }

            $room = HostelRoom::find($id);

            if (!$room) {
                return $this->notFoundResponse('Hostel room not found');
            }

            $rules = [
                'room_number' => 'sometimes|required|string|unique:hostel_rooms,room_number,' . $id,
                'room_type_id' => 'sometimes|required|exists:room_types,id',
                'building' => 'sometimes|required|string|max:100',
                'floor' => 'sometimes|required|string|max:20',
                'wing' => 'nullable|string|max:50',
                'capacity' => 'sometimes|required|integer|min:1',
                'current_occupancy' => 'sometimes|required|integer|min:0',
                'rent_amount' => 'sometimes|required|numeric|min:0',
                'currency' => 'sometimes|required|string|max:3',
                'facilities' => 'nullable|array',
                'facilities.*' => 'string',
                'description' => 'nullable|string',
                'is_active' => 'boolean',
            ];

            $validated = $this->validateRequest($request, $rules);

            $room->update(array_filter([
                'room_number' => $validated['room_number'] ?? null,
                'room_type_id' => $validated['room_type_id'] ?? null,
                'building' => $validated['building'] ?? null,
                'floor' => $validated['floor'] ?? null,
                'wing' => $validated['wing'] ?? null,
                'capacity' => $validated['capacity'] ?? null,
                'current_occupancy' => $validated['current_occupancy'] ?? null,
                'rent_amount' => $validated['rent_amount'] ?? null,
                'currency' => $validated['currency'] ?? null,
                'facilities' => $validated['facilities'] ?? null,
                'description' => $validated['description'] ?? null,
                'is_active' => $validated['is_active'] ?? null,
            ]));

            $room->load(['roomType']);

            return $this->successResponse($room, 'Hostel room updated successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to update hostel room: ' . $e->getMessage());
        }
    }

    public function destroyRoom(int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('delete hostel rooms')) {
                return $this->forbiddenResponse('You do not have permission to delete hostel rooms');
            }

            $room = HostelRoom::find($id);

            if (!$room) {
                return $this->notFoundResponse('Hostel room not found');
            }

            $room->delete();

            return $this->successResponse(null, 'Hostel room deleted successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to delete hostel room: ' . $e->getMessage());
        }
    }

    public function roomTypes(Request $request): JsonResponse
    {
        try {
            if (!$this->checkPermission('view hostel rooms')) {
                return $this->forbiddenResponse('You do not have permission to view hostel rooms');
            }

            $roomTypes = RoomType::all();

            return $this->successResponse($roomTypes, 'Room types retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve room types: ' . $e->getMessage());
        }
    }

    public function statistics(): JsonResponse
    {
        try {
            if (!$this->checkPermission('view hostel reports')) {
                return $this->forbiddenResponse('You do not have permission to view hostel reports');
            }

            $stats = [
                'total_rooms' => HostelRoom::count(),
                'active_rooms' => HostelRoom::where('is_active', true)->count(),
                'total_room_types' => RoomType::count(),
                'total_capacity' => HostelRoom::sum('capacity'),
                'total_occupancy' => HostelRoom::sum('current_occupancy'),
                'available_beds' => HostelRoom::sum('capacity') - HostelRoom::sum('current_occupancy'),
                'rooms_by_building' => HostelRoom::selectRaw('building, count(*) as count')
                    ->groupBy('building')
                    ->get(),
                'rooms_by_type' => HostelRoom::with('roomType')
                    ->selectRaw('room_type_id, count(*) as count')
                    ->groupBy('room_type_id')
                    ->get(),
            ];

            return $this->successResponse($stats, 'Hostel statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve hostel statistics: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClassController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        try {
            if (!$this->checkPermission('view classes')) {
                return $this->forbiddenResponse('You do not have permission to view classes');
            }

            $query = ClassRoom::with(['classTeacher']);

            if ($search = $this->getSearchQuery($request)) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            }

            $perPage = $this->getPerPage($request);
            $classes = $query->paginate($perPage);

            return $this->paginatedResponse($classes, 'Classes retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve classes: ' . $e->getMessage());
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            if (!$this->checkPermission('create classes')) {
                return $this->forbiddenResponse('You do not have permission to create classes');
            }

            $rules = [
                'name' => 'required|string|max:255',
                'code' => 'required|string|unique:class_rooms,code',
                'description' => 'nullable|string',
                'capacity' => 'required|integer|min:1',
                'class_teacher_id' => 'nullable|exists:users,id',
                'room_number' => 'nullable|string|max:50',
                'building' => 'nullable|string|max:100',
                'floor' => 'nullable|string|max:20',
                'wing' => 'nullable|string|max:50',
            ];

            $validated = $this->validateRequest($request, $rules);

            $class = ClassRoom::create([
                'name' => $validated['name'],
                'code' => $validated['code'],
                'description' => $validated['description'] ?? null,
                'capacity' => $validated['capacity'],
                'class_teacher_id' => $validated['class_teacher_id'] ?? null,
                'room_number' => $validated['room_number'] ?? null,
                'building' => $validated['building'] ?? null,
                'floor' => $validated['floor'] ?? null,
                'wing' => $validated['wing'] ?? null,
                'status' => 'active',
                'display_order' => 0,
            ]);

            $class->load(['classTeacher']);

            return $this->successResponse($class, 'Class created successfully', 201);
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to create class: ' . $e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('view classes')) {
                return $this->forbiddenResponse('You do not have permission to view classes');
            }

            $class = ClassRoom::with(['classTeacher'])->find($id);

            if (!$class) {
                return $this->notFoundResponse('Class not found');
            }

            return $this->successResponse($class, 'Class retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve class: ' . $e->getMessage());
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('edit classes')) {
                return $this->forbiddenResponse('You do not have permission to edit classes');
            }

            $class = ClassRoom::find($id);

            if (!$class) {
                return $this->notFoundResponse('Class not found');
            }

            $rules = [
                'name' => 'sometimes|required|string|max:255',
                'code' => 'sometimes|required|string|unique:class_rooms,code,' . $id,
                'description' => 'nullable|string',
                'capacity' => 'sometimes|required|integer|min:1',
                'class_teacher_id' => 'nullable|exists:users,id',
                'room_number' => 'nullable|string|max:50',
                'building' => 'nullable|string|max:100',
                'floor' => 'nullable|string|max:20',
                'wing' => 'nullable|string|max:50',
            ];

            $validated = $this->validateRequest($request, $rules);

            $class->update(array_filter([
                'name' => $validated['name'] ?? null,
                'code' => $validated['code'] ?? null,
                'description' => $validated['description'] ?? null,
                'capacity' => $validated['capacity'] ?? null,
                'class_teacher_id' => $validated['class_teacher_id'] ?? null,
                'room_number' => $validated['room_number'] ?? null,
                'building' => $validated['building'] ?? null,
                'floor' => $validated['floor'] ?? null,
                'wing' => $validated['wing'] ?? null,
            ]));

            $class->load(['classTeacher']);

            return $this->successResponse($class, 'Class updated successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to update class: ' . $e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('delete classes')) {
                return $this->forbiddenResponse('You do not have permission to delete classes');
            }

            $class = ClassRoom::find($id);

            if (!$class) {
                return $this->notFoundResponse('Class not found');
            }

            $class->delete();

            return $this->successResponse(null, 'Class deleted successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to delete class: ' . $e->getMessage());
        }
    }

    public function statistics(): JsonResponse
    {
        try {
            if (!$this->checkPermission('view classes')) {
                return $this->forbiddenResponse('You do not have permission to view class statistics');
            }

            $stats = [
                'total_classes' => ClassRoom::count(),
                'active_classes' => ClassRoom::where('status', 'active')->count(),
                'classes_by_building' => ClassRoom::selectRaw('building, count(*) as count')
                    ->whereNotNull('building')
                    ->groupBy('building')
                    ->get(),
            ];

            return $this->successResponse($stats, 'Class statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve class statistics: ' . $e->getMessage());
        }
    }
}

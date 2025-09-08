<?php

namespace App\Http\Controllers\Api;

use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubjectController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        try {
            if (!$this->checkPermission('view subjects')) {
                return $this->forbiddenResponse('You do not have permission to view subjects');
            }

            $query = Subject::with(['department']);

            if ($search = $this->getSearchQuery($request)) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            }

            $perPage = $this->getPerPage($request);
            $subjects = $query->paginate($perPage);

            return $this->paginatedResponse($subjects, 'Subjects retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve subjects: ' . $e->getMessage());
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            if (!$this->checkPermission('create subjects')) {
                return $this->forbiddenResponse('You do not have permission to create subjects');
            }

            $rules = [
                'name' => 'required|string|max:255',
                'code' => 'required|string|unique:subjects,code',
                'description' => 'nullable|string',
                'department_id' => 'nullable|exists:departments,id',
                'credits' => 'required|integer|min:1',
                'hours_per_week' => 'required|integer|min:1',
                'is_compulsory' => 'boolean',
                'is_elective' => 'boolean',
            ];

            $validated = $this->validateRequest($request, $rules);

            $subject = Subject::create([
                'name' => $validated['name'],
                'code' => $validated['code'],
                'description' => $validated['description'] ?? null,
                'department_id' => $validated['department_id'] ?? null,
                'credits' => $validated['credits'],
                'hours_per_week' => $validated['hours_per_week'],
                'is_compulsory' => $validated['is_compulsory'] ?? false,
                'is_elective' => $validated['is_elective'] ?? false,
                'status' => 'active',
                'display_order' => 0,
            ]);

            $subject->load(['department']);

            return $this->successResponse($subject, 'Subject created successfully', 201);
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to create subject: ' . $e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('view subjects')) {
                return $this->forbiddenResponse('You do not have permission to view subjects');
            }

            $subject = Subject::with(['department'])->find($id);

            if (!$subject) {
                return $this->notFoundResponse('Subject not found');
            }

            return $this->successResponse($subject, 'Subject retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve subject: ' . $e->getMessage());
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('edit subjects')) {
                return $this->forbiddenResponse('You do not have permission to edit subjects');
            }

            $subject = Subject::find($id);

            if (!$subject) {
                return $this->notFoundResponse('Subject not found');
            }

            $rules = [
                'name' => 'sometimes|required|string|max:255',
                'code' => 'sometimes|required|string|unique:subjects,code,' . $id,
                'description' => 'nullable|string',
                'department_id' => 'nullable|exists:departments,id',
                'credits' => 'sometimes|required|integer|min:1',
                'hours_per_week' => 'sometimes|required|integer|min:1',
                'is_compulsory' => 'boolean',
                'is_elective' => 'boolean',
            ];

            $validated = $this->validateRequest($request, $rules);

            $subject->update(array_filter([
                'name' => $validated['name'] ?? null,
                'code' => $validated['code'] ?? null,
                'description' => $validated['description'] ?? null,
                'department_id' => $validated['department_id'] ?? null,
                'credits' => $validated['credits'] ?? null,
                'hours_per_week' => $validated['hours_per_week'] ?? null,
                'is_compulsory' => $validated['is_compulsory'] ?? null,
                'is_elective' => $validated['is_elective'] ?? null,
            ]));

            $subject->load(['department']);

            return $this->successResponse($subject, 'Subject updated successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to update subject: ' . $e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('delete subjects')) {
                return $this->forbiddenResponse('You do not have permission to delete subjects');
            }

            $subject = Subject::find($id);

            if (!$subject) {
                return $this->notFoundResponse('Subject not found');
            }

            $subject->delete();

            return $this->successResponse(null, 'Subject deleted successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to delete subject: ' . $e->getMessage());
        }
    }

    public function statistics(): JsonResponse
    {
        try {
            if (!$this->checkPermission('view subjects')) {
                return $this->forbiddenResponse('You do not have permission to view subject statistics');
            }

            $stats = [
                'total_subjects' => Subject::count(),
                'active_subjects' => Subject::where('status', 'active')->count(),
                'compulsory_subjects' => Subject::where('is_compulsory', true)->count(),
                'elective_subjects' => Subject::where('is_elective', true)->count(),
                'subjects_by_department' => Subject::with('department')
                    ->selectRaw('department_id, count(*) as count')
                    ->groupBy('department_id')
                    ->get(),
            ];

            return $this->successResponse($stats, 'Subject statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve subject statistics: ' . $e->getMessage());
        }
    }
}

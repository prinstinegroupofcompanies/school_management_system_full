<?php

namespace App\Http\Controllers\Api;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TeacherController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        try {
            if (!$this->checkPermission('view teachers')) {
                return $this->forbiddenResponse('You do not have permission to view teachers');
            }

            $query = Teacher::with(['user', 'department', 'designation']);

            if ($search = $this->getSearchQuery($request)) {
                $query->where(function ($q) use ($search) {
                    $q->where('employee_id', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%");
                      });
                });
            }

            $perPage = $this->getPerPage($request);
            $teachers = $query->paginate($perPage);

            return $this->paginatedResponse($teachers, 'Teachers retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve teachers: ' . $e->getMessage());
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            if (!$this->checkPermission('create teachers')) {
                return $this->forbiddenResponse('You do not have permission to create teachers');
            }

            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'employee_id' => 'required|string|unique:teachers,employee_id',
                'department_id' => 'nullable|exists:departments,id',
                'designation_id' => 'nullable|exists:designations,id',
                'joining_date' => 'required|date',
                'basic_salary' => 'required|numeric|min:0',
            ];

            $validated = $this->validateRequest($request, $rules);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'status' => 'active',
            ]);

            $user->assignRole('teacher');

            $teacher = Teacher::create([
                'user_id' => $user->id,
                'employee_id' => $validated['employee_id'],
                'department_id' => $validated['department_id'] ?? null,
                'designation_id' => $validated['designation_id'] ?? null,
                'joining_date' => $validated['joining_date'],
                'basic_salary' => $validated['basic_salary'],
                'employment_type' => 'full_time',
                'employment_status' => 'active',
                'currency' => 'LRD',
            ]);

            $teacher->load(['user', 'department', 'designation']);

            return $this->successResponse($teacher, 'Teacher created successfully', 201);
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to create teacher: ' . $e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('view teachers')) {
                return $this->forbiddenResponse('You do not have permission to view teachers');
            }

            $teacher = Teacher::with(['user', 'department', 'designation'])->find($id);

            if (!$teacher) {
                return $this->notFoundResponse('Teacher not found');
            }

            return $this->successResponse($teacher, 'Teacher retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve teacher: ' . $e->getMessage());
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('edit teachers')) {
                return $this->forbiddenResponse('You do not have permission to edit teachers');
            }

            $teacher = Teacher::find($id);

            if (!$teacher) {
                return $this->notFoundResponse('Teacher not found');
            }

            $rules = [
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|unique:users,email,' . $teacher->user_id,
                'department_id' => 'nullable|exists:departments,id',
                'designation_id' => 'nullable|exists:designations,id',
                'basic_salary' => 'sometimes|required|numeric|min:0',
            ];

            $validated = $this->validateRequest($request, $rules);

            if (isset($validated['name']) || isset($validated['email'])) {
                $teacher->user->update(array_filter([
                    'name' => $validated['name'] ?? null,
                    'email' => $validated['email'] ?? null,
                ]));
            }

            if (isset($validated['department_id']) || isset($validated['designation_id']) || isset($validated['basic_salary'])) {
                $teacher->update(array_filter([
                    'department_id' => $validated['department_id'] ?? null,
                    'designation_id' => $validated['designation_id'] ?? null,
                    'basic_salary' => $validated['basic_salary'] ?? null,
                ]));
            }

            $teacher->load(['user', 'department', 'designation']);

            return $this->successResponse($teacher, 'Teacher updated successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to update teacher: ' . $e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('delete teachers')) {
                return $this->forbiddenResponse('You do not have permission to delete teachers');
            }

            $teacher = Teacher::find($id);

            if (!$teacher) {
                return $this->notFoundResponse('Teacher not found');
            }

            $teacher->user->delete();

            return $this->successResponse(null, 'Teacher deleted successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to delete teacher: ' . $e->getMessage());
        }
    }

    public function statistics(): JsonResponse
    {
        try {
            if (!$this->checkPermission('view teachers')) {
                return $this->forbiddenResponse('You do not have permission to view teacher statistics');
            }

            $stats = [
                'total_teachers' => Teacher::count(),
                'active_teachers' => Teacher::where('employment_status', 'active')->count(),
                'teachers_by_department' => Teacher::with('department')
                    ->selectRaw('department_id, count(*) as count')
                    ->groupBy('department_id')
                    ->get(),
            ];

            return $this->successResponse($stats, 'Teacher statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve teacher statistics: ' . $e->getMessage());
        }
    }
}

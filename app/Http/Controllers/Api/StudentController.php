<?php

namespace App\Http\Controllers\Api;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        try {
            if (!$this->checkPermission('view students')) {
                return $this->forbiddenResponse('You do not have permission to view students');
            }

            $query = Student::with(['user', 'classRoom', 'section']);

            if ($search = $this->getSearchQuery($request)) {
                $query->where(function ($q) use ($search) {
                    $q->where('admission_no', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%");
                      });
                });
            }

            $perPage = $this->getPerPage($request);
            $students = $query->paginate($perPage);

            return $this->paginatedResponse($students, 'Students retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve students: ' . $e->getMessage());
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            if (!$this->checkPermission('create students')) {
                return $this->forbiddenResponse('You do not have permission to create students');
            }

            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'admission_no' => 'required|string|unique:students,admission_no',
                'class_id' => 'required|exists:class_rooms,id',
            ];

            $validated = $this->validateRequest($request, $rules);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'status' => 'active',
            ]);

            $user->assignRole('student');

            $student = Student::create([
                'user_id' => $user->id,
                'admission_no' => $validated['admission_no'],
                'class_id' => $validated['class_id'],
                'status' => 'active',
            ]);

            $student->load(['user', 'classRoom', 'section']);

            return $this->successResponse($student, 'Student created successfully', 201);
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to create student: ' . $e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('view students')) {
                return $this->forbiddenResponse('You do not have permission to view students');
            }

            $student = Student::with(['user', 'classRoom', 'section'])->find($id);

            if (!$student) {
                return $this->notFoundResponse('Student not found');
            }

            return $this->successResponse($student, 'Student retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve student: ' . $e->getMessage());
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('edit students')) {
                return $this->forbiddenResponse('You do not have permission to edit students');
            }

            $student = Student::find($id);

            if (!$student) {
                return $this->notFoundResponse('Student not found');
            }

            $rules = [
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|unique:users,email,' . $student->user_id,
                'class_id' => 'sometimes|required|exists:class_rooms,id',
            ];

            $validated = $this->validateRequest($request, $rules);

            if (isset($validated['name']) || isset($validated['email'])) {
                $student->user->update(array_filter([
                    'name' => $validated['name'] ?? null,
                    'email' => $validated['email'] ?? null,
                ]));
            }

            if (isset($validated['class_id'])) {
                $student->update(['class_id' => $validated['class_id']]);
            }

            $student->load(['user', 'classRoom', 'section']);

            return $this->successResponse($student, 'Student updated successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to update student: ' . $e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('delete students')) {
                return $this->forbiddenResponse('You do not have permission to delete students');
            }

            $student = Student::find($id);

            if (!$student) {
                return $this->notFoundResponse('Student not found');
            }

            $student->user->delete();

            return $this->successResponse(null, 'Student deleted successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to delete student: ' . $e->getMessage());
        }
    }

    public function statistics(): JsonResponse
    {
        try {
            if (!$this->checkPermission('view students')) {
                return $this->forbiddenResponse('You do not have permission to view student statistics');
            }

            $stats = [
                'total_students' => Student::count(),
                'active_students' => Student::where('status', 'active')->count(),
                'students_by_class' => Student::with('classRoom')
                    ->selectRaw('class_id, count(*) as count')
                    ->groupBy('class_id')
                    ->get(),
            ];

            return $this->successResponse($stats, 'Student statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve student statistics: ' . $e->getMessage());
        }
    }
}

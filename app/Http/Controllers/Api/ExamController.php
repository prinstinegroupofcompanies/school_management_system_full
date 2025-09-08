<?php

namespace App\Http\Controllers\Api;

use App\Models\ExamType;
use App\Models\ExamSchedule;
use App\Models\ExamMark;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExamController extends BaseController
{
    public function types(Request $request): JsonResponse
    {
        try {
            if (!$this->checkPermission('view exams')) {
                return $this->forbiddenResponse('You do not have permission to view exams');
            }

            $query = ExamType::query();

            if ($search = $this->getSearchQuery($request)) {
                $query->where('name', 'like', "%{$search}%");
            }

            $perPage = $this->getPerPage($request);
            $types = $query->paginate($perPage);

            return $this->paginatedResponse($types, 'Exam types retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve exam types: ' . $e->getMessage());
        }
    }

    public function storeType(Request $request): JsonResponse
    {
        try {
            if (!$this->checkPermission('create exams')) {
                return $this->forbiddenResponse('You do not have permission to create exams');
            }

            $rules = [
                'name' => 'required|string|max:255',
                'code' => 'required|string|unique:exam_types,code',
                'description' => 'nullable|string',
                'total_marks' => 'required|integer|min:1',
                'pass_marks' => 'required|integer|min:1',
                'duration_minutes' => 'required|integer|min:1',
                'is_active' => 'boolean',
            ];

            $validated = $this->validateRequest($request, $rules);

            $type = ExamType::create([
                'name' => $validated['name'],
                'code' => $validated['code'],
                'description' => $validated['description'] ?? null,
                'total_marks' => $validated['total_marks'],
                'pass_marks' => $validated['pass_marks'],
                'duration_minutes' => $validated['duration_minutes'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            return $this->successResponse($type, 'Exam type created successfully', 201);
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to create exam type: ' . $e->getMessage());
        }
    }

    public function showType(int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('view exams')) {
                return $this->forbiddenResponse('You do not have permission to view exams');
            }

            $type = ExamType::find($id);

            if (!$type) {
                return $this->notFoundResponse('Exam type not found');
            }

            return $this->successResponse($type, 'Exam type retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve exam type: ' . $e->getMessage());
        }
    }

    public function updateType(Request $request, int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('edit exams')) {
                return $this->forbiddenResponse('You do not have permission to edit exams');
            }

            $type = ExamType::find($id);

            if (!$type) {
                return $this->notFoundResponse('Exam type not found');
            }

            $rules = [
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'total_marks' => 'sometimes|required|integer|min:1',
                'pass_marks' => 'sometimes|required|integer|min:1',
                'duration_minutes' => 'sometimes|required|integer|min:1',
                'is_active' => 'boolean',
            ];

            $validated = $this->validateRequest($request, $rules);

            $type->update(array_filter([
                'name' => $validated['name'] ?? null,
                'description' => $validated['description'] ?? null,
                'total_marks' => $validated['total_marks'] ?? null,
                'pass_marks' => $validated['pass_marks'] ?? null,
                'duration_minutes' => $validated['duration_minutes'] ?? null,
                'is_active' => $validated['is_active'] ?? null,
            ]));

            return $this->successResponse($type, 'Exam type updated successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to update exam type: ' . $e->getMessage());
        }
    }

    public function destroyType(int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('delete exams')) {
                return $this->forbiddenResponse('You do not have permission to delete exams');
            }

            $type = ExamType::find($id);

            if (!$type) {
                return $this->notFoundResponse('Exam type not found');
            }

            $type->delete();

            return $this->successResponse(null, 'Exam type deleted successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to delete exam type: ' . $e->getMessage());
        }
    }

    public function schedules(Request $request): JsonResponse
    {
        try {
            if (!$this->checkPermission('view exams')) {
                return $this->forbiddenResponse('You do not have permission to view exams');
            }

            $query = ExamSchedule::with(['examType', 'classRoom', 'subject']);

            if ($search = $this->getSearchQuery($request)) {
                $query->where('title', 'like', "%{$search}%");
            }

            $perPage = $this->getPerPage($request);
            $schedules = $query->paginate($perPage);

            return $this->paginatedResponse($schedules, 'Exam schedules retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve exam schedules: ' . $e->getMessage());
        }
    }

    public function storeSchedule(Request $request): JsonResponse
    {
        try {
            if (!$this->checkPermission('create exams')) {
                return $this->forbiddenResponse('You do not have permission to create exams');
            }

            $rules = [
                'title' => 'required|string|max:255',
                'exam_type_id' => 'required|exists:exam_types,id',
                'class_id' => 'required|exists:class_rooms,id',
                'subject_id' => 'required|exists:subjects,id',
                'exam_date' => 'required|date',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'room_number' => 'nullable|string|max:50',
                'instructions' => 'nullable|string',
            ];

            $validated = $this->validateRequest($request, $rules);

            $schedule = ExamSchedule::create([
                'title' => $validated['title'],
                'exam_type_id' => $validated['exam_type_id'],
                'class_id' => $validated['class_id'],
                'subject_id' => $validated['subject_id'],
                'exam_date' => $validated['exam_date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'room_number' => $validated['room_number'] ?? null,
                'instructions' => $validated['instructions'] ?? null,
                'status' => 'scheduled',
            ]);

            $schedule->load(['examType', 'classRoom', 'subject']);

            return $this->successResponse($schedule, 'Exam schedule created successfully', 201);
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to create exam schedule: ' . $e->getMessage());
        }
    }

    public function showSchedule(int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('view exams')) {
                return $this->forbiddenResponse('You do not have permission to view exams');
            }

            $schedule = ExamSchedule::with(['examType', 'classRoom', 'subject'])->find($id);

            if (!$schedule) {
                return $this->notFoundResponse('Exam schedule not found');
            }

            return $this->successResponse($schedule, 'Exam schedule retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve exam schedule: ' . $e->getMessage());
        }
    }

    public function updateSchedule(Request $request, int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('edit exams')) {
                return $this->forbiddenResponse('You do not have permission to edit exams');
            }

            $schedule = ExamSchedule::find($id);

            if (!$schedule) {
                return $this->notFoundResponse('Exam schedule not found');
            }

            $rules = [
                'title' => 'sometimes|required|string|max:255',
                'exam_date' => 'sometimes|required|date',
                'start_time' => 'sometimes|required|date_format:H:i',
                'end_time' => 'sometimes|required|date_format:H:i|after:start_time',
                'room_number' => 'nullable|string|max:50',
                'instructions' => 'nullable|string',
                'status' => 'sometimes|required|in:scheduled,ongoing,completed,cancelled',
            ];

            $validated = $this->validateRequest($request, $rules);

            $schedule->update(array_filter([
                'title' => $validated['title'] ?? null,
                'exam_date' => $validated['exam_date'] ?? null,
                'start_time' => $validated['start_time'] ?? null,
                'end_time' => $validated['end_time'] ?? null,
                'room_number' => $validated['room_number'] ?? null,
                'instructions' => $validated['instructions'] ?? null,
                'status' => $validated['status'] ?? null,
            ]));

            $schedule->load(['examType', 'classRoom', 'subject']);

            return $this->successResponse($schedule, 'Exam schedule updated successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to update exam schedule: ' . $e->getMessage());
        }
    }

    public function destroySchedule(int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('delete exams')) {
                return $this->forbiddenResponse('You do not have permission to delete exams');
            }

            $schedule = ExamSchedule::find($id);

            if (!$schedule) {
                return $this->notFoundResponse('Exam schedule not found');
            }

            $schedule->delete();

            return $this->successResponse(null, 'Exam schedule deleted successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to delete exam schedule: ' . $e->getMessage());
        }
    }

    public function statistics(): JsonResponse
    {
        try {
            if (!$this->checkPermission('view exams')) {
                return $this->forbiddenResponse('You do not have permission to view exam statistics');
            }

            $stats = [
                'total_types' => ExamType::count(),
                'total_schedules' => ExamSchedule::count(),
                'total_marks' => ExamMark::count(),
                'scheduled_exams' => ExamSchedule::where('status', 'scheduled')->count(),
                'completed_exams' => ExamSchedule::where('status', 'completed')->count(),
            ];

            return $this->successResponse($stats, 'Exam statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve exam statistics: ' . $e->getMessage());
        }
    }
}

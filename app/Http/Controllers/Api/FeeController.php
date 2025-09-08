<?php

namespace App\Http\Controllers\Api;

use App\Models\FeeStructure;
use App\Models\FeePayment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeeController extends BaseController
{
    public function structures(Request $request): JsonResponse
    {
        try {
            if (!$this->checkPermission('view fees')) {
                return $this->forbiddenResponse('You do not have permission to view fees');
            }

            $query = FeeStructure::with(['classRoom']);

            if ($search = $this->getSearchQuery($request)) {
                $query->where('name', 'like', "%{$search}%");
            }

            $perPage = $this->getPerPage($request);
            $structures = $query->paginate($perPage);

            return $this->paginatedResponse($structures, 'Fee structures retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve fee structures: ' . $e->getMessage());
        }
    }

    public function storeStructure(Request $request): JsonResponse
    {
        try {
            if (!$this->checkPermission('create fees')) {
                return $this->forbiddenResponse('You do not have permission to create fees');
            }

            $rules = [
                'name' => 'required|string|max:255',
                'class_id' => 'required|exists:class_rooms,id',
                'academic_year' => 'required|string|max:20',
                'fee_type' => 'required|in:tuition,transport,hostel,library,other',
                'amount' => 'required|numeric|min:0',
                'currency' => 'required|string|max:3',
                'frequency' => 'required|in:monthly,quarterly,annually,one_time',
                'due_date' => 'required|date',
                'description' => 'nullable|string',
            ];

            $validated = $this->validateRequest($request, $rules);

            $structure = FeeStructure::create([
                'name' => $validated['name'],
                'class_id' => $validated['class_id'],
                'academic_year' => $validated['academic_year'],
                'fee_type' => $validated['fee_type'],
                'amount' => $validated['amount'],
                'currency' => $validated['currency'],
                'frequency' => $validated['frequency'],
                'due_date' => $validated['due_date'],
                'description' => $validated['description'] ?? null,
                'status' => 'active',
            ]);

            $structure->load(['classRoom']);

            return $this->successResponse($structure, 'Fee structure created successfully', 201);
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to create fee structure: ' . $e->getMessage());
        }
    }

    public function showStructure(int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('view fees')) {
                return $this->forbiddenResponse('You do not have permission to view fees');
            }

            $structure = FeeStructure::with(['classRoom'])->find($id);

            if (!$structure) {
                return $this->notFoundResponse('Fee structure not found');
            }

            return $this->successResponse($structure, 'Fee structure retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve fee structure: ' . $e->getMessage());
        }
    }

    public function updateStructure(Request $request, int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('edit fees')) {
                return $this->forbiddenResponse('You do not have permission to edit fees');
            }

            $structure = FeeStructure::find($id);

            if (!$structure) {
                return $this->notFoundResponse('Fee structure not found');
            }

            $rules = [
                'name' => 'sometimes|required|string|max:255',
                'amount' => 'sometimes|required|numeric|min:0',
                'due_date' => 'sometimes|required|date',
                'description' => 'nullable|string',
            ];

            $validated = $this->validateRequest($request, $rules);

            $structure->update(array_filter([
                'name' => $validated['name'] ?? null,
                'amount' => $validated['amount'] ?? null,
                'due_date' => $validated['due_date'] ?? null,
                'description' => $validated['description'] ?? null,
            ]));

            $structure->load(['classRoom']);

            return $this->successResponse($structure, 'Fee structure updated successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to update fee structure: ' . $e->getMessage());
        }
    }

    public function destroyStructure(int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('delete fees')) {
                return $this->forbiddenResponse('You do not have permission to delete fees');
            }

            $structure = FeeStructure::find($id);

            if (!$structure) {
                return $this->notFoundResponse('Fee structure not found');
            }

            $structure->delete();

            return $this->successResponse(null, 'Fee structure deleted successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to delete fee structure: ' . $e->getMessage());
        }
    }

    public function payments(Request $request): JsonResponse
    {
        try {
            if (!$this->checkPermission('view fee payments')) {
                return $this->forbiddenResponse('You do not have permission to view fee payments');
            }

            $query = FeePayment::with(['student.user', 'feeStructure']);

            if ($search = $this->getSearchQuery($request)) {
                $query->whereHas('student.user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            }

            $perPage = $this->getPerPage($request);
            $payments = $query->paginate($perPage);

            return $this->paginatedResponse($payments, 'Fee payments retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve fee payments: ' . $e->getMessage());
        }
    }

    public function storePayment(Request $request): JsonResponse
    {
        try {
            if (!$this->checkPermission('manage fee payments')) {
                return $this->forbiddenResponse('You do not have permission to manage fee payments');
            }

            $rules = [
                'student_id' => 'required|exists:students,id',
                'fee_structure_id' => 'required|exists:fee_structures,id',
                'amount' => 'required|numeric|min:0',
                'payment_date' => 'required|date',
                'payment_method' => 'required|in:cash,check,bank_transfer,online',
                'reference_number' => 'nullable|string|max:100',
                'notes' => 'nullable|string',
            ];

            $validated = $this->validateRequest($request, $rules);

            $payment = FeePayment::create([
                'student_id' => $validated['student_id'],
                'fee_structure_id' => $validated['fee_structure_id'],
                'amount' => $validated['amount'],
                'payment_date' => $validated['payment_date'],
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => 'paid',
            ]);

            $payment->load(['student.user', 'feeStructure']);

            return $this->successResponse($payment, 'Fee payment created successfully', 201);
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to create fee payment: ' . $e->getMessage());
        }
    }

    public function showPayment(int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('view fee payments')) {
                return $this->forbiddenResponse('You do not have permission to view fee payments');
            }

            $payment = FeePayment::with(['student.user', 'feeStructure'])->find($id);

            if (!$payment) {
                return $this->notFoundResponse('Fee payment not found');
            }

            return $this->successResponse($payment, 'Fee payment retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve fee payment: ' . $e->getMessage());
        }
    }

    public function updatePayment(Request $request, int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('manage fee payments')) {
                return $this->forbiddenResponse('You do not have permission to manage fee payments');
            }

            $payment = FeePayment::find($id);

            if (!$payment) {
                return $this->notFoundResponse('Fee payment not found');
            }

            $rules = [
                'amount' => 'sometimes|required|numeric|min:0',
                'payment_date' => 'sometimes|required|date',
                'payment_method' => 'sometimes|required|in:cash,check,bank_transfer,online',
                'reference_number' => 'nullable|string|max:100',
                'notes' => 'nullable|string',
                'status' => 'sometimes|required|in:paid,pending,overdue,cancelled',
            ];

            $validated = $this->validateRequest($request, $rules);

            $payment->update(array_filter([
                'amount' => $validated['amount'] ?? null,
                'payment_date' => $validated['payment_date'] ?? null,
                'payment_method' => $validated['payment_method'] ?? null,
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => $validated['status'] ?? null,
            ]));

            $payment->load(['student.user', 'feeStructure']);

            return $this->successResponse($payment, 'Fee payment updated successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to update fee payment: ' . $e->getMessage());
        }
    }

    public function destroyPayment(int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('manage fee payments')) {
                return $this->forbiddenResponse('You do not have permission to manage fee payments');
            }

            $payment = FeePayment::find($id);

            if (!$payment) {
                return $this->notFoundResponse('Fee payment not found');
            }

            $payment->delete();

            return $this->successResponse(null, 'Fee payment deleted successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to delete fee payment: ' . $e->getMessage());
        }
    }

    public function statistics(): JsonResponse
    {
        try {
            if (!$this->checkPermission('view fees')) {
                return $this->forbiddenResponse('You do not have permission to view fee statistics');
            }

            $stats = [
                'total_structures' => FeeStructure::count(),
                'total_payments' => FeePayment::count(),
                'total_collected' => FeePayment::where('status', 'paid')->sum('amount'),
                'total_pending' => FeePayment::where('status', 'pending')->sum('amount'),
                'payments_by_method' => FeePayment::selectRaw('payment_method, count(*) as count')
                    ->groupBy('payment_method')
                    ->get(),
            ];

            return $this->successResponse($stats, 'Fee statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve fee statistics: ' . $e->getMessage());
        }
    }
}

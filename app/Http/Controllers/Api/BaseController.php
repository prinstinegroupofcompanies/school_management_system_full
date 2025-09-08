<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

abstract class BaseController extends Controller
{
    /**
     * Success response.
     */
    protected function successResponse($data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toISOString(),
        ], $code);
    }

    /**
     * Error response.
     */
    protected function errorResponse(string $message = 'Error', int $code = 400, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
            'timestamp' => now()->toISOString(),
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Validation error response.
     */
    protected function validationErrorResponse($errors, string $message = 'Validation failed'): JsonResponse
    {
        return $this->errorResponse($message, 422, $errors);
    }

    /**
     * Not found response.
     */
    protected function notFoundResponse(string $message = 'Resource not found'): JsonResponse
    {
        return $this->errorResponse($message, 404);
    }

    /**
     * Unauthorized response.
     */
    protected function unauthorizedResponse(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->errorResponse($message, 401);
    }

    /**
     * Forbidden response.
     */
    protected function forbiddenResponse(string $message = 'Forbidden'): JsonResponse
    {
        return $this->errorResponse($message, 403);
    }

    /**
     * Server error response.
     */
    protected function serverErrorResponse(string $message = 'Internal server error'): JsonResponse
    {
        return $this->errorResponse($message, 500);
    }

    /**
     * Validate request data.
     */
    protected function validateRequest(Request $request, array $rules, array $messages = []): array
    {
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Get paginated response.
     */
    protected function paginatedResponse($data, string $message = 'Data retrieved successfully'): JsonResponse
    {
        return $this->successResponse([
            'data' => $data->items(),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
            ],
        ], $message);
    }

    /**
     * Check if user has permission.
     */
    protected function checkPermission(string $permission): bool
    {
        return auth()->check() && auth()->user()->can($permission);
    }

    /**
     * Check if user has role.
     */
    protected function checkRole(string $role): bool
    {
        return auth()->check() && auth()->user()->user_type === $role;
    }

    /**
     * Get authenticated user.
     */
    protected function getAuthUser()
    {
        return auth()->user();
    }

    /**
     * Get user ID.
     */
    protected function getUserId(): ?int
    {
        return auth()->id();
    }

    /**
     * Check if request is authenticated.
     */
    protected function isAuthenticated(): bool
    {
        return auth()->check();
    }

    /**
     * Get request filters.
     */
    protected function getFilters(Request $request): array
    {
        return $request->only(['search', 'status', 'sort_by', 'sort_order', 'per_page']);
    }

    /**
     * Get search query.
     */
    protected function getSearchQuery(Request $request): ?string
    {
        return $request->get('search');
    }

    /**
     * Get status filter.
     */
    protected function getStatusFilter(Request $request): ?string
    {
        return $request->get('status');
    }

    /**
     * Get sort parameters.
     */
    protected function getSortParams(Request $request): array
    {
        return [
            'sort_by' => $request->get('sort_by', 'created_at'),
            'sort_order' => $request->get('sort_order', 'desc'),
        ];
    }

    /**
     * Get per page value.
     */
    protected function getPerPage(Request $request): int
    {
        return (int) $request->get('per_page', 15);
    }
}

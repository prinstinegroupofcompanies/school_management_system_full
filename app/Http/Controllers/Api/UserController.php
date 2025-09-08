<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends BaseController
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            if (!$this->checkPermission('view users')) {
                return $this->forbiddenResponse('You do not have permission to view users');
            }

            $query = User::with(['roles', 'permissions']);

            // Apply search filter
            if ($search = $this->getSearchQuery($request)) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            // Apply status filter
            if ($status = $this->getStatusFilter($request)) {
                $query->where('status', $status);
            }

            // Apply role filter
            if ($role = $request->get('role')) {
                $query->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role);
                });
            }

            // Apply sorting
            $sortParams = $this->getSortParams($request);
            $query->orderBy($sortParams['sort_by'], $sortParams['sort_order']);

            // Get paginated results
            $perPage = $this->getPerPage($request);
            $users = $query->paginate($perPage);

            return $this->paginatedResponse($users, 'Users retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve users: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            if (!$this->checkPermission('create users')) {
                return $this->forbiddenResponse('You do not have permission to create users');
            }

            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'nullable|string|max:20',
                'password' => 'required|string|min:8|confirmed',
                'roles' => 'required|array',
                'roles.*' => 'exists:roles,name',
                'status' => 'nullable|in:active,inactive',
                'locale' => 'nullable|string|max:5',
                'timezone' => 'nullable|string|max:50',
            ];

            $validated = $this->validateRequest($request, $rules);

            // Create user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make($validated['password']),
                'status' => $validated['status'] ?? 'active',
                'locale' => $validated['locale'] ?? config('app.locale'),
                'timezone' => $validated['timezone'] ?? config('app.timezone'),
            ]);

            // Assign roles
            $user->assignRole($validated['roles']);

            // Load relationships
            $user->load(['roles', 'permissions']);

            return $this->successResponse($user, 'User created successfully', 201);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to create user: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified user.
     */
    public function show(int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('view users')) {
                return $this->forbiddenResponse('You do not have permission to view users');
            }

            $user = User::with(['roles', 'permissions'])->find($id);

            if (!$user) {
                return $this->notFoundResponse('User not found');
            }

            return $this->successResponse($user, 'User retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve user: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('edit users')) {
                return $this->forbiddenResponse('You do not have permission to edit users');
            }

            $user = User::find($id);

            if (!$user) {
                return $this->notFoundResponse('User not found');
            }

            $rules = [
                'name' => 'sometimes|required|string|max:255',
                'email' => [
                    'sometimes',
                    'required',
                    'email',
                    Rule::unique('users', 'email')->ignore($id),
                ],
                'phone' => 'nullable|string|max:20',
                'password' => 'nullable|string|min:8|confirmed',
                'roles' => 'sometimes|required|array',
                'roles.*' => 'exists:roles,name',
                'status' => 'nullable|in:active,inactive',
                'locale' => 'nullable|string|max:5',
                'timezone' => 'nullable|string|max:50',
            ];

            $validated = $this->validateRequest($request, $rules);

            // Update user
            $user->update(array_filter([
                'name' => $validated['name'] ?? null,
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'status' => $validated['status'] ?? null,
                'locale' => $validated['locale'] ?? null,
                'timezone' => $validated['timezone'] ?? null,
            ]));

            // Update password if provided
            if (isset($validated['password'])) {
                $user->update(['password' => Hash::make($validated['password'])]);
            }

            // Update roles if provided
            if (isset($validated['roles'])) {
                $user->syncRoles($validated['roles']);
            }

            // Load relationships
            $user->load(['roles', 'permissions']);

            return $this->successResponse($user, 'User updated successfully');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to update user: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified user.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('delete users')) {
                return $this->forbiddenResponse('You do not have permission to delete users');
            }

            $user = User::find($id);

            if (!$user) {
                return $this->notFoundResponse('User not found');
            }

            // Prevent self-deletion
            if ($user->id === $this->getUserId()) {
                return $this->forbiddenResponse('You cannot delete your own account');
            }

            $user->delete();

            return $this->successResponse(null, 'User deleted successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to delete user: ' . $e->getMessage());
        }
    }

    /**
     * Get user profile.
     */
    public function profile(): JsonResponse
    {
        try {
            if (!$this->isAuthenticated()) {
                return $this->unauthorizedResponse('User not authenticated');
            }

            $user = $this->getAuthUser()->load(['roles', 'permissions']);

            return $this->successResponse($user, 'Profile retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve profile: ' . $e->getMessage());
        }
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        try {
            if (!$this->isAuthenticated()) {
                return $this->unauthorizedResponse('User not authenticated');
            }

            $user = $this->getAuthUser();

            $rules = [
                'name' => 'sometimes|required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'locale' => 'nullable|string|max:5',
                'timezone' => 'nullable|string|max:50',
            ];

            $validated = $this->validateRequest($request, $rules);

            $user->update($validated);

            return $this->successResponse($user->load(['roles', 'permissions']), 'Profile updated successfully');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to update profile: ' . $e->getMessage());
        }
    }

    /**
     * Change user password.
     */
    public function changePassword(Request $request): JsonResponse
    {
        try {
            if (!$this->isAuthenticated()) {
                return $this->unauthorizedResponse('User not authenticated');
            }

            $rules = [
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed|different:current_password',
            ];

            $validated = $this->validateRequest($request, $rules);

            $user = $this->getAuthUser();

            // Verify current password
            if (!Hash::check($validated['current_password'], $user->password)) {
                return $this->errorResponse('Current password is incorrect', 422);
            }

            // Update password
            $user->update(['password' => Hash::make($validated['new_password'])]);

            return $this->successResponse(null, 'Password changed successfully');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to change password: ' . $e->getMessage());
        }
    }

    /**
     * Get available roles.
     */
    public function getRoles(): JsonResponse
    {
        try {
            if (!$this->checkPermission('view roles')) {
                return $this->forbiddenResponse('You do not have permission to view roles');
            }

            $roles = Role::with('permissions')->get();

            return $this->successResponse($roles, 'Roles retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve roles: ' . $e->getMessage());
        }
    }

    /**
     * Get user statistics.
     */
    public function getStatistics(): JsonResponse
    {
        try {
            if (!$this->checkPermission('view users')) {
                return $this->forbiddenResponse('You do not have permission to view user statistics');
            }

            $stats = [
                'total_users' => User::count(),
                'active_users' => User::where('status', 'active')->count(),
                'inactive_users' => User::where('status', 'inactive')->count(),
                'users_by_role' => Role::withCount('users')->get(),
                'recent_users' => User::latest()->take(5)->get(['id', 'name', 'email', 'created_at']),
            ];

            return $this->successResponse($stats, 'User statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve user statistics: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * User login.
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $rules = [
                'email' => 'required|email',
                'password' => 'required|string',
                'device_name' => 'nullable|string',
            ];

            $validated = $this->validateRequest($request, $rules);

            if (!Auth::attempt([
                'email' => $validated['email'],
                'password' => $validated['password'],
            ])) {
                return $this->errorResponse('Invalid credentials', 401);
            }

            $user = Auth::user();

            if ($user->status !== 'active') {
                Auth::logout();
                return $this->errorResponse('Account is not active', 403);
            }

            $deviceName = $validated['device_name'] ?? $request->ip();
            $token = $user->createToken($deviceName)->plainTextToken;

            return $this->successResponse([
                'user' => $user->load(['roles', 'permissions']),
                'token' => $token,
                'token_type' => 'Bearer',
            ], 'Login successful');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Login failed: ' . $e->getMessage());
        }
    }

    /**
     * User registration.
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'nullable|string|max:20',
                'password' => 'required|string|min:8|confirmed',
                'role' => 'required|string|exists:roles,name',
            ];

            $validated = $this->validateRequest($request, $rules);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make($validated['password']),
                'status' => 'active',
            ]);

            $user->assignRole($validated['role']);

            $deviceName = $request->get('device_name', $request->ip());
            $token = $user->createToken($deviceName)->plainTextToken;

            return $this->successResponse([
                'user' => $user->load(['roles', 'permissions']),
                'token' => $token,
                'token_type' => 'Bearer',
            ], 'Registration successful', 201);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Registration failed: ' . $e->getMessage());
        }
    }

    /**
     * User logout.
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return $this->successResponse(null, 'Logout successful');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Logout failed: ' . $e->getMessage());
        }
    }

    /**
     * Refresh token.
     */
    public function refresh(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $user->tokens()->delete();
            
            $deviceName = $request->get('device_name', $request->ip());
            $token = $user->createToken($deviceName)->plainTextToken;

            return $this->successResponse([
                'user' => $user->load(['roles', 'permissions']),
                'token' => $token,
                'token_type' => 'Bearer',
            ], 'Token refreshed successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Token refresh failed: ' . $e->getMessage());
        }
    }

    /**
     * Get authenticated user.
     */
    public function me(Request $request): JsonResponse
    {
        try {
            $user = $request->user()->load(['roles', 'permissions']);

            return $this->successResponse($user, 'User profile retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve user profile: ' . $e->getMessage());
        }
    }

    /**
     * Change password.
     */
    public function changePassword(Request $request): JsonResponse
    {
        try {
            $rules = [
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed|different:current_password',
            ];

            $validated = $this->validateRequest($request, $rules);

            $user = $request->user();

            if (!Hash::check($validated['current_password'], $user->password)) {
                return $this->errorResponse('Current password is incorrect', 422);
            }

            $user->update(['password' => Hash::make($validated['new_password'])]);

            return $this->successResponse(null, 'Password changed successfully');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Password change failed: ' . $e->getMessage());
        }
    }

    /**
     * Forgot password.
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        try {
            $rules = ['email' => 'required|email'];

            $validated = $this->validateRequest($request, $rules);

            $status = Password::sendResetLink(['email' => $validated['email']]);

            if ($status === Password::RESET_LINK_SENT) {
                return $this->successResponse(null, 'Password reset link sent successfully');
            }

            return $this->errorResponse('Unable to send password reset link', 400);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Password reset failed: ' . $e->getMessage());
        }
    }

    /**
     * Reset password.
     */
    public function resetPassword(Request $request): JsonResponse
    {
        try {
            $rules = [
                'token' => 'required|string',
                'email' => 'required|email',
                'password' => 'required|string|min:8|confirmed',
            ];

            $validated = $this->validateRequest($request, $rules);

            $status = Password::reset($validated, function ($user, $password) {
                $user->update(['password' => Hash::make($password)]);
            });

            if ($status === Password::PASSWORD_RESET) {
                return $this->successResponse(null, 'Password reset successfully');
            }

            return $this->errorResponse('Unable to reset password', 400);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Password reset failed: ' . $e->getMessage());
        }
    }
}

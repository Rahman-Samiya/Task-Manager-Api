<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use App\Traits\ApiResponse;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Register a new user.
     *
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        try {
            $user = $this->authService->register($request->validated());
            $token = $this->authService->createToken($user, 'mobile app');

            return $this->successResponse([
                'user' => new UserResource($user),
                'token' => $token,
            ], 'User registered successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Login a user.
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        try {
            $user = $this->authService->login($request->validated());

            if (!$user) {
                return $this->errorResponse('Invalid credentials', 401);
            }

            $token = $this->authService->createToken($user, 'mobile app');

            return $this->successResponse([
                'user' => new UserResource($user),
                'token' => $token,
            ], 'User logged in successfully', 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Logout a user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try {
            $user = auth('sanctum')->user();

            $this->authService->logout($user);

            return $this->successResponse(null, 'User logged out successfully', 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Logout user from all devices.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logoutFromAllDevices()
    {
        try {
            $user = auth('sanctum')->user();

            $this->authService->logoutFromAllDevices($user);

            return $this->successResponse(null, 'Logged out from all devices', 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Get authenticated user details.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        try {
            $user = auth('sanctum')->user();

            return $this->successResponse(
                new UserResource($user),
                'User details retrieved successfully',
                200
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }
}

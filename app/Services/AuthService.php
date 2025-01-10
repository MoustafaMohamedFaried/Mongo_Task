<?php

namespace App\Services;

use App\Repositories\AuthRepository;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Carbon\Exceptions\Exception;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\JWT;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthService
{
    use ApiResponseTrait;
    protected $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function register(Request $request)
    {
        try {
            $request['password'] = Hash::make($request['password']);

            // Validate input
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users,email',
                'password' => 'required|string|min:6',
            ]);

            $createdUser = $this->authRepository->create($validatedData);

            return $this->apiResponse($createdUser, 'User registered successfully', 201); // 201 for resource created
        } catch (ValidationException $e) {
            return $this->errorApiResponse($e->errors(), 'Validation failed', 422); // 422 Unprocessable Entity
        } catch (\Exception $e) {
            return $this->errorApiResponse($e->getMessage(), 'Error at register user', 500); // 500 Internal Server Error
        }
    }

    public function login(Request $request)
    {
        try {
            // Validate input
            $validatedData = $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            if (!$token = JWTAuth::attempt($validatedData)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            return $this->apiResponse($token, 'Login successful', 200);
        } catch (ValidationException $e) {
            return $this->errorApiResponse($e->errors(), 'Validation failed', 422);
        } catch (\Exception $e) {
            return $this->errorApiResponse($e->getMessage(), 'Error at login user', 500);
        }
    }

    public function profile()
    {
        try {
            // Authenticate user using JWT token
            $user = JWTAuth::parseToken()->authenticate();

            // Return the authenticated user's profile
            return $this->apiResponse($user, 'User profile retrieved successfully', 200);
        } catch (TokenExpiredException $e) {
            return $this->errorApiResponse([], 'Token has expired', 401);
        } catch (TokenInvalidException $e) {
            return $this->errorApiResponse([], 'Token is invalid', 401);
        } catch (JWTException $e) {
            return $this->errorApiResponse([], 'Token is not provided', 401);
        }
    }

    public function logout()
    {
        try {
            // Invalidate the token
            JWTAuth::invalidate(JWTAuth::getToken());

            return $this->apiResponse([], 'User successfully signed out', 200);
        } catch (JWTException $e) {
            return $this->errorApiResponse([], 'Failed to log out, token is invalid', 401);
        }
    }
}

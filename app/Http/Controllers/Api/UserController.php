<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use Carbon\Exceptions\Exception;
use App\Traits\ApiResponseTrait;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\JWT;

class UserController extends Controller
{
    use ApiResponseTrait;

    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        try {
            return $this->apiResponse($this->userService->getAllUsers(), '', 200);
        } catch (\Exception $e) {
            return $this->errorApiResponse($e->getMessage(), 'Error at get users', $e->getCode());
        }
    }

    public function store(Request $request)
    {
        try {
            // get the decoded token claims
            // $decodedToken = JWTAuth::parseToken()->getPayload();
            // dd($decodedToken);

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
            ]);

            $createdUser = $this->userService->createUser($validatedData);

            return $this->apiResponse($createdUser, 'User created successfully', 200);
        } catch (ValidationException $e) {

            return $this->errorApiResponse($e->errors(), 'Validation failed', $e->getCode());
        } catch (Exception $e) {

            return $this->errorApiResponse($e->getMessage(), 'Error at create user', $e->getCode());
        }
    }

    public function show($user_id)
    {
        return $this->apiResponse($this->userService->getUser($user_id), '', 200);
    }

    public function update(Request $request, $user_id)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user_id,
                'password' => 'nullable|min:6',
            ]);

            $updatedUser = $this->userService->updateUser($user_id, $validatedData);

            return $this->apiResponse($updatedUser, 'User updated successfully', 200);
        } catch (ValidationException $e) {

            return $this->errorApiResponse($e->errors(), 'Validation failed', $e->getCode());
        } catch (Exception $e) {

            return $this->errorApiResponse($e->getMessage(), 'Error at update user', $e->getCode());
        }
    }

    public function destroy($user_id)
    {
        try {
            $this->userService->deleteUser($user_id);

            return $this->apiResponse([], 'User deleted successfully', 200);
        } catch (Exception $e) {

            return $this->errorApiResponse($e->getMessage(), 'Error at delete user', $e->getCode());
        }
    }

    public function register(Request $request)
    {
        try {
            // Validate input
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users,email',
                'password' => 'required|string|min:6',
            ]);

            // Use the AuthService to handle registration
            $this->userService->createUser($validatedData);

            if (!$token = JWTAuth::attempt($validatedData)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $createdToken = $this->userService->createNewToken($token);

            return $this->apiResponse($createdToken, 'User registered successfully', 201); // 201 for resource created
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

            $createdToken = $this->userService->createNewToken($token);

            return $this->apiResponse($createdToken, 'Login successful', 200);
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
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return $this->errorApiResponse([], 'Token has expired', 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return $this->errorApiResponse([], 'Token is invalid', 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return $this->errorApiResponse([], 'Token is not provided', 401);
        }
    }

    public function logout()
    {
        try {
            // Invalidate the token
            JWTAuth::invalidate(JWTAuth::getToken());

            return $this->apiResponse([], 'User successfully signed out', 200);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return $this->errorApiResponse([], 'Failed to log out, token is invalid', 401);
        }
    }

    public function checkLogin()
    {
        return $this->apiResponse([], 'Forbidden', 403);
    }

    public function checkToken()
    {
        $user = auth()->user();

        return $this->apiResponse($user, '', 200);
    }
}

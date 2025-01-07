<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers()
    {
        return $this->userRepository->getAll();
    }

    public function getUser($user_id)
    {
        return $this->userRepository->find($user_id);
    }

    public function createUser(array $data)
    {
        // Hash the password
        $data['password'] = Hash::make($data['password']);

        // Create the user using the repository
        $createdUser = $this->userRepository->create($data);

        return $createdUser;
    }

    public function updateUser($user_id, array $data)
    {
        // Hash the password if provided
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']); // Remove it if not provided
        }

        // Update the user using the repository
        return $this->userRepository->update($user_id, $data);
    }

    public function deleteUser($user_id)
    {
        return $this->userRepository->delete($user_id);
    }

    public function createNewToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'Bearer', // Standardize capitalization
            'expires_in' => JWTAuth::factory()->getTTL() * 60, // Use JWTAuth for consistency
            'user' => auth()->user(), // Include the authenticated user details
        ];
    }
}

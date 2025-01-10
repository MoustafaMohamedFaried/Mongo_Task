<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register(Request $request)
    {
        return $this->userService->register($request);
    }

    public function login(Request $request)
    {
        return $this->userService->login($request);
    }

    public function profile()
    {
        return $this->userService->profile();
    }

    public function logout()
    {
        return $this->userService->logout();
    }

    public function checkLogin()
    {
        return $this->apiResponse([], 'Forbidden', 403);
    }
}

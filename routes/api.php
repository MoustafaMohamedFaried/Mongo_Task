<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/




// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Routes that don't require authentication
Route::post('/register', [UserController::class, 'register'])->name('users.register')->middleware('checkApiKey');
Route::get('/login-data', [UserController::class, 'checkLogin'])->name('users.checkLogin');
Route::post('/login', [UserController::class, 'login'])->name('users.login')->middleware('checkApiKey');
// Route::get('/users/checkToken', [UserController::class, 'checkToken'])->name('users.checkToken')->middleware(['auth:jwt','checkApiKey']);

// Routes that require authentication
Route::group(['middleware' => ['auth:jwt', 'checkApiKey']], function () {
    Route::apiResource('users', UserController::class);
    Route::get('/user_profile', [UserController::class, 'profile'])->name('users.profile');
    Route::get('/logout', [UserController::class, 'logout'])->name('users.logout');
});


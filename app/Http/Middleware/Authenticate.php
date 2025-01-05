<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // return $request->expectsJson() ? null : route('users.checkLogin');

        if (!$request->expectsJson()) {
            return route('users.checkLogin'); // This causes the "Route [login] not defined" error.
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class checkIfHasKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!$request->header('x-api-key') || $request->header('x-api-key') != 'kdsldnsjdnsjkndjksndjknskd545646545645646'){
            return response()->json('you dont hvae permission');
        }
        return $next($request);
    }
}

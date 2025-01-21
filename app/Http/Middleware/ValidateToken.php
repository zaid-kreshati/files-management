<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ValidateToken
{
    public function handle($request, Closure $next)
    {
        // Check if the token is valid and authenticated using Passport's API guard
        if (Auth::guard('api')->check()) {
            return $next($request);
        }

        // Return unauthorized response if the token is invalid or not provided
        return response()->json(['error' => 'Unauthorized. Invalid or missing token.'], Response::HTTP_UNAUTHORIZED);
    }
}

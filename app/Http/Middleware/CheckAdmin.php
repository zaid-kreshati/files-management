<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Traits\JsonResponseTrait; // Include the trait

class CheckAdmin
{
    use JsonResponseTrait; // Use the JsonResponseTrait

    public function handle($request, Closure $next)
    {
        // Use 'api' guard for Passport (or change to your custom guard if needed)
        $user = Auth::guard('api')->user();

        if ($user && $user->hasRole('admin')) {
            return $next($request);
        }

        // Return unauthorized JSON response if the user is not an admin
        return $this->errorResponse('Unauthorized', 401);
    }
}

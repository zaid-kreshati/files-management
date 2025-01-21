<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ValidateAccessToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken(); // Get token from the Authorization header

        if (!$token) {
            return response()->json(['message' => 'Access token missing'], 401);
        }

        try {
            $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256')); // Verify token
            if ($decoded->exp < time()) {
                return response()->json(['message' => 'Access token expired'], 401);
            }

            // Optionally, you can set the user in the request for further use
            //$request->user = $decoded->user_id;
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid access token'], 401);
        }

        return $next($request); // Proceed if the token is valid
    }
}

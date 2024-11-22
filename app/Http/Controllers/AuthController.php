<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function registerClient(RegisterRequest $request)
    {
        $user = $this->authService->register(array_merge($request->validated(), ['role' => 'user']));
        return response()->json(['user' => $user], 201);
    }

    public function registerAdmin(RegisterRequest $request)
    {
        $user = $this->authService->register(array_merge($request->validated(), ['role' => 'admin']));
        return response()->json(['user' => $user], 201);
    }

    public function login(LoginRequest $request)
    {
        $authData = $this->authService->login($request->validated());

        if ($authData) {
            return response()->json($authData, 200);
        }

        return response()->json(['error' => 'Invalid credentials'], 401);
    }

    public function logout()
    {
        $this->authService->logout();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}

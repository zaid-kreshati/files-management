<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Repositories\AuthRepository;

class AuthService
{
    protected AuthRepository $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function register(array $data): void
    {
        $user = $this->authRepository->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        $user->assignRole($data['role']);
    }

    public function login(array $data): ?array
    {
        if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            $user = Auth::user();
            $token = $user->createToken('API Token')->plainTextToken;
            return ['user' => $user, 'token' => $token];
        }
        return null;
    }

    public function logout(): true
    {
        Auth::user()->tokens()->delete();
        return true;
    }
}

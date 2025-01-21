<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Repositories\AuthRepository;
use App\Models\RefreshToken;

class AuthService
{
    protected $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function register(array $data)
    {
        Log::info($data);
        $user = $this->authRepository->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        $user->assignRole($data['role']);

        return ;
    }

    public function login(array $data)
    {
        if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            $user = Auth::user();
            $accesstoken = $user->createToken('API Token')->plainTextToken;
            $refreshToken = $user->createToken('refresh-token')->plainTextToken;
            $this->authRepository->createRefreshToken($user->id, $refreshToken);
            return [ 'accessToken' => $accesstoken,'refreshToken'=>$refreshToken];
        }

        return null;
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();
        return true;
    }

    public function refreshToken($userId, $refreshToken)
    {
        $this->authRepository->refreshToken($userId, $refreshToken);
    }
}

<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function register(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->assignRole($data['role']);
        $token = $user->createToken('API Token')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    public function login(array $data)
    {
        if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            $user = Auth::user();
            $token = $user->createToken('API Token')->plainTextToken;
            return ['user' => $user, 'token' => $token];
        }

        return null;
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();
        return true;
    }
}

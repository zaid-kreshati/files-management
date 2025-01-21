<?php
// app/Repositories/UserRepository.php

namespace App\Repositories;

use App\Models\User;
use App\Models\RefreshToken;

class AuthRepository
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function create(array $data)
    {
        return $this->user->create($data);
    }

    public function findByEmail($email)
    {
        return $this->user->where('email', $email)->first();
    }

    public function revokeUserTokens(User $user)
    {
        // Revoke all tokens for the user
        $user->tokens()->delete();
    }

    public function createRefreshToken($userId, $refreshToken)
    {
        return RefreshToken::create([
            'user_id' => $userId,
            'token' => $refreshToken,
            'expires_at' => now()->addDays(7)
        ]);
    }





}

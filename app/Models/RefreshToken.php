<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefreshToken extends Model
{
    protected $table = 'refresh_tokens';
    protected $fillable = ['user_id', 'token', 'expires_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

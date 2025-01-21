<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = ['status', 'group_id', 'sender_id', 'receiver_id'];
    protected $table = 'invitations';

    // The group the invitation belongs to
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    // The user who sent the invitation (owner)
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // The user who received the invitation
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

}

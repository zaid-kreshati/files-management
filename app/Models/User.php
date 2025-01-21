<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


     // Relationship to groups the user owns
     public function ownedGroups()
     {
         return $this->hasMany(Group::class, 'owner_id');
     }

     // Relationship to groups the user is a member of
     public function groups()
     {
         return $this->belongsToMany(UserGroup::class)->where('status', 'accepted');  // Track membership status
     }











    public function filesCheckedOut()
    {
        return $this->hasMany(File::class, 'checked_out_by');
    }


    public function sentInvitations()
    {
        return $this->hasMany(Invitation::class, 'sender_id');
    }
    public function receivedInvitations()
    {
        return $this->hasMany(Invitation::class, 'receiver_id');
    }

    public function checkouts()
    {
        return $this->hasMany(Checkout::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function auditTrails()
    {
        return $this->hasMany(AuditTrail::class);
    }

    public function operationsLogs()
    {
        return $this->hasMany(OperationsLog::class);
    }

}

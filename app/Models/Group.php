<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $owner_id
 * @property mixed $members
 */
class Group extends Model
{
    use HasFactory;

    protected $fillable = ['name' ,'owner_id'];
    protected $table = 'groups';

    public function files()
    {
        return $this->belongsToMany(File::class, 'files_groups_pivot');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // Users who are members of the group
    public function members()
    {
        return $this->belongsToMany(User::class, 'users_groups_pivot')
        ->withPivot('status')  // Track membership status
        ->withTimestamps();
    }

    // Invitations associated with the group
    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }
}

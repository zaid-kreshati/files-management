<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;


    protected $fillable = ['name', 'path', 'status', 'checked_in_by', 'checked_in_at', 'group_id' , 'approval_status'];
    protected $table = 'files';
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'files_groups_pivot');
    }
    public function checkedInBy()
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    public function checkouts()
    {
        return $this->hasMany(Checkout::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function backups()
    {
        return $this->hasMany(Backup::class);
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

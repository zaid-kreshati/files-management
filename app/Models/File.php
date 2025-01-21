<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * The suggestion is to explicitly document the id property in the File class using a @ property 'PHPDoc annotation.
 * This helps tools like IDEs and static analyzers understand that $id exists, even if it's not defined directly in the class.
 */


/**
 * @property mixed $id
 * @property mixed $status
 * @property mixed $name
 * @property mixed $path
 * @property mixed|string $approval_status
 * @method static find($fileId)
 */

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

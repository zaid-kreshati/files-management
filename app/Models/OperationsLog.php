<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationsLog extends Model
{
    use HasFactory;
    protected $table = 'operations_log';
    protected $fillable = ['file_id', 'user_id', 'operation', 'status'];

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}

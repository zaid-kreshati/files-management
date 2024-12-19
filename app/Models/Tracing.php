<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tracing extends Model
{
    use HasFactory;

    protected $table = 'tracings';
    protected $fillable = ['file_id', 'user_id', 'action', 'changes' , 'before' , 'after'];

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

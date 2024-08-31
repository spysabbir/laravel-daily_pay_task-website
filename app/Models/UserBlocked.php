<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBlocked extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [];

    public function blocked()
    {
        return $this->belongsTo(User::class, 'blocked_id');
    }

    public function blockedBy()
    {
        return $this->belongsTo(User::class, 'blocked_by');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [];

    public function blocked()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function blockedBy()
    {
        return $this->belongsTo(User::class, 'blocked_by')->withTrashed();
    }
}

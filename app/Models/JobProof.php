<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobProof extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function job()
    {
        return $this->belongsTo(JobPost::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

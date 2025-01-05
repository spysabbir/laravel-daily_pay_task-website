<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by')->withTrashed();
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by')->withTrashed();
    }
}

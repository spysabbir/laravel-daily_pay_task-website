<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProofTask extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function postTask()
    {
        return $this->belongsTo(PostTask::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function rating()
    {
        return $this->hasOne(Rating::class, 'post_task_id', 'post_task_id');
    }

    public function bonus()
    {
        return $this->hasOne(Bonus::class, 'post_task_id', 'post_task_id');
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

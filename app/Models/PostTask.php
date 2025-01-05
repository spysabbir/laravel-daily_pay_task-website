<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostTask extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function childCategory()
    {
        return $this->belongsTo(ChildCategory::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by')->withTrashed();
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by')->withTrashed();
    }

    public function pausedBy()
    {
        return $this->belongsTo(User::class, 'paused_by')->withTrashed();
    }

    public function canceledBy()
    {
        return $this->belongsTo(User::class, 'canceled_by')->withTrashed();
    }

    public function proofTasks()
    {
        return $this->hasMany(ProofTask::class);
    }
}

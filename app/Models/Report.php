<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function replies()
    {
        return $this->hasMany(ReportReply::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reported()
    {
        return $this->belongsTo(User::class, 'reported_id');
    }

    public function reportedBy()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
    
}

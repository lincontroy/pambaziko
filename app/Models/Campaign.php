<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'upload_id', 'paybill_id', 'name', 'total_count',
        'sent_count', 'failed_count', 'status', 'started_at', 'completed_at'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function upload()
    {
        return $this->belongsTo(Upload::class);
    }

    public function paybill()
    {
        return $this->belongsTo(Paybill::class);
    }

    public function contacts()
    {
        return $this->hasManyThrough(Contact::class, Upload::class);
    }

    public function stkRequests()
    {
        return $this->hasMany(StkRequest::class);
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->total_count === 0) {
            return 0;
        }
        return round((($this->sent_count + $this->failed_count) / $this->total_count) * 100);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paybill extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'paybill_number', 'consumer_key', 
        'consumer_secret', 'passkey', 'daily_limit', 'current_count', 'reset_at'
    ];

    protected $casts = [
        'reset_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }

    public function hasQuota()
    {
        return $this->current_count < $this->daily_limit;
    }

    public function incrementCount()
    {
        $this->current_count += 1;
        $this->save();
    }

}
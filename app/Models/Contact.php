<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'upload_id', 'user_id', 'phone', 'amount', 'status', 
        'response_json', 'attempts', 'last_attempt_at'
    ];

    protected $casts = [
        'last_attempt_at' => 'datetime',
        'response_json' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function upload()
    {
        return $this->belongsTo(Upload::class);
    }

    public function stkRequests()
    {
        return $this->hasMany(StkRequest::class);
    }
}
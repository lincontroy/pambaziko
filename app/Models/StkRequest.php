<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StkRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id', 'campaign_id', 'request_json', 'response_json', 'status', 'attempts'
    ];

    protected $casts = [
        'request_json' => 'array',
        'response_json' => 'array',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
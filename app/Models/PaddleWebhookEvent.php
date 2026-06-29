<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaddleWebhookEvent extends Model
{
    protected $fillable = [
        'event_id',
        'event_type',
        'occurred_at',
        'payload',
        'processed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'occurred_at' => 'datetime',
        'processed_at' => 'datetime',
    ];
}
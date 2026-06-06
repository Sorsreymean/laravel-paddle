<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'paddle_transaction_id',
    'paddle_subscription_id',
    'invoice_number',
    'status',
    'total',
    'tax',
    'currency',
    'billed_at',
    'payload',
])]
class BillingRecord extends Model
{
    protected function casts(): array
    {
        return [
            'billed_at' => 'datetime',
            'payload' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Price extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'unit_price' => 'array',
            'billing_cycle' => 'array',
            'trial_period' => 'array',
            'quantity' => 'array',
            'custom_data' => 'array',
            'paddle_created_at' => 'datetime',
            'paddle_updated_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

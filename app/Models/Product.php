<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'custom_data' => 'array',
            'paddle_created_at' => 'datetime',
            'paddle_updated_at' => 'datetime',
        ];
    }

    public function prices(): HasMany
    {
        return $this->hasMany(Price::class);
    }
}

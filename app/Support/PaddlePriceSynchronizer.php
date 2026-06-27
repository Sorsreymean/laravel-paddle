<?php

namespace App\Support;

use App\Models\Price;
use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class PaddlePriceSynchronizer
{
    public function syncMany(Collection $prices): void
    {
        if (! Schema::hasTable('prices')) {
            return;
        }

        $prices
            ->filter(fn ($price) => filled(Arr::get($price, 'id')))
            ->each(fn (array $price) => $this->sync($price));
    }

    public function sync(array $price): Price
    {
        // dd($price);
        if (! Schema::hasTable('prices')) {
            return new Price();
        }

        $localProductId = null;

        if (Schema::hasTable('products') && filled($price['product_id'] ?? null)) {
            $localProductId = Product::query()
                ->where('paddle_id', $price['product_id'])
                ->value('id');
        }

        return Price::updateOrCreate(
            ['paddle_id' => $price['id']],
            [
                'product_id' => $localProductId,
                'paddle_product_id' => $price['product_id'] ?? null,
                'description' => $price['description'] ?? null,
                'status' => $price['status'] ?? null,
                'tax_mode' => $price['tax_mode'] ?? null,
                'amount' => (($price['unit_price']['amount']) / 100) ?? null,
                'currency' => $price['unit_price']['currency_code'] ?? null,
                'interval' => $price['billing_cycle']['interval'] ?? null,
                'frequency' => $price['billing_cycle']['frequency'] ?? null,
                'unit_price' => $price['unit_price'] ?? null,
                'billing_cycle' => $price['billing_cycle'] ?? null,
                'trial_period' => $price['trial_period'] ?? null,
                'quantity' => $price['quantity'] ?? null,
                'custom_data' => $price['custom_data'] ?? null,
                'paddle_created_at' => filled($price['created_at'] ?? null)
                    ? Carbon::parse($price['created_at'])
                    : null,
                'paddle_updated_at' => filled($price['updated_at'] ?? null)
                    ? Carbon::parse($price['updated_at'])
                    : null,
            ],
        );
    }
}

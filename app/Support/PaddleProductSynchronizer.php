<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class PaddleProductSynchronizer
{
    public function syncMany(Collection $products): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        $products
            ->filter(fn ($product) => filled(Arr::get($product, 'id')))
            ->each(fn (array $product) => $this->sync($product));
    }

    public function sync(array $product): Product
    {
        if (! Schema::hasTable('products')) {
            return new Product();
        }
        // dd($product);
        return Product::updateOrCreate(
            ['paddle_id' => $product['id']],
            [
                'name' => $product['name'] ?? 'Unnamed product',
                'description' => $product['description'] ?? null,
                'status' => $product['status'] ?? null,
                'type' => $product['type'] ?? null,
                'tax_category' => $product['tax_category'] ?? null,
                'image_url' => $product['image_url'] ?? null,
                'custom_data' => $product['custom_data'] ?? null,
                'paddle_created_at' => filled($product['created_at'] ?? null)
                    ? Carbon::parse($product['created_at'])
                    : null,
                'paddle_updated_at' => filled($product['updated_at'] ?? null)
                    ? Carbon::parse($product['updated_at'])
                    : null,
            ],
        );
    }
}

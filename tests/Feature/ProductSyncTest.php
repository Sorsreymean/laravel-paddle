<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProductSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_or_updates_local_products_when_paddle_products_are_fetched(): void
    {
        config()->set('cashier.api_key', 'test_api_key');
        config()->set('cashier.client_side_token', 'test_client_token');
        config()->set('cashier.webhook_secret', 'test_webhook_secret');
        config()->set('services.paddle.default_price_id', 'pri_test_123');
        config()->set('cashier.sandbox', true);

        Http::fake([
            'https://sandbox-api.paddle.com/products*' => Http::sequence()
                ->push([
                    'data' => [[
                        'id' => 'pro_123',
                        'name' => 'Starter Plan',
                        'description' => 'First version',
                        'status' => 'active',
                        'type' => 'standard',
                        'tax_category' => 'standard',
                        'image_url' => 'https://example.com/starter.png',
                        'custom_data' => ['category' => 'plans'],
                        'created_at' => '2026-06-22T03:00:00Z',
                        'updated_at' => '2026-06-22T03:00:00Z',
                    ]],
                ], 200)
                ->push([
                    'data' => [[
                        'id' => 'pro_123',
                        'name' => 'Starter Plan Updated',
                        'description' => 'Updated version',
                        'status' => 'inactive',
                        'type' => 'standard',
                        'tax_category' => 'digital-goods',
                        'image_url' => 'https://example.com/starter-new.png',
                        'custom_data' => ['category' => 'plans'],
                        'created_at' => '2026-06-22T03:00:00Z',
                        'updated_at' => '2026-06-22T05:00:00Z',
                    ]],
                ], 200),
            'https://sandbox-api.paddle.com/prices*' => Http::sequence()
                ->push([
                    'data' => [[
                        'id' => 'pri_test_123',
                        'product_id' => 'pro_123',
                        'unit_price' => ['amount' => '1000', 'currency_code' => 'USD'],
                        'billing_cycle' => ['interval' => 'month', 'frequency' => 1],
                        'status' => 'active',
                    ]],
                ], 200)
                ->push([
                    'data' => [[
                        'id' => 'pri_test_123',
                        'product_id' => 'pro_123',
                        'unit_price' => ['amount' => '1200', 'currency_code' => 'USD'],
                        'billing_cycle' => ['interval' => 'month', 'frequency' => 1],
                        'status' => 'active',
                    ]],
                ], 200),
        ]);

        $this->get(route('home'))->assertOk();

        $this->assertDatabaseHas('products', [
            'paddle_id' => 'pro_123',
            'name' => 'Starter Plan',
            'description' => 'First version',
            'status' => 'active',
            'type' => 'standard',
            'tax_category' => 'standard',
            'image_url' => 'https://example.com/starter.png',
        ]);
        $this->get(route('home'))->assertOk();

        $this->assertDatabaseHas('products', [
            'paddle_id' => 'pro_123',
            'name' => 'Starter Plan Updated',
            'description' => 'Updated version',
            'status' => 'inactive',
            'tax_category' => 'digital-goods',
            'image_url' => 'https://example.com/starter-new.png',
        ]);
    }
}

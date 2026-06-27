<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SubscriptionPageTest extends TestCase
{
    public function test_it_shows_subscriptions_from_paddle(): void
    {
        config()->set('cashier.api_key', 'test_api_key');
        config()->set('cashier.sandbox', true);

        Http::fake([
            'https://sandbox-api.paddle.com/subscriptions*' => Http::response([
                'data' => [[
                    'id' => 'sub_123',
                    'status' => 'active',
                    'customer_id' => 'ctm_123',
                    'address_id' => 'add_123',
                    'collection_mode' => 'automatic',
                    'started_at' => '2026-06-22T08:00:00Z',
                    'next_billed_at' => '2026-07-22T08:00:00Z',
                    'items' => [[
                        'price' => [
                            'product' => ['name' => 'Starter Plan'],
                            'billing_cycle' => [
                                'interval' => 'month',
                                'frequency' => 1,
                            ],
                        ],
                    ]],
                ]],
            ], 200),
            'https://sandbox-api.paddle.com/customers/ctm_123' => Http::response([
                'data' => [
                    'id' => 'ctm_123',
                    'email' => 'subscriber@example.com',
                ],
            ], 200),
        ]);

        $response = $this->get(route('subscriptions.index'));

        $response->assertOk();
        $response->assertSee('Subscriptions from Paddle');
        $response->assertSee('sub_123');
        $response->assertSee('Starter Plan');
        $response->assertSee('automatic');
        $response->assertSee('subscriber@example.com');
    }
}

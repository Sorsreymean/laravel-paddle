<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TransactionPageTest extends TestCase
{
    public function test_it_shows_transactions_from_paddle(): void
    {
        config()->set('cashier.api_key', 'test_api_key');
        config()->set('cashier.sandbox', true);

        Http::fake([
            'https://sandbox-api.paddle.com/transactions*' => Http::response([
                'data' => [[
                    'id' => 'txn_123',
                    'invoice_number' => 'INV-123',
                    'status' => 'completed',
                    'customer_id' => 'ctm_123',
                    'subscription_id' => 'sub_123',
                    'currency_code' => 'USD',
                    'billed_at' => '2026-06-22T08:30:00Z',
                    'details' => [
                        'totals' => [
                            'total' => '1900',
                            'tax' => '0',
                        ],
                        'line_items' => [[
                            'product' => ['name' => 'Starter Plan'],
                        ]],
                    ],
                ]],
            ], 200),
            'https://sandbox-api.paddle.com/customers/ctm_123' => Http::response([
                'data' => [
                    'id' => 'ctm_123',
                    'email' => 'customer@example.com',
                ],
            ], 200),
        ]);

        $response = $this->get(route('transactions.index'));

        $response->assertOk();
        $response->assertSee('Transactions from Paddle');
        $response->assertSee('txn_123');
        $response->assertSee('INV-123');
        $response->assertSee('Starter Plan');
        $response->assertSee('customer@example.com');
    }
}

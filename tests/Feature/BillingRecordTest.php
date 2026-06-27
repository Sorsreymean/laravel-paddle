<?php

namespace Tests\Feature;

use App\Models\BillingRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Paddle\Events\SubscriptionCreated;
use Laravel\Paddle\Events\SubscriptionUpdated;
use Laravel\Paddle\Events\TransactionCompleted;
use Laravel\Paddle\Subscription;
use Laravel\Paddle\Transaction;
use Tests\TestCase;

class BillingRecordTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_billing_record_when_a_transaction_is_completed(): void
    {
        $user = User::factory()->create();

        $transaction = new Transaction([
            'paddle_id' => 'txn_123',
            'paddle_subscription_id' => 'sub_123',
            'invoice_number' => 'INV-123',
            'status' => Transaction::STATUS_COMPLETED,
            'total' => '1900',
            'tax' => '0',
            'currency' => 'USD',
            'billed_at' => now(),
        ]);

        event(new TransactionCompleted($user, $transaction, ['event_id' => 'evt_123']));

        $this->assertDatabaseHas('billing_records', [
            'user_id' => $user->id,
            'paddle_transaction_id' => 'txn_123',
            'paddle_subscription_id' => 'sub_123',
            'invoice_number' => 'INV-123',
            'status' => Transaction::STATUS_COMPLETED,
            'total' => '1900',
            'currency' => 'USD',
        ]);

        $record = BillingRecord::first();

        $this->assertSame('evt_123', $record->payload['event_id']);
    }

    public function test_success_page_syncs_the_latest_transaction_into_billing_records(): void
    {
        config()->set('services.paddle.demo_email', 'demo@example.com');
        config()->set('services.paddle.demo_name', 'Demo User');
        config()->set('cashier.api_key', 'test_api_key');
        config()->set('cashier.sandbox', true);

        Http::fake([
            'https://sandbox-api.paddle.com/customers*' => Http::sequence()
                ->push(['data' => []], 200)
                ->push([
                    'data' => [
                        'id' => 'ctm_demo_123',
                        'name' => 'Demo User',
                        'email' => 'demo@example.com',
                    ],
                ], 200),
            'https://sandbox-api.paddle.com/subscriptions/sub_sync_123' => Http::response([
                'data' => [
                    'id' => 'sub_sync_123',
                    'status' => Subscription::STATUS_ACTIVE,
                    'items' => [[
                        'status' => 'active',
                        'quantity' => 1,
                        'price' => [
                            'id' => 'pri_sync_123',
                            'product_id' => 'pro_sync_123',
                        ],
                    ]],
                ],
            ], 200),
        ]);

        $user = User::factory()->create([
            'email' => 'demo@example.com',
        ]);

        $user->transactions()->create([
            'paddle_id' => 'txn_sync_123',
            'paddle_subscription_id' => 'sub_sync_123',
            'invoice_number' => 'INV-SYNC-123',
            'status' => Transaction::STATUS_COMPLETED,
            'total' => '2900',
            'tax' => '100',
            'currency' => 'USD',
            'billed_at' => now(),
        ]);

        $this->get(route('billing.success'))->assertOk();

        $this->assertDatabaseHas('billing_records', [
            'user_id' => $user->id,
            'paddle_transaction_id' => 'txn_sync_123',
            'paddle_subscription_id' => 'sub_sync_123',
            'invoice_number' => 'INV-SYNC-123',
            'status' => Transaction::STATUS_COMPLETED,
            'total' => '2900',
            'tax' => '100',
            'currency' => 'USD',
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'billable_type' => User::class,
            'billable_id' => $user->id,
            'paddle_id' => 'sub_sync_123',
            'type' => Subscription::DEFAULT_TYPE,
            'status' => Subscription::STATUS_ACTIVE,
        ]);

        $this->assertDatabaseHas('subscription_items', [
            'price_id' => 'pri_sync_123',
            'product_id' => 'pro_sync_123',
            'status' => 'active',
            'quantity' => 1,
        ]);
    }

    public function test_success_page_creates_transaction_record_from_paddle_when_missing_locally(): void
    {
        config()->set('services.paddle.demo_email', 'demo@example.com');
        config()->set('services.paddle.demo_name', 'Demo User');
        config()->set('cashier.api_key', 'test_api_key');
        config()->set('cashier.sandbox', true);

        Http::fake([
            'https://sandbox-api.paddle.com/customers*' => Http::sequence()
                ->push(['data' => []], 200)
                ->push([
                    'data' => [
                        'id' => 'ctm_demo_456',
                        'name' => 'Demo User',
                        'email' => 'demo@example.com',
                    ],
                ], 200),
            'https://sandbox-api.paddle.com/transactions*' => Http::response([
                'data' => [[
                    'id' => 'txn_remote_123',
                    'subscription_id' => 'sub_remote_123',
                    'invoice_number' => 'INV-REMOTE-123',
                    'status' => Transaction::STATUS_COMPLETED,
                    'currency_code' => 'USD',
                    'billed_at' => '2026-06-22T10:00:00Z',
                    'details' => [
                        'totals' => [
                            'total' => '3900',
                            'tax' => '200',
                        ],
                    ],
                ]],
            ], 200),
            'https://sandbox-api.paddle.com/subscriptions/sub_remote_123' => Http::response([
                'data' => [
                    'id' => 'sub_remote_123',
                    'status' => Subscription::STATUS_ACTIVE,
                    'items' => [[
                        'status' => 'active',
                        'quantity' => 1,
                        'price' => [
                            'id' => 'pri_remote_123',
                            'product_id' => 'pro_remote_123',
                        ],
                    ]],
                ],
            ], 200),
        ]);

        $user = User::factory()->create([
            'email' => 'demo@example.com',
        ]);

        $this->get(route('billing.success'))->assertOk();

        $this->assertDatabaseHas('transactions', [
            'billable_type' => User::class,
            'billable_id' => $user->id,
            'paddle_id' => 'txn_remote_123',
            'paddle_subscription_id' => 'sub_remote_123',
            'invoice_number' => 'INV-REMOTE-123',
            'status' => Transaction::STATUS_COMPLETED,
            'total' => '3900',
            'tax' => '200',
            'currency' => 'USD',
        ]);

        $this->assertDatabaseHas('billing_records', [
            'user_id' => $user->id,
            'paddle_transaction_id' => 'txn_remote_123',
            'paddle_subscription_id' => 'sub_remote_123',
        ]);
    }

    public function test_success_page_uses_the_current_customer_subscription_when_transaction_has_no_subscription_id(): void
    {
        config()->set('services.paddle.demo_email', 'demo@example.com');
        config()->set('services.paddle.demo_name', 'Demo User');
        config()->set('cashier.api_key', 'test_api_key');
        config()->set('cashier.sandbox', true);

        Http::fake([
            'https://sandbox-api.paddle.com/customers*' => Http::sequence()
                ->push(['data' => []], 200)
                ->push([
                    'data' => [
                        'id' => 'ctm_demo_789',
                        'name' => 'Demo User',
                        'email' => 'demo@example.com',
                    ],
                ], 200),
            'https://sandbox-api.paddle.com/transactions*' => Http::response([
                'data' => [[
                    'id' => 'txn_remote_456',
                    'subscription_id' => null,
                    'invoice_number' => 'INV-REMOTE-456',
                    'status' => Transaction::STATUS_COMPLETED,
                    'currency_code' => 'USD',
                    'billed_at' => '2026-06-22T10:00:00Z',
                    'details' => [
                        'totals' => [
                            'total' => '4900',
                            'tax' => '250',
                        ],
                    ],
                ]],
            ], 200),
            'https://sandbox-api.paddle.com/subscriptions*' => Http::response([
                'data' => [
                    [
                        'id' => 'sub_old_123',
                        'status' => 'canceled',
                        'created_at' => '2026-06-20T08:00:00Z',
                        'items' => [[
                            'status' => 'canceled',
                            'quantity' => 1,
                            'price' => [
                                'id' => 'pri_old_123',
                                'product_id' => 'pro_old_123',
                            ],
                        ]],
                    ],
                    [
                        'id' => 'sub_current_123',
                        'status' => Subscription::STATUS_ACTIVE,
                        'created_at' => '2026-06-24T08:00:00Z',
                        'started_at' => '2026-06-24T08:00:00Z',
                        'items' => [[
                            'status' => 'active',
                            'quantity' => 1,
                            'price' => [
                                'id' => 'pri_current_123',
                                'product_id' => 'pro_current_123',
                            ],
                        ]],
                    ],
                ],
            ], 200),
        ]);

        $user = User::factory()->create([
            'email' => 'demo@example.com',
        ]);

        $this->get(route('billing.success'))->assertOk();

        $this->assertDatabaseHas('subscriptions', [
            'billable_type' => User::class,
            'billable_id' => $user->id,
            'paddle_id' => 'sub_current_123',
            'status' => Subscription::STATUS_ACTIVE,
        ]);

        $this->assertDatabaseHas('subscription_items', [
            'price_id' => 'pri_current_123',
            'product_id' => 'pro_current_123',
            'status' => 'active',
            'quantity' => 1,
        ]);
    }

    public function test_subscription_webhook_events_sync_subscription_records(): void
    {
        $user = User::factory()->create();

        $subscription = $user->subscriptions()->create([
            'type' => Subscription::DEFAULT_TYPE,
            'paddle_id' => 'sub_webhook_123',
            'status' => 'pending',
        ]);

        event(new SubscriptionCreated($user, $subscription, [
            'data' => [
                'id' => 'sub_webhook_123',
                'status' => Subscription::STATUS_ACTIVE,
                'custom_data' => [
                    'subscription_type' => Subscription::DEFAULT_TYPE,
                ],
                'items' => [[
                    'status' => 'active',
                    'quantity' => 1,
                    'price' => [
                        'id' => 'pri_webhook_123',
                        'product_id' => 'pro_webhook_123',
                    ],
                ]],
            ],
        ]));

        event(new SubscriptionUpdated($subscription->fresh(), [
            'data' => [
                'id' => 'sub_webhook_123',
                'status' => Subscription::STATUS_PAST_DUE,
                'custom_data' => [
                    'subscription_type' => Subscription::DEFAULT_TYPE,
                ],
                'items' => [[
                    'status' => 'past_due',
                    'quantity' => 2,
                    'price' => [
                        'id' => 'pri_webhook_123',
                        'product_id' => 'pro_webhook_123',
                    ],
                ]],
            ],
        ]));

        $this->assertDatabaseHas('subscriptions', [
            'billable_type' => User::class,
            'billable_id' => $user->id,
            'paddle_id' => 'sub_webhook_123',
            'status' => Subscription::STATUS_PAST_DUE,
        ]);

        $this->assertDatabaseHas('subscription_items', [
            'subscription_id' => $subscription->id,
            'price_id' => 'pri_webhook_123',
            'product_id' => 'pro_webhook_123',
            'status' => 'past_due',
            'quantity' => 2,
        ]);
    }

    public function test_manual_api_webhook_processes_transaction_completed_payload(): void
    {
        $user = User::factory()->create();

        $user->customer()->create([
            'paddle_id' => 'ctm_manual_123',
            'name' => $user->name,
            'email' => $user->email,
            'trial_ends_at' => null,
        ]);

        $response = $this->postJson(route('paddle.webhook.manual'), [
            'event_id' => 'evt_manual_123',
            'event_type' => 'transaction.completed',
            'data' => [
                'id' => 'txn_manual_123',
                'customer_id' => 'ctm_manual_123',
                'subscription_id' => 'sub_manual_123',
                'invoice_number' => 'INV-MANUAL-123',
                'status' => Transaction::STATUS_COMPLETED,
                'currency_code' => 'USD',
                'billed_at' => '2026-06-25T10:00:00Z',
                'details' => [
                    'totals' => [
                        'total' => '5900',
                        'tax' => '300',
                    ],
                ],
            ],
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('transactions', [
            'billable_type' => User::class,
            'billable_id' => $user->id,
            'paddle_id' => 'txn_manual_123',
            'paddle_subscription_id' => 'sub_manual_123',
        ]);

        $this->assertDatabaseHas('billing_records', [
            'user_id' => $user->id,
            'paddle_transaction_id' => 'txn_manual_123',
            'paddle_subscription_id' => 'sub_manual_123',
        ]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\BillingRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Paddle\Events\TransactionCompleted;
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
}

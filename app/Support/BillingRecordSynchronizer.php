<?php

namespace App\Support;

use App\Models\BillingRecord;
use Illuminate\Database\Eloquent\Model;
use Laravel\Paddle\Transaction;

class BillingRecordSynchronizer
{
    public function sync(Model $billable, Transaction $transaction, array $payload = []): BillingRecord
    {
        return BillingRecord::updateOrCreate(
            ['paddle_transaction_id' => $transaction->paddle_id],
            [
                'user_id' => $billable->getKey(),
                'paddle_subscription_id' => $transaction->paddle_subscription_id,
                'invoice_number' => $transaction->invoice_number,
                'status' => $transaction->status,
                'total' => $transaction->total,
                'tax' => $transaction->tax,
                'currency' => $transaction->currency,
                'billed_at' => $transaction->billed_at,
                'payload' => $payload ?: null,
            ],
        );
    }
}

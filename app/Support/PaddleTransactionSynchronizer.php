<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Laravel\Paddle\Transaction;

class PaddleTransactionSynchronizer
{
    public function sync(Model $billable, array $data): Transaction
    {
        return $billable->transactions()->updateOrCreate(
            ['paddle_id' => $data['id']],
            [
                'paddle_subscription_id' => $data['subscription_id'] ?? null,
                'invoice_number' => $data['invoice_number'] ?? null,
                'status' => $data['status'] ?? Transaction::STATUS_READY,
                'total' => data_get($data, 'details.totals.total', 0),
                'tax' => data_get($data, 'details.totals.tax', 0),
                'currency' => $data['currency_code'] ?? 'USD',
                'billed_at' => filled($data['billed_at'] ?? null)
                    ? Carbon::parse($data['billed_at'], 'UTC')
                    : null,
            ],
        );
    }
}

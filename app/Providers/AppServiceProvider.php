<?php

namespace App\Providers;

use App\Models\BillingRecord;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Paddle\Events\TransactionCompleted;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(TransactionCompleted::class, function (TransactionCompleted $event): void {
            BillingRecord::updateOrCreate(
                ['paddle_transaction_id' => $event->transaction->paddle_id],
                [
                    'user_id' => $event->billable->getKey(),
                    'paddle_subscription_id' => $event->transaction->paddle_subscription_id,
                    'invoice_number' => $event->transaction->invoice_number,
                    'status' => $event->transaction->status,
                    'total' => $event->transaction->total,
                    'tax' => $event->transaction->tax,
                    'currency' => $event->transaction->currency,
                    'billed_at' => $event->transaction->billed_at,
                    'payload' => $event->payload,
                ],
            );
        });
    }
}

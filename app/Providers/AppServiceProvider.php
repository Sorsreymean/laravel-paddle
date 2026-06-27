<?php

namespace App\Providers;

use App\Events\UserCreated;
use App\Listeners\CreatePaddleCustomer;
use App\Support\BillingRecordSynchronizer;
use App\Support\PaddleSubscriptionSynchronizer;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Laravel\Paddle\Events\SubscriptionCreated;
use Laravel\Paddle\Events\SubscriptionUpdated;
use Illuminate\Support\ServiceProvider;
use Laravel\Paddle\Events\TransactionCompleted;
use Laravel\Paddle\Events\TransactionUpdated;

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
        Event::listen(UserCreated::class, CreatePaddleCustomer::class);

        Event::listen(TransactionCompleted::class, function (TransactionCompleted $event): void {
            Log::info('Paddle webhook processed: transaction completed', [
                'billable_type' => $event->billable::class,
                'billable_id' => $event->billable->getKey(),
                'transaction_id' => $event->transaction->paddle_id,
                'subscription_id' => $event->transaction->paddle_subscription_id,
                'event_id' => $event->payload['event_id'] ?? null,
            ]);

            app(BillingRecordSynchronizer::class)->sync($event->billable, $event->transaction, $event->payload);
        });

        Event::listen(TransactionUpdated::class, function (TransactionUpdated $event): void {
            Log::info('Paddle webhook processed: transaction updated', [
                'billable_type' => $event->billable::class,
                'billable_id' => $event->billable->getKey(),
                'transaction_id' => $event->transaction->paddle_id,
                'subscription_id' => $event->transaction->paddle_subscription_id,
                'event_id' => $event->payload['event_id'] ?? null,
            ]);

            app(BillingRecordSynchronizer::class)->sync($event->billable, $event->transaction, $event->payload);
        });

        Event::listen(SubscriptionCreated::class, function (SubscriptionCreated $event): void {
            $data = $event->payload['data'] ?? null;

            Log::info('Paddle webhook processed: subscription created', [
                'billable_type' => $event->billable::class,
                'billable_id' => $event->billable->getKey(),
                'subscription_id' => $event->subscription->paddle_id,
                'status' => $event->subscription->status,
                'event_id' => $event->payload['event_id'] ?? null,
            ]);

            if (is_array($data)) {
                app(PaddleSubscriptionSynchronizer::class)->sync($event->billable, $data);
            }
        });

        Event::listen(SubscriptionUpdated::class, function (SubscriptionUpdated $event): void {
            $data = $event->payload['data'] ?? null;
            $billable = $event->subscription->billable;

            Log::info('Paddle webhook processed: subscription updated', [
                'billable_type' => $billable ? get_class($billable) : null,
                'billable_id' => $billable?->getKey(),
                'subscription_id' => $event->subscription->paddle_id,
                'status' => $event->subscription->status,
                'event_id' => $event->payload['event_id'] ?? null,
            ]);

            if ($billable && is_array($data)) {
                app(PaddleSubscriptionSynchronizer::class)->sync($billable, $data);
            }
        });
    }
}

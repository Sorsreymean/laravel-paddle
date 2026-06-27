<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Laravel\Paddle\Subscription;

class PaddleSubscriptionSynchronizer
{
    public function sync(Model $billable, array $data): Subscription
    {
        $subscription = $billable->subscriptions()->updateOrCreate(
            ['paddle_id' => $data['id']],
            [
                'type' => $data['custom_data']['subscription_type'] ?? Subscription::DEFAULT_TYPE,
                'status' => $data['status'],
                'trial_ends_at' => ($data['status'] ?? null) === Subscription::STATUS_TRIALING
                    ? $this->parseDate($data['next_billed_at'] ?? null)
                    : null,
                'paused_at' => $this->resolvePausedAt($data),
                'ends_at' => $this->resolveEndsAt($data),
            ],
        );

        $prices = [];

        foreach ($data['items'] ?? [] as $item) {
            $priceId = $item['price']['id'] ?? null;

            if (! $priceId) {
                continue;
            }

            $prices[] = $priceId;

            $subscription->items()->updateOrCreate(
                ['price_id' => $priceId],
                [
                    'product_id' => $item['price']['product_id'] ?? '',
                    'status' => $item['status'] ?? 'active',
                    'quantity' => $item['quantity'] ?? 1,
                ],
            );
        }

        if ($prices !== []) {
            $subscription->items()->whereNotIn('price_id', $prices)->delete();
        }

        return $subscription;
    }

    protected function resolvePausedAt(array $data): ?Carbon
    {
        if (isset($data['paused_at'])) {
            return $this->parseDate($data['paused_at']);
        }

        if (($data['scheduled_change']['action'] ?? null) === 'pause') {
            return $this->parseDate($data['scheduled_change']['effective_at'] ?? null);
        }

        return null;
    }

    protected function resolveEndsAt(array $data): ?Carbon
    {
        if (isset($data['canceled_at'])) {
            return $this->parseDate($data['canceled_at']);
        }

        if (($data['scheduled_change']['action'] ?? null) === 'cancel') {
            return $this->parseDate($data['scheduled_change']['effective_at'] ?? null);
        }

        return null;
    }

    protected function parseDate(?string $value): ?Carbon
    {
        return $value ? Carbon::parse($value, 'UTC') : null;
    }
}

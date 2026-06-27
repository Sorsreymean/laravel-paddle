<?php

namespace App\Listeners;

use App\Events\UserCreated;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreatePaddleCustomer
{
    public function handle(UserCreated $event): void
    {
        $user = $event->user;

        if (blank(config('cashier.api_key')) || blank($user->email) || $user->customer) {
            return;
        }

        try {
            $user->createAsCustomer();
        } catch (Throwable $exception) {
            Log::warning('Unable to create Paddle customer for user.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}

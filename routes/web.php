<?php

use App\Models\BillingRecord;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

Route::get('/', function () {
    $webhookPath = trim(config('cashier.path', 'paddle'), '/').'/webhook';
    $webhookUrl = config('cashier.webhook') ?: url($webhookPath);

    return view('home', [
        'paddleReady' => filled(config('cashier.client_side_token'))
            && filled(config('cashier.api_key'))
            && filled(config('cashier.webhook_secret'))
            && filled(config('services.paddle.default_price_id')),
        'priceId' => config('services.paddle.default_price_id'),
        'webhookUrl' => $webhookUrl,
        'sandbox' => (bool) config('cashier.sandbox'),
    ]);
})->name('home');

Route::get('/billing/checkout', function () {
    $priceId = config('services.paddle.default_price_id');

    abort_if(blank($priceId), 500, 'Set PADDLE_DEFAULT_PRICE_ID in your .env file before opening checkout.');

    $user = User::firstOrCreate(
        ['email' => config('services.paddle.demo_email')],
        [
            'name' => config('services.paddle.demo_name'),
            'password' => Hash::make(Str::random(32)),
        ],
    );

    $checkout = $user
        ->subscribe($priceId, 'default')
        ->returnTo(route('billing.success'));

    return view('billing', [
        'checkout' => $checkout,
        'priceId' => $priceId,
        'user' => $user,
    ]);
})->name('billing.checkout');

Route::get('/billing/success', function () {
    $user = User::where('email', config('services.paddle.demo_email'))->first();

    return view('success', [
        'user' => $user,
        'subscription' => $user?->subscription('default'),
        'billingRecord' => $user
            ? BillingRecord::whereBelongsTo($user)->latest('billed_at')->latest('id')->first()
            : null,
    ]);
})->name('billing.success');

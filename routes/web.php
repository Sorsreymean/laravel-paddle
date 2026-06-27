<?php

use App\Models\BillingRecord;
use App\Http\Controllers\ManualPaddleWebhookController;
use App\Models\Price;
use App\Models\User;
use App\Models\Product;
use App\Models\Plan;
use App\Support\BillingRecordSynchronizer;
use App\Support\PaddleProductSynchronizer;
use App\Support\PaddlePriceSynchronizer;
use App\Support\PaddleSubscriptionSynchronizer;
use App\Support\PaddleTransactionSynchronizer;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

Route::get('/', function () {
    $webhookUrl = route('cashier.webhook');
    $sandbox = (bool) config('cashier.sandbox');
    $products = collect();
    $productPrices = collect();
    $plans = collect();
    $productError = null;

    if (filled(config('cashier.api_key'))) {
        $baseUrl = $sandbox
            ? 'https://sandbox-api.paddle.com'
            : 'https://api.paddle.com';

        try {
            $response = Http::withToken(config('cashier.api_key'))
                ->acceptJson()
                ->timeout(15)
                ->get($baseUrl.'/products', [
                    'per_page' => 25,
                    'order_by' => 'created_at[DESC]',
                ]);

            if ($response->successful()) {
                $products = collect($response->json('data', []));
                app(PaddleProductSynchronizer::class)->syncMany($products);

                $priceResponse = Http::withToken(config('cashier.api_key'))
                    ->acceptJson()
                    ->timeout(15)
                    ->get($baseUrl.'/prices', [
                        'per_page' => 100,
                        'status' => 'active',
                    ]);

                if ($priceResponse->successful()) {
                    $prices = collect($priceResponse->json('data', []));
                    app(PaddlePriceSynchronizer::class)->syncMany($prices);

                    $productPrices = $prices
                        ->groupBy('product_id');
                } else {
                    $productError = $priceResponse->json('error.detail')
                        ?? $priceResponse->json('error.errors.0.detail')
                        ?? 'Unable to load prices from Paddle.';
                }
            } else {
                $productError = $response->json('error.detail')
                    ?? $response->json('error.errors.0.detail')
                    ?? 'Unable to load products from Paddle.';
            }
        } catch (ConnectionException) {
            $productError = 'Could not reach Paddle. Check your network or sandbox API settings.';
        }
    } else {
        $productError = 'Add your Paddle API key to load Paddle products.';
    }
    if (Schema::hasTable('plans') && Schema::hasTable('products') && Schema::hasTable('prices')) {
        $plans = Price::query()
            ->with('product:id,paddle_id,name')
            ->get();

        Plan::truncate();

        foreach ($plans as $planPrice) {
            if (! $planPrice->product?->name) {
                continue;
            }

            Plan::create([
                'name' => $planPrice->product->name,
                'price' => (float) ((data_get($planPrice->unit_price, 'amount', 0)) / 100 ) ?? 0,
                'bill_period' => (string) data_get($planPrice->billing_cycle, 'interval', 'one-time'),
                'period' => (string) data_get($planPrice->billing_cycle, 'frequency', 1),
                'paddle_id' => $planPrice->paddle_id,
            ]);
        }
    }
    $app_plan = Plan::where('bill_period','<>','one-time')->get();
    return view('home', [
        'paddleReady' => filled(config('cashier.client_side_token'))
            && filled(config('cashier.api_key'))
            && filled(config('cashier.webhook_secret'))
            && filled(config('services.paddle.default_price_id')),
        'priceId' => config('services.paddle.default_price_id'),
        'webhookUrl' => $webhookUrl,
        'sandbox' => $sandbox,
        'products' => $products,
        'productPrices' => $productPrices,
        'productError' => $productError,
        'plans' => $app_plan
    ]);
})->name('home');

Route::get('/users/create', function () {
    return view('users-create', [
        'sandbox' => (bool) config('cashier.sandbox'),
        'webhookUrl' => route('cashier.webhook'),
    ]);
})->name('users.create');

Route::post('/users', function () {
    $data = request()->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255', 'unique:users,email'],
    ]);

    $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Str::random(32),
    ]);

    $warning = blank(config('cashier.api_key'))
        ? 'User created locally. Paddle customer sync was skipped because the API key is missing.'
        : ($user->fresh()->customer
            ? null
            : 'User created locally. Paddle customer sync did not finish, so check the log or Paddle API settings.');

    $redirect = redirect()
        ->route('users.create')
        ->with('status', 'User created successfully for '.$user->email.'.');

    if (filled($warning)) {
        $redirect->with('warning', $warning);
    }

    return $redirect;
})->name('users.store');

Route::get('/transactions', function () {
    $sandbox = (bool) config('cashier.sandbox');
    $transactions = collect();
    $transactionError = null;

    if (filled(config('cashier.api_key'))) {
        $baseUrl = $sandbox
            ? 'https://sandbox-api.paddle.com'
            : 'https://api.paddle.com';

        try {
            $response = Http::withToken(config('cashier.api_key'))
                ->acceptJson()
                ->timeout(15)
                ->get($baseUrl.'/transactions', [
                    'per_page' => 25,
                    'order_by' => 'billed_at[DESC]',
                ]);

            if ($response->successful()) {
                $transactions = collect($response->json('data', []));

                $customerEmails = $transactions
                    ->pluck('customer_id')
                    ->filter()
                    ->unique()
                    ->mapWithKeys(function (string $customerId) use ($baseUrl) {
                        $customerResponse = Http::withToken(config('cashier.api_key'))
                            ->acceptJson()
                            ->timeout(15)
                            ->get($baseUrl.'/customers/'.$customerId);

                        return [
                            $customerId => $customerResponse->successful()
                                ? data_get($customerResponse->json('data'), 'email')
                                : null,
                        ];
                    });

                $transactions = $transactions->map(function (array $transaction) use ($customerEmails) {
                    $transaction['customer_email'] = $customerEmails->get($transaction['customer_id'] ?? '');

                    return $transaction;
                });
            } else {
                $transactionError = $response->json('error.detail')
                    ?? $response->json('error.errors.0.detail')
                    ?? 'Unable to load transactions from Paddle.';
            }
        } catch (ConnectionException) {
            $transactionError = 'Could not reach Paddle. Check your network or sandbox API settings.';
        }
    } else {
        $transactionError = 'Add your Paddle API key to load Paddle transactions.';
    }

    return view('transactions', [
        'sandbox' => $sandbox,
        'transactions' => $transactions,
        'transactionError' => $transactionError,
    ]);
})->name('transactions.index');

Route::get('/subscriptions', function () {
    $sandbox = (bool) config('cashier.sandbox');
    $subscriptions = collect();
    $subscriptionError = null;

    if (filled(config('cashier.api_key'))) {
        $baseUrl = $sandbox
            ? 'https://sandbox-api.paddle.com'
            : 'https://api.paddle.com';

        try {
            $response = Http::withToken(config('cashier.api_key'))
                ->acceptJson()
                ->timeout(15)
                ->get($baseUrl.'/subscriptions', [
                    'per_page' => 25,
                    // 'order_by' => 'created_at[DESC]',
                ]);

            if ($response->successful()) {
                $subscriptions = collect($response->json('data', []));

                $customerEmails = $subscriptions
                    ->pluck('customer_id')
                    ->filter()
                    ->unique()
                    ->mapWithKeys(function (string $customerId) use ($baseUrl) {
                        $customerResponse = Http::withToken(config('cashier.api_key'))
                            ->acceptJson()
                            ->timeout(15)
                            ->get($baseUrl.'/customers/'.$customerId);

                        return [
                            $customerId => $customerResponse->successful()
                                ? data_get($customerResponse->json('data'), 'email')
                                : null,
                        ];
                    });

                $subscriptions = $subscriptions->map(function (array $subscription) use ($customerEmails) {
                    $subscription['customer_email'] = $customerEmails->get($subscription['customer_id'] ?? '');

                    return $subscription;
                });
            } else {
                $subscriptionError = $response->json('error.detail')
                    ?? $response->json('error.errors.0.detail')
                    ?? 'Unable to load subscriptions from Paddle.';
            }
        } catch (ConnectionException) {
            $subscriptionError = 'Could not reach Paddle. Check your network or sandbox API settings.';
        }
    } else {
        $subscriptionError = 'Add your Paddle API key to load Paddle subscriptions.';
    }
    // dd($subscriptions);
    return view('subscriptions', [
        'sandbox' => $sandbox,
        'subscriptions' => $subscriptions,
        'subscriptionError' => $subscriptionError,
    ]);
})->name('subscriptions.index');

Route::get('/subscriptions/create', function () {
    $webhookUrl = route('cashier.webhook');
    $sandbox = (bool) config('cashier.sandbox');
    $products = collect();
    $productPrices = collect();
    $productError = null;
    $selectedPriceId = request('price_id', config('services.paddle.default_price_id'));

    if (filled(config('cashier.api_key'))) {
        $baseUrl = $sandbox
            ? 'https://sandbox-api.paddle.com'
            : 'https://api.paddle.com';

        try {
            $response = Http::withToken(config('cashier.api_key'))
                ->acceptJson()
                ->timeout(15)
                ->get($baseUrl.'/products', [
                    'per_page' => 25,
                    'order_by' => 'created_at[DESC]',
                ]);

            if ($response->successful()) {
                $products = collect($response->json('data', []));
                app(PaddleProductSynchronizer::class)->syncMany($products);

                $priceResponse = Http::withToken(config('cashier.api_key'))
                    ->acceptJson()
                    ->timeout(15)
                    ->get($baseUrl.'/prices', [
                        'per_page' => 100,
                        'status' => 'active',
                    ]);

                if ($priceResponse->successful()) {
                    $prices = collect($priceResponse->json('data', []));
                    app(PaddlePriceSynchronizer::class)->syncMany($prices);

                    $productPrices = $prices
                        ->groupBy('product_id');
                } else {
                    $productError = $priceResponse->json('error.detail')
                        ?? $priceResponse->json('error.errors.0.detail')
                        ?? 'Unable to load prices from Paddle.';
                }
            } else {
                $productError = $response->json('error.detail')
                    ?? $response->json('error.errors.0.detail')
                    ?? 'Unable to load products from Paddle.';
            }
        } catch (ConnectionException) {
            $productError = 'Could not reach Paddle. Check your network or sandbox API settings.';
        }
    } else {
        $productError = 'Add your Paddle API key to load Paddle products.';
    }

    return view('subscriptions-create', [
        'paddleReady' => filled(config('cashier.client_side_token'))
            && filled(config('cashier.api_key'))
            && filled(config('cashier.webhook_secret')),
        'priceId' => $selectedPriceId,
        'webhookUrl' => $webhookUrl,
        'sandbox' => $sandbox,
        'products' => $products,
        'productPrices' => $productPrices,
        'productError' => $productError,
    ]);
})->name('subscriptions.create');

Route::get('/billing', function () {
    $priceId = request('price_id', config('services.paddle.default_price_id'));

    abort_if(blank($priceId), 500, 'Set PADDLE_DEFAULT_PRICE_ID in your .env file before opening checkout.');

    // $customerData = [
    //     'name' => config('services.paddle.demo_name'),
    //     'email' => config('services.paddle.demo_email'),
    // ];

    // if (request()->isMethod('post') || request()->filled('name') || request()->filled('email')) {
    //     $customerData = request()->validate([
    //         'name' => ['required', 'string', 'max:255'],
    //         'email' => ['required', 'email', 'max:255'],
    //         'price_id' => ['required', 'string'],
    //     ]);
    // }

    $sandbox = (bool) config('cashier.sandbox');
    $selectedPrice = null;
    $selectedProduct = null;

    if (filled(config('cashier.api_key'))) {
        $baseUrl = $sandbox
            ? 'https://sandbox-api.paddle.com'
            : 'https://api.paddle.com';

        try {
            $priceResponse = Http::withToken(config('cashier.api_key'))
                ->acceptJson()
                ->timeout(15)
                ->get($baseUrl.'/prices/'.$priceId);

            if ($priceResponse->successful()) {
                $selectedPrice = $priceResponse->json('data');
                app(PaddlePriceSynchronizer::class)->sync($selectedPrice);

                if ($productId = data_get($selectedPrice, 'product_id')) {
                    $productResponse = Http::withToken(config('cashier.api_key'))
                        ->acceptJson()
                        ->timeout(15)
                        ->get($baseUrl.'/products/'.$productId);

                    if ($productResponse->successful()) {
                        $selectedProduct = $productResponse->json('data');
                        app(PaddleProductSynchronizer::class)->sync($selectedProduct);
                    }
                }
            }
        } catch (ConnectionException) {
            // Leave product details empty and continue to checkout.
        }
    }
    $user = User::get();
    // dd($user);
    // $user = User::firstOrCreate(
    //     ['email' => $customerData['email']],
    //     [
    //         'name' => $customerData['name'],
    //         'password' => Hash::make(Str::random(32)),
    //     ],
    // );

    // if ($user->name !== $customerData['name']) {
    //     $user->forceFill(['name' => $customerData['name']])->save();
    // }

    // $checkout = $user
    //     ->subscribe($priceId, 'default')
    //     ->returnTo(route('billing.success'));

    return view('billing', [
        // 'checkout' => $checkout,
        'priceId' => $priceId,
        'selectedPrice' => $selectedPrice,
        'selectedProduct' => $selectedProduct,
        'user' => $user,
    ]);
})->name('billing');
Route::match(['get', 'post'], '/billing/checkout', function () {
    $checkoutData = request()->validate([
        'price_id' => ['required', 'string'],
        'user' => ['required', 'integer', 'exists:users,id'],
    ]);

    $priceId = $checkoutData['price_id'];
    abort_if(blank($priceId), 500, 'Set PADDLE_DEFAULT_PRICE_ID in your .env file before opening checkout.');

    // $customerData = [
    //     'name' => config('services.paddle.demo_name'),
    //     'email' => config('services.paddle.demo_email'),
    // ];

    // if (request()->isMethod('post') || request()->filled('name') || request()->filled('email')) {
    //     $customerData = request()->validate([
    //         'name' => ['required', 'string', 'max:255'],
    //         'email' => ['required', 'email', 'max:255'],
    //         'price_id' => ['required', 'string'],
    //     ]);
    // }

    $sandbox = (bool) config('cashier.sandbox');
    $selectedPrice = null;
    $selectedProduct = null;

    if (filled(config('cashier.api_key'))) {
        $baseUrl = $sandbox
            ? 'https://sandbox-api.paddle.com'
            : 'https://api.paddle.com';

        try {
            $priceResponse = Http::withToken(config('cashier.api_key'))
                ->acceptJson()
                ->timeout(15)
                ->get($baseUrl.'/prices/'.$priceId);

            if ($priceResponse->successful()) {
                $selectedPrice = $priceResponse->json('data');
                app(PaddlePriceSynchronizer::class)->sync($selectedPrice);

                if ($productId = data_get($selectedPrice, 'product_id')) {
                    $productResponse = Http::withToken(config('cashier.api_key'))
                        ->acceptJson()
                        ->timeout(15)
                        ->get($baseUrl.'/products/'.$productId);

                    if ($productResponse->successful()) {
                        $selectedProduct = $productResponse->json('data');
                        app(PaddleProductSynchronizer::class)->sync($selectedProduct);
                    }
                }
            }
        } catch (ConnectionException) {
            // Leave product details empty and continue to checkout.
        }
    }
    // $user = User::get();
    // dd($user);
    // $user = User::firstOrCreate(
    //     ['email' => $customerData['email']],
    //     [
    //         'name' => $customerData['name'],
    //         'password' => Hash::make(Str::random(32)),
    //     ],
    // );
    $user = User::findOrFail($checkoutData['user']);
    request()->session()->put('billing_checkout_user_id', $user->id);

    // if ($user->name !== $customerData['name']) {
    //     $user->forceFill(['name' => $customerData['name']])->save();
    // }

    // dd(gettype($user));
    $checkout = $user
        ->subscribe($priceId, 'default')
        ->returnTo(route('billing.success'));
    // dd($checkout);
    return view('checkout', [
        'checkout' => $checkout,
        'priceId' => $priceId,
        // 'selectedPrice' => $selectedPrice,
        // 'selectedProduct' => $selectedProduct,
        // 'user' => $user,
    ]);
})->name('billing.checkout');
Route::get('/billing/success', function () {
    $selectedUserId = session('billing_checkout_user_id');

    $user = $selectedUserId
        ? User::find($selectedUserId)
        : User::where('email', config('services.paddle.demo_email'))->first();
    // dd($user->customer?->paddle_id);
    if ($user && filled(config('cashier.api_key')) && $user->customer?->paddle_id) {
        $baseUrl = config('cashier.sandbox')
            ? 'https://sandbox-api.paddle.com'
            : 'https://api.paddle.com';

        try {
            $transactionResponse = Http::withToken(config('cashier.api_key'))
                ->acceptJson()
                ->timeout(15)
                ->get($baseUrl.'/transactions', [
                    'customer_id' => $user->customer->paddle_id,
                    'per_page' => 1,
                    'order_by' => 'billed_at[DESC]',
                ]);
            
            if ($transactionResponse->successful()) {
                $remoteTransaction = collect($transactionResponse->json('data', []))->first();
                // dd($remoteTransaction);
                if ($remoteTransaction) {
                    app(PaddleTransactionSynchronizer::class)->sync($user, $remoteTransaction);
                }
            }
        } catch (ConnectionException) {
            // Keep the success page available even if Paddle sync is temporarily unreachable.
        }
    }

    $latestTransaction = $user
        ? $user->transactions()
            ->whereNotNull('paddle_subscription_id')
            ->latest('billed_at')
            ->latest('id')
            ->first()
        : null;
    if (! $latestTransaction && $user) {
        $latestTransaction = $user->transactions()
            ->latest('billed_at')
            ->latest('id')
            ->first();
    }

    if ($user && $latestTransaction) {
        app(BillingRecordSynchronizer::class)->sync($user, $latestTransaction);

        if (filled(config('cashier.api_key'))) {
            $baseUrl = config('cashier.sandbox')
                ? 'https://sandbox-api.paddle.com'
                : 'https://api.paddle.com';

            try {
                if (filled($latestTransaction->paddle_subscription_id)) {
                    $subscriptionResponse = Http::withToken(config('cashier.api_key'))
                        ->acceptJson()
                        ->timeout(15)
                        ->get($baseUrl.'/subscriptions/'.$latestTransaction->paddle_subscription_id);

                    if ($subscriptionResponse->successful()) {
                        app(PaddleSubscriptionSynchronizer::class)->sync($user, $subscriptionResponse->json('data', []));
                    }
                } elseif ($user->customer?->paddle_id) {
                    $subscriptionResponse = Http::withToken(config('cashier.api_key'))
                        ->acceptJson()
                        ->timeout(15)
                        ->get($baseUrl.'/subscriptions', [
                            'customer_id' => $user->customer->paddle_id,
                            'per_page' => 10,
                            'order_by' => 'created_at[DESC]',
                        ]);

                    $remoteSubscriptions = collect($subscriptionResponse->json('data', []));

                    $remoteSubscription = $subscriptionResponse->successful()
                        ? $remoteSubscriptions
                            ->sortByDesc(function (array $subscription) {
                                return strtotime(
                                    $subscription['started_at']
                                        ?? $subscription['created_at']
                                        ?? $subscription['next_billed_at']
                                        ?? '1970-01-01T00:00:00Z'
                                );
                            })
                            ->first(function (array $subscription) {
                                return in_array($subscription['status'] ?? null, ['active', 'trialing', 'past_due'], true);
                            })
                        ?? $remoteSubscriptions->first()
                        : null;
                    if ($remoteSubscription) {
                        app(PaddleSubscriptionSynchronizer::class)->sync($user, $remoteSubscription);
                    }
                }
            } catch (ConnectionException) {
                // Keep the success page available even if Paddle sync is temporarily unreachable.
            }
        }
    }
    $subscription = $user?->subscription('default');

    if ($subscription) {
        $subscription->load('items');
    }

    return view('success', [
        'user' => $user,
        'subscription' => $subscription,
        'billingRecord' => $user
            ? BillingRecord::whereBelongsTo($user)->latest('billed_at')->latest('id')->first()
            : null,
    ]);
})->name('billing.success');

Route::post('/paddle/webhook/manual', ManualPaddleWebhookController::class)
    ->name('paddle.webhook.manual');

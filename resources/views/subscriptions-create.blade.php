<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Create Subscription | {{ config('app.name') }}</title>
        <style>
            :root {
                --ink: #1b2430;
                --muted: #667085;
                --line: rgba(27, 36, 48, 0.12);
                --surface: rgba(255, 255, 255, 0.84);
                --surface-strong: #ffffff;
                --accent: #0f766e;
                --accent-strong: #115e59;
                --accent-soft: rgba(15, 118, 110, 0.1);
                --warn: #b45309;
                --danger: #b42318;
                --shadow: 0 26px 80px rgba(27, 36, 48, 0.12);
            }

            * { box-sizing: border-box; }

            body {
                margin: 0;
                min-height: 100vh;
                font-family: "Segoe UI", sans-serif;
                color: var(--ink);
                background:
                    radial-gradient(circle at top left, rgba(251, 191, 36, 0.28), transparent 25%),
                    radial-gradient(circle at right center, rgba(14, 165, 233, 0.18), transparent 22%),
                    linear-gradient(155deg, #fff8eb, #f3fbf8 44%, #eef4ff);
            }

            .shell {
                width: min(1180px, calc(100% - 32px));
                margin: 0 auto;
                padding: 32px 0 56px;
            }

            .hero,
            .form-card,
            .catalog-card {
                background: var(--surface);
                backdrop-filter: blur(14px);
                border: 1px solid var(--line);
                border-radius: 28px;
                box-shadow: var(--shadow);
            }

            .hero {
                padding: 32px;
                display: grid;
                gap: 20px;
            }

            .eyebrow,
            .nav-link,
            .status,
            .pill {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                width: fit-content;
                border-radius: 999px;
                border: 1px solid var(--line);
                background: rgba(255, 255, 255, 0.72);
            }

            .eyebrow,
            .nav-link,
            .pill {
                padding: 8px 14px;
                font-size: 12px;
                font-weight: 700;
                letter-spacing: 0.1em;
                text-transform: uppercase;
            }

            .nav-row {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
            }

            .nav-link {
                color: var(--ink);
                text-decoration: none;
            }

            h1,
            h2,
            h3,
            p {
                margin: 0;
            }

            h1 {
                font-size: clamp(2rem, 5vw, 3.1rem);
                line-height: 0.94;
            }

            p {
                color: var(--muted);
                line-height: 1.7;
            }

            .hero-grid {
                display: grid;
                grid-template-columns: minmax(0, 1.4fr) minmax(280px, 0.8fr);
                gap: 18px;
            }

            .info-panel {
                display: grid;
                gap: 14px;
                padding: 22px;
                border-radius: 24px;
                border: 1px solid rgba(15, 118, 110, 0.12);
                background: linear-gradient(180deg, rgba(15, 118, 110, 0.11), rgba(255, 255, 255, 0.74));
            }

            .meta-label {
                display: block;
                margin-bottom: 6px;
                font-size: 12px;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                color: var(--muted);
            }

            code {
                font-family: Consolas, monospace;
                color: var(--accent-strong);
                word-break: break-all;
            }

            .status {
                padding: 10px 14px;
                font-size: 14px;
                font-weight: 600;
            }

            .dot {
                width: 10px;
                height: 10px;
                border-radius: 999px;
                background: #16a34a;
            }

            .dot-warn {
                background: var(--warn);
            }

            .content {
                margin-top: 22px;
                display: grid;
                grid-template-columns: minmax(0, 1.05fr) minmax(320px, 0.95fr);
                gap: 22px;
                align-items: start;
            }

            .form-card,
            .catalog-card {
                padding: 28px;
            }

            .card-head {
                display: flex;
                justify-content: space-between;
                gap: 12px;
                align-items: flex-start;
                margin-bottom: 22px;
            }

            .card-head h2 {
                font-size: 1.35rem;
            }

            .form-grid {
                display: grid;
                gap: 18px;
            }

            label {
                display: grid;
                gap: 8px;
                font-size: 14px;
                font-weight: 600;
            }

            input,
            select {
                width: 100%;
                padding: 14px 16px;
                border-radius: 16px;
                border: 1px solid var(--line);
                background: rgba(255, 255, 255, 0.92);
                color: var(--ink);
                font: inherit;
            }

            input:focus,
            select:focus {
                outline: 2px solid rgba(15, 118, 110, 0.16);
                border-color: rgba(15, 118, 110, 0.5);
            }

            .hint,
            .error-text,
            .empty,
            .product-note {
                font-size: 14px;
            }

            .hint,
            .product-note,
            .empty {
                color: var(--muted);
            }

            .error-text {
                color: var(--danger);
            }

            .error-box {
                margin-bottom: 18px;
                padding: 14px 16px;
                border-radius: 16px;
                border: 1px solid rgba(180, 35, 24, 0.15);
                background: rgba(180, 35, 24, 0.06);
                color: var(--danger);
            }

            .summary {
                display: grid;
                gap: 12px;
                padding: 18px;
                border-radius: 20px;
                border: 1px solid var(--line);
                background: rgba(255, 255, 255, 0.72);
            }

            .summary-row {
                display: flex;
                justify-content: space-between;
                gap: 12px;
                align-items: baseline;
            }

            .summary-row strong {
                font-size: 1rem;
            }

            .action-row {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                align-items: center;
            }

            .button,
            .button-secondary {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 14px 22px;
                border-radius: 999px;
                font-size: 14px;
                font-weight: 700;
                text-decoration: none;
            }

            .button {
                border: 0;
                color: #fff;
                cursor: pointer;
                background: linear-gradient(135deg, var(--accent), var(--accent-strong));
                box-shadow: 0 18px 36px rgba(15, 118, 110, 0.2);
            }

            .button-secondary {
                color: var(--ink);
                border: 1px solid var(--line);
                background: rgba(255, 255, 255, 0.84);
            }

            .catalog-list {
                display: grid;
                gap: 14px;
            }

            .catalog-item {
                padding: 18px;
                border-radius: 20px;
                border: 1px solid var(--line);
                background: rgba(255, 255, 255, 0.72);
            }

            .catalog-item h3 {
                margin-bottom: 6px;
                font-size: 1rem;
            }

            .price-list {
                display: grid;
                gap: 8px;
                margin-top: 12px;
            }

            .price-chip {
                display: flex;
                justify-content: space-between;
                gap: 12px;
                align-items: center;
                padding: 10px 12px;
                border-radius: 14px;
                background: var(--accent-soft);
                color: var(--accent-strong);
                font-size: 13px;
                font-weight: 700;
            }

            @media (max-width: 920px) {
                .shell {
                    width: min(100% - 20px, 1180px);
                    padding: 20px 0 40px;
                }

                .hero,
                .form-card,
                .catalog-card {
                    border-radius: 22px;
                }

                .hero,
                .form-card,
                .catalog-card {
                    padding: 22px;
                }

                .hero-grid,
                .content {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </head>
    <body>
        @php
            $priceOptions = $products->flatMap(function (array $product) use ($productPrices) {
                return $productPrices->get($product['id'], collect())->map(function (array $price) use ($product) {
                    $amount = (int) data_get($price, 'unit_price.amount', 0);
                    $currency = data_get($price, 'unit_price.currency_code', 'USD');
                    $interval = data_get($price, 'billing_cycle.interval', 'one-time');
                    $frequency = data_get($price, 'billing_cycle.frequency');

                    return [
                        'id' => $price['id'] ?? null,
                        'product_name' => $product['name'] ?? 'Unnamed product',
                        'description' => $product['description'] ?? 'Paddle catalog item',
                        'status' => $product['status'] ?? 'unknown',
                        'label' => ($product['name'] ?? 'Unnamed product').' - '.$currency.' '.number_format($amount / 100, 2),
                        'billing' => $frequency ? 'Every '.$frequency.' '.$interval : $interval,
                    ];
                });
            })->filter(fn (array $price) => filled($price['id']))->values();

            $selectedOption = $priceOptions->firstWhere('id', old('price_id', $priceId));
        @endphp

        <main class="shell">
            <section class="hero">
                <div class="eyebrow">Paddle Subscription Form</div>
                <div class="nav-row">
                    <a class="nav-link" href="{{ route('home') }}">Products</a>
                    <a class="nav-link" href="{{ route('transactions.index') }}">Transactions</a>
                    <a class="nav-link" href="{{ route('subscriptions.index') }}">Subscriptions</a>
                    <a class="nav-link" href="{{ route('subscriptions.create') }}">Create</a>
                    <a class="nav-link" href="{{ route('users.create') }}">Create User</a>
                </div>

                <div class="hero-grid">
                    <div>
                        <h1>Create a subscription checkout.</h1>
                        <p>
                            Enter the customer details, choose an active Paddle price, and continue to the checkout preview before payment.
                        </p>
                    </div>

                    <aside class="info-panel">
                        <div>
                            <span class="meta-label">Environment</span>
                            <strong>{{ $sandbox ? 'Sandbox' : 'Live' }}</strong>
                        </div>
                        <div>
                            <span class="meta-label">Webhook</span>
                            <code>{{ $webhookUrl }}</code>
                        </div>
                        <div>
                            <span class="meta-label">Checkout status</span>
                            <strong>{{ $paddleReady ? 'Configured' : 'Needs setup' }}</strong>
                        </div>
                    </aside>
                </div>

                <div class="status">
                    <span class="{{ $productError ? 'dot dot-warn' : 'dot' }}"></span>
                    {{ $productError ?: 'Choose a price from Paddle to open a new subscription checkout.' }}
                </div>
            </section>

            <section class="content">
                <div class="form-card">
                    <div class="card-head">
                        <div>
                            <h2>Customer and plan</h2>
                            <p>Use this form to create a checkout session for a specific customer.</p>
                        </div>
                        <span class="pill">{{ $priceOptions->count() }} prices</span>
                    </div>

                    @if ($errors->any())
                        <div class="error-box">Please correct the highlighted fields and try again.</div>
                    @endif

                    <form class="form-grid" method="POST" action="{{ route('billing.checkout') }}">
                        @csrf

                        <label>
                            Customer name
                            <input type="text" name="name" value="{{ old('name', config('services.paddle.demo_name')) }}" placeholder="Enter customer name">
                            @error('name')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </label>

                        <label>
                            Customer email
                            <input type="email" name="email" value="{{ old('email', config('services.paddle.demo_email')) }}" placeholder="customer@example.com">
                            @error('email')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </label>

                        <label>
                            Subscription plan
                            <select name="price_id">
                                <option value="">Select an active Paddle price</option>
                                @foreach ($priceOptions as $option)
                                    <option value="{{ $option['id'] }}" @selected(old('price_id', $priceId) === $option['id'])>
                                        {{ $option['label'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('price_id')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                            <span class="hint">Only active prices returned by Paddle are shown here.</span>
                        </label>

                        <div class="summary">
                            <div class="summary-row">
                                <span class="meta-label">Selected product</span>
                                <strong>{{ $selectedOption['product_name'] ?? 'No price selected' }}</strong>
                            </div>
                            <div class="summary-row">
                                <span class="meta-label">Billing cycle</span>
                                <strong>{{ $selectedOption['billing'] ?? 'Choose a price first' }}</strong>
                            </div>
                            <p class="product-note">{{ $selectedOption['description'] ?? 'The checkout preview will show the full product details.' }}</p>
                        </div>

                        <div class="action-row">
                            <button class="button" type="submit">Continue to checkout</button>
                            <a class="button-secondary" href="{{ route('home') }}">Back to products</a>
                        </div>
                    </form>
                </div>

                <aside class="catalog-card">
                    <div class="card-head">
                        <div>
                            <h2>Available catalog</h2>
                            <p>Quick view of the products and prices currently returned by Paddle.</p>
                        </div>
                        <span class="pill">{{ $sandbox ? 'Sandbox' : 'Live' }}</span>
                    </div>

                    @if ($productError && $products->isEmpty())
                        <div class="error-box">{{ $productError }}</div>
                    @elseif ($products->isEmpty())
                        <div class="empty">No Paddle products were returned yet. Create a product and price in Paddle, then refresh this page.</div>
                    @else
                        <div class="catalog-list">
                            @foreach ($products as $product)
                                @php
                                    $prices = $productPrices->get($product['id'], collect());
                                @endphp
                                <article class="catalog-item">
                                    <h3>{{ $product['name'] ?? 'Unnamed product' }}</h3>
                                    <p>{{ $product['description'] ?? 'Paddle catalog item' }}</p>

                                    @if ($prices->isEmpty())
                                        <p class="product-note" style="margin-top: 12px;">No active prices are attached to this product yet.</p>
                                    @else
                                        <div class="price-list">
                                            @foreach ($prices as $price)
                                                @php
                                                    $amount = (int) data_get($price, 'unit_price.amount', 0);
                                                    $currency = data_get($price, 'unit_price.currency_code', 'USD');
                                                    $interval = data_get($price, 'billing_cycle.interval', 'one-time');
                                                    $frequency = data_get($price, 'billing_cycle.frequency');
                                                @endphp
                                                <div class="price-chip">
                                                    <span>{{ $currency }} {{ number_format($amount / 100, 2) }}</span>
                                                    <span>{{ $frequency ? 'Every '.$frequency.' '.$interval : $interval }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </article>
                            @endforeach
                        </div>
                    @endif
                </aside>
            </section>
        </main>
    </body>
</html>

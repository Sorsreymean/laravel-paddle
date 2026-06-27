<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name') }}</title>
        <style>
            :root {
                --ink: #12211c;
                --muted: #59706a;
                --line: rgba(18, 33, 28, 0.12);
                --surface: rgba(255, 251, 245, 0.82);
                --surface-strong: #fffdf8;
                --accent: #0f766e;
                --accent-soft: rgba(15, 118, 110, 0.1);
                --warn: #b45309;
                --danger: #b91c1c;
                --shadow: 0 24px 70px rgba(18, 33, 28, 0.12);
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                min-height: 100vh;
                font-family: "Segoe UI", sans-serif;
                color: var(--ink);
                background:
                    radial-gradient(circle at top left, rgba(251, 191, 36, 0.32), transparent 24%),
                    radial-gradient(circle at right center, rgba(45, 212, 191, 0.18), transparent 20%),
                    linear-gradient(145deg, #fff3dd, #eefbf7 48%, #eef6ff);
            }

            .shell {
                width: min(1240px, calc(100% - 32px));
                margin: 0 auto;
                padding: 32px 0 56px;
            }

            .hero,
            .table-card {
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

            .eyebrow {
                display: inline-flex;
                width: fit-content;
                align-items: center;
                gap: 10px;
                padding: 8px 14px;
                border-radius: 999px;
                border: 1px solid var(--line);
                background: rgba(255, 255, 255, 0.72);
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
                display: inline-flex;
                align-items: center;
                gap: 10px;
                width: fit-content;
                padding: 8px 14px;
                border-radius: 999px;
                border: 1px solid var(--line);
                background: rgba(255, 255, 255, 0.72);
                color: var(--ink);
                font-size: 12px;
                font-weight: 700;
                letter-spacing: 0.1em;
                text-decoration: none;
                text-transform: uppercase;
            }

            h1 {
                margin: 0;
                font-size: 2rem;
                line-height: 0.95;
                /* max-width: 12ch; */
            }

            p {
                margin: 0;
                color: var(--muted);
                line-height: 1.7;
            }

            .hero-top {
                display: grid;
                grid-template-columns: minmax(0, 1.6fr) minmax(280px, 0.9fr);
                gap: 20px;
            }

            .stats {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 14px;
            }

            .stat {
                padding: 18px;
                border-radius: 22px;
                border: 1px solid var(--line);
                background: rgba(255, 255, 255, 0.72);
            }

            .stat-label {
                display: block;
                margin-bottom: 8px;
                font-size: 12px;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                color: var(--muted);
            }

            .stat-value {
                font-size: 1.7rem;
                font-weight: 700;
            }

            .meta {
                display: grid;
                gap: 12px;
                align-content: start;
                padding: 22px;
                border-radius: 24px;
                background: linear-gradient(180deg, rgba(15, 118, 110, 0.12), rgba(255, 255, 255, 0.72));
                border: 1px solid rgba(15, 118, 110, 0.12);
            }

            .meta-row {
                display: grid;
                gap: 6px;
            }

            .meta-label {
                font-size: 12px;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                color: var(--muted);
            }

            code {
                font-family: Consolas, monospace;
                font-size: 0.95em;
                color: var(--accent);
                word-break: break-all;
            }

            .status {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                width: fit-content;
                padding: 10px 14px;
                border-radius: 999px;
                border: 1px solid var(--line);
                background: rgba(255, 255, 255, 0.72);
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

            .table-card {
                margin-top: 22px;
                overflow: hidden;
            }

            .table-head {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 16px;
                padding: 24px 28px 18px;
                border-bottom: 1px solid var(--line);
            }

            .table-head h2 {
                margin: 0;
                font-size: 1.3rem;
            }

            .pill {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 8px 12px;
                border-radius: 999px;
                border: 1px solid var(--line);
                background: var(--surface-strong);
                color: var(--muted);
                font-size: 13px;
            }

            .table-wrap {
                overflow-x: auto;
            }

            table {
                width: 100%;
                min-width: 980px;
                border-collapse: collapse;
            }

            th,
            td {
                padding: 16px 28px;
                text-align: left;
                border-bottom: 1px solid var(--line);
                vertical-align: top;
            }

            th {
                font-size: 12px;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                color: var(--muted);
                background: rgba(255, 255, 255, 0.62);
            }

            tbody tr:hover {
                background: rgba(255, 255, 255, 0.55);
            }

            .tx-title {
                font-weight: 700;
            }

            .tx-subtitle,
            .empty,
            .error {
                font-size: 14px;
                color: var(--muted);
            }

            .badge {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 7px 10px;
                border-radius: 999px;
                background: var(--accent-soft);
                color: var(--accent);
                font-size: 12px;
                font-weight: 700;
                text-transform: capitalize;
            }

            .badge.paid,
            .badge.completed,
            .badge.billed {
                background: rgba(22, 163, 74, 0.12);
                color: #166534;
            }

            .badge.past_due,
            .badge.ready {
                background: rgba(180, 83, 9, 0.12);
                color: var(--warn);
            }

            .badge.canceled,
            .badge.failed {
                background: rgba(185, 28, 28, 0.12);
                color: var(--danger);
            }

            .money {
                font-weight: 700;
            }

            .action-link {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 10px 14px;
                border-radius: 999px;
                background: linear-gradient(135deg, var(--accent), #155e75);
                color: #fff;
                font-size: 13px;
                font-weight: 700;
                text-decoration: none;
                white-space: nowrap;
                box-shadow: 0 14px 30px rgba(15, 118, 110, 0.18);
            }

            .action-link.secondary {
                background: rgba(255, 255, 255, 0.78);
                color: var(--ink);
                border: 1px solid var(--line);
                box-shadow: none;
            }

            .empty,
            .error {
                padding: 28px;
            }

            .error {
                color: var(--danger);
                background: rgba(185, 28, 28, 0.05);
            }

            @media (max-width: 900px) {
                .shell {
                    width: min(100% - 20px, 1240px);
                    padding: 20px 0 40px;
                }

                .hero,
                .table-card {
                    border-radius: 22px;
                }

                .hero {
                    padding: 22px;
                }

                .hero-top,
                .stats {
                    grid-template-columns: 1fr;
                }

                .table-head {
                    padding: 20px;
                    align-items: flex-start;
                    flex-direction: column;
                }

                th,
                td {
                    padding: 14px 20px;
                }
            }
        </style>
    </head>
    <body>
        <main>
            <section class="hero">
                <div class="eyebrow">Paddle Sandbox Ledger</div>
                <div class="nav-row">
                    <a class="nav-link" href="{{ route('home') }}">Products</a>
                    {{-- <a class="nav-link" href="{{ route('transactions.index') }}">Transactions</a> --}}
                    {{-- <a class="nav-link" href="{{ route('subscriptions.index') }}">Subscriptions</a> --}}
                    {{-- <a class="nav-link" href="{{ route('subscriptions.create') }}">Create</a> --}}
                    <a class="nav-link" href="{{ route('users.create') }}">Create Customer</a>
                </div>
                <div class="hero-top">
                    <div>
                        <h1>Subscription with Paddle</h1>
                        <p>
                            This dashboard pulls product records directly from Paddle's API and lays them out in
                            a clean Blade data table for quick review and checkout entry.
                        </p>
                    </div>

                    {{-- <aside class="meta">
                        <div class="meta-row">
                            <span class="meta-label">Environment</span>
                            <strong>{{ $sandbox ? 'Sandbox' : 'Live' }}</strong>
                        </div>
                        <div class="meta-row">
                            <span class="meta-label">Webhook</span>
                            <code>{{ $webhookUrl }}</code>
                        </div>
                        <div class="meta-row">
                            <span class="meta-label">Price ID</span>
                            <code>{{ $priceId ?: 'missing PADDLE_DEFAULT_PRICE_ID' }}</code>
                        </div>
                    </aside> --}}
                </div>

                <div class="stats">
                    {{-- <article class="stat">
                        <span class="stat-label">Products loaded</span>
                        <div class="stat-value">{{ $products->count() }}</div>
                    </article> --}}
                    <article class="stat">
                        <span class="stat-label">API status</span>
                        <div class="stat-value">{{ $productError ? 'Issue' : 'Ready' }}</div>
                    </article>
                    <article class="stat">
                        <span class="stat-label">Checkout config</span>
                        <div class="stat-value">{{ $paddleReady ? 'Complete' : 'Pending' }}</div>
                    </article>
                </div>

                <div class="status">
                    <span class="{{ $productError ? 'dot dot-warn' : 'dot' }}"></span>
                    {{ $productError ?: 'Connected to Paddle and showing the newest product records.' }}
                </div>
            </section>

            <section class="table-card">
                <div class="table-head">
                    <div>
                        <h2>Plan Data Table</h2>
                    </div>

                    <span class="pill">{{ $sandbox ? 'Sandbox mode' : 'Live mode' }}</span>
                </div>

                @if ($productError && $products->isEmpty())
                    <div class="error">{{ $productError }}</div>
                @elseif ($products->isEmpty())
                    <div class="empty">No Paddle products were returned yet. Create a sandbox product in Paddle and refresh this page.</div>
                @else
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Plan Name</th>
                                    <th>Price</th>
                                    <th>Bill Period</th>
                                    <th>Period</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($plans as $plan)
                                @php
                                // dd($plan);
                                    $checkoutUrl = route('billing', ['price_id' => $plan['paddle_id']]);
                                @endphp
                                {{-- {{$plan['name']}} --}}
                                        <tr>
                                            <td>
                                                <div class="tx-title">{{ $plan['name'] }}</div>
                                            </td>
                                            <td>
                                                <div class="tx-title">{{ $plan['price'] ?? 0 }}</div>
                                            </td>
                                            <td>
                                                <div class="tx-title">{{ $plan['bill_period'] }}</div>
                                            </td>
                                            <td>
                                                <div class="tx-title">{{ $plan['period'] }}</div>
                                            </td>
                                            <td>
                                                <a
                                                    class="action-link secondary"
                                                    href="{{ $checkoutUrl }}"
                                                >
                                                    {{ 'subscribe'  }}
                                                </a>
                                            </td>
                                        </tr>
                                @endforeach
                                {{-- @foreach ($products as $product)
                                    @php
                                        $status = $product['status'] ?? 'unknown';
                                        $description = $product['description'] ?? 'No description provided';
                                        $prices = $productPrices->get($product['id'], collect());
                                        $taxCategory = $product['tax_category'] ?? 'unspecified';
                                        $createdAt = $product['created_at'] ?? null;
                                        $isActive = strtolower($status) === 'active';
                                    @endphp
                                    @forelse ($prices as $price)
                                        @php
                                            $checkoutUrl = route('subscriptions.create', ['price_id' => $price['id']]);
                                            $amount = (int) ($price['unit_price']['amount'] ?? 0);
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="tx-title">{{ $product['name'] ?? 'Unnamed product' }}</div>
                                                <div class="tx-subtitle">{{ $product['custom_data']['category'] ?? 'Paddle catalog item' }}</div>
                                            </td>
                                            <td>
                                                <span class="badge {{ str_replace(' ', '_', strtolower($status)) }}">{{ str_replace('_', ' ', $status) }}</span>
                                            </td>
                                            <td>
                                                <div class="tx-title">{{ $description }}</div>
                                                <div class="tx-subtitle">Image {{ !empty($product['image_url']) ? 'available' : 'not set' }}</div>
                                            </td>
                                            <td>
                                                <div class="tx-title">{{ ($price['unit_price']['currency_code'] ?? 'USD').' '.number_format($amount / 100, 2) }}</div>
                                                <div class="tx-subtitle">{{ $price['billing_cycle']['interval'] ?? 'one-time' }}</div>
                                            </td>
                                            <td>
                                                <div class="tx-title">{{ str_replace('_', ' ', $taxCategory) }}</div>
                                                <div class="tx-subtitle">Type {{ $product['type'] ?? 'standard' }}</div>
                                            </td>
                                            <td>
                                                <a
                                                    class="action-link {{ $isActive ? '' : 'secondary' }}"
                                                    href="{{ $isActive ? $checkoutUrl : '#' }}"
                                                >
                                                    {{ $isActive ? 'Create subscription' : 'Unavailable' }}
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td>
                                                <div class="tx-title">{{ $product['name'] ?? 'Unnamed product' }}</div>
                                                <div class="tx-subtitle">{{ $product['custom_data']['category'] ?? 'Paddle catalog item' }}</div>
                                            </td>
                                            <td>
                                                <span class="badge {{ str_replace(' ', '_', strtolower($status)) }}">{{ str_replace('_', ' ', $status) }}</span>
                                            </td>
                                            <td>
                                                <div class="tx-title">{{ $description }}</div>
                                                <div class="tx-subtitle">Image {{ !empty($product['image_url']) ? 'available' : 'not set' }}</div>
                                            </td>
                                            <td>
                                                <div class="tx-title">No price</div>
                                                <div class="tx-subtitle">Create a Paddle price for this product</div>
                                            </td>
                                            <td>
                                                <div class="tx-title">Unavailable</div>
                                                <div class="tx-subtitle">No price id found</div>
                                            </td>
                                            <td>
                                                <div class="tx-title">{{ str_replace('_', ' ', $taxCategory) }}</div>
                                                <div class="tx-subtitle">Type {{ $product['type'] ?? 'standard' }}</div>
                                            </td>
                                            <td>
                                                <a class="action-link secondary" href="#">
                                                    Unavailable
                                                </a>
                                            </td>
                                        </tr>
                                    @endforelse
                                @endforeach --}}
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </main>
    </body>
</html>

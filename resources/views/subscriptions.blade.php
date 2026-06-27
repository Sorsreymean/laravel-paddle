<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Subscriptions | {{ config('app.name') }}</title>
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

            * { box-sizing: border-box; }

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

            .eyebrow,
            .pill,
            .nav-link,
            .status {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                width: fit-content;
                border-radius: 999px;
                border: 1px solid var(--line);
                background: rgba(255, 255, 255, 0.72);
            }

            .eyebrow,
            .pill,
            .nav-link {
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

            h1 {
                margin: 0;
                font-size: 2rem;
                line-height: 0.95;
            }

            p {
                margin: 0;
                color: var(--muted);
                line-height: 1.7;
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

            .table-wrap {
                overflow-x: auto;
            }

            table {
                width: 100%;
                min-width: 1200px;
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

            .badge.active,
            .badge.trialing {
                background: rgba(22, 163, 74, 0.12);
                color: #166534;
            }

            .badge.paused,
            .badge.past_due {
                background: rgba(180, 83, 9, 0.12);
                color: var(--warn);
            }

            .badge.canceled,
            .badge.inactive {
                background: rgba(185, 28, 28, 0.12);
                color: var(--danger);
            }

            .empty,
            .error {
                padding: 28px;
            }

            .error {
                color: var(--danger);
                background: rgba(185, 28, 28, 0.05);
            }

            code {
                font-family: Consolas, monospace;
                font-size: 0.95em;
                color: var(--accent);
                word-break: break-all;
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
        <main class="shell">
            <section class="hero">
                <div class="eyebrow">Paddle Subscriptions</div>
                <div class="nav-row">
                    <a class="nav-link" href="{{ route('home') }}">Products</a>
                    <a class="nav-link" href="{{ route('transactions.index') }}">Transactions</a>
                    <a class="nav-link" href="{{ route('subscriptions.index') }}">Subscriptions</a>
                    <a class="nav-link" href="{{ route('subscriptions.create') }}">Create</a>
                    <a class="nav-link" href="{{ route('users.create') }}">Create User</a>
                </div>

                <div>
                    <h1>Subscriptions from Paddle</h1>
                    <p>
                        This page pulls the latest subscription records directly from Paddle so you can review customer,
                        items, status, billing cycle, and next renewal details in one place.
                    </p>
                </div>

                <div class="stats">
                    <article class="stat">
                        <span class="stat-label">Subscriptions loaded</span>
                        <div class="stat-value">{{ $subscriptions->count() }}</div>
                    </article>
                    <article class="stat">
                        <span class="stat-label">API status</span>
                        <div class="stat-value">{{ $subscriptionError ? 'Issue' : 'Ready' }}</div>
                    </article>
                    <article class="stat">
                        <span class="stat-label">Mode</span>
                        <div class="stat-value">{{ $sandbox ? 'Sandbox' : 'Live' }}</div>
                    </article>
                </div>

                <div class="status">
                    <span class="{{ $subscriptionError ? 'dot dot-warn' : 'dot' }}"></span>
                    {{ $subscriptionError ?: 'Connected to Paddle and showing the newest subscriptions.' }}
                </div>
            </section>

            <section class="table-card">
                <div class="table-head">
                    <div>
                        <h2>Subscription Data Table</h2>
                        <p>Recent rows returned from <code>/subscriptions</code> on the Paddle API.</p>
                    </div>

                    <span class="pill">{{ $sandbox ? 'Sandbox mode' : 'Live mode' }}</span>
                </div>

                @if ($subscriptionError && $subscriptions->isEmpty())
                    <div class="error">{{ $subscriptionError }}</div>
                @elseif ($subscriptions->isEmpty())
                    <div class="empty">No Paddle subscriptions were returned yet. Complete a subscription checkout in Paddle and refresh this page.</div>
                @else
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Subscription</th>
                                    <th>Status</th>
                                    <th>Customer</th>
                                    <th>Items</th>
                                    <th>Billing</th>
                                    <th>Next Bill</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($subscriptions as $subscription)
                                
                                    @php
                                    // dd($subscription);
                                        $status = $subscription['status'] ?? 'unknown';
                                        $items = collect($subscription['items'] ?? []);
                                        $firstItem = $items->first();
                                        $interval = data_get($firstItem, 'price.billing_cycle.interval', 'one-time');
                                        $frequency = data_get($firstItem, 'price.billing_cycle.frequency');
                                        $nextBilledAt = data_get($subscription, 'next_billed_at');
                                        $startedAt = data_get($subscription, 'started_at') ?? data_get($subscription, 'created_at');
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="tx-title">{{ data_get($subscription, 'id', data_get($firstItem, 'price.product.name', 'Unknown subscription')) }}</div>
                                            <div class="tx-subtitle">Started {{ $startedAt ? \Illuminate\Support\Carbon::parse($startedAt)->format('d M Y') : 'Unknown' }}</div>
                                        </td>
                                        <td>
                                            <span class="badge {{ str_replace(' ', '_', strtolower($status)) }}">{{ str_replace('_', ' ', $status) }}</span>
                                        </td>
                                        <td>
                                            <div class="tx-title">{{ data_get($subscription, 'customer_email') ?: data_get($subscription, 'customer_id', 'Unknown customer') }}</div>
                                            <div class="tx-subtitle">Address {{ data_get($subscription, 'address_id', 'None') }}</div>
                                        </td>
                                        <td>
                                            @if ($items->isNotEmpty())
                                                <div class="tx-title">{{ $items->pluck('price.product.name')->filter()->join(', ') ?: 'Subscription items loaded' }}</div>
                                                <div class="tx-subtitle">{{ $items->count() }} item(s)</div>
                                            @else
                                                <div class="tx-title">No item details</div>
                                                <div class="tx-subtitle">This subscription did not return items</div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="tx-title">{{ $frequency ? 'Every '.$frequency.' '.$interval : $interval }}</div>
                                            <div class="tx-subtitle">Collection {{ data_get($subscription, 'collection_mode', 'automatic') }}</div>
                                        </td>
                                        <td>
                                            <div class="tx-title">{{ $nextBilledAt ? \Illuminate\Support\Carbon::parse($nextBilledAt)->format('d M Y') : 'Not scheduled' }}</div>
                                            <div class="tx-subtitle">{{ $nextBilledAt ? \Illuminate\Support\Carbon::parse($nextBilledAt)->format('H:i') : 'No next bill date' }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </main>
    </body>
</html>

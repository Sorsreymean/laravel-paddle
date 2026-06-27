<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Billing Result | {{ config('app.name') }}</title>
        <style>
            body {
                margin: 0;
                min-height: 100vh;
                display: grid;
                place-items: center;
                padding: 20px;
                font-family: "Segoe UI", sans-serif;
                background: linear-gradient(135deg, #ecfeff, #f0fdf4);
                color: #163020;
            }

            .card {
                width: min(620px, 100%);
                padding: 34px;
                border-radius: 28px;
                background: rgba(255, 255, 255, 0.9);
                border: 1px solid rgba(22, 48, 32, 0.12);
                box-shadow: 0 20px 60px rgba(22, 48, 32, 0.1);
            }

            h1 {
                margin: 0 0 14px;
                font-size: clamp(2rem, 7vw, 3.4rem);
                line-height: 0.95;
            }

            p {
                margin: 0 0 12px;
                line-height: 1.7;
                color: #476253;
            }

            .pill {
                display: inline-block;
                margin-bottom: 16px;
                padding: 8px 14px;
                border-radius: 999px;
                background: rgba(22, 163, 74, 0.12);
                color: #166534;
                font-size: 13px;
                font-weight: 700;
                letter-spacing: 0.08em;
                text-transform: uppercase;
            }

            .panel {
                margin: 22px 0;
                padding: 16px;
                border-radius: 16px;
                background: #f8fffb;
                border: 1px solid rgba(22, 48, 32, 0.1);
            }

            .meta {
                display: grid;
                gap: 10px;
            }

            .row {
                display: flex;
                justify-content: space-between;
                gap: 14px;
                align-items: baseline;
                flex-wrap: wrap;
            }

            .label {
                color: #476253;
                font-size: 14px;
            }

            .value {
                font-weight: 700;
                color: #163020;
            }

            code {
                font-family: Consolas, monospace;
            }

            a {
                color: #0f766e;
                font-weight: 700;
                text-decoration: none;
            }
        </style>
    </head>
    <body>
        <main class="card">
            <div class="pill">Billing Success</div>
            <h1>Payment Completed</h1>
            <p>After Paddle confirms a successful charge, this app now stores a billing record in your local database.</p>

            @if ($subscription)
                <section class="panel">
                    <div class="meta">
                        <div class="row">
                            <span class="label">Customer</span>
                            <span class="value">{{ $user?->name ?? 'Unknown user' }}</span>
                        </div>
                        <div class="row">
                            <span class="label">Subscription ID</span>
                            <span class="value">{{ $subscription->paddle_id }}</span>
                        </div>
                        <div class="row">
                            <span class="label">Status</span>
                            <span class="value">{{ $subscription->status }}</span>
                        </div>
                        <div class="row">
                            <span class="label">Type</span>
                            <span class="value">{{ $subscription->type }}</span>
                        </div>
                        @if ($billingRecord)
                            <div class="row">
                                <span class="label">Latest invoice</span>
                                <span class="value">{{ $billingRecord->invoice_number ?? 'N/A' }}</span>
                            </div>
                        @endif
                        @if ($subscription->items->isNotEmpty())
                            <div class="row">
                                <span class="label">Price IDs</span>
                                <span class="value">{{ $subscription->items->pluck('price_id')->implode(', ') }}</span>
                            </div>
                        @endif
                    </div>
                </section>
            @else
                <section class="panel">
                    <p>No synced subscription was found for this checkout yet.</p>
                </section>
            @endif

            <a href="{{ route('subscriptions.index') }}">See subscription</a>
        </main>
    </body>
</html>

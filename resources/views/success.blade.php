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
            <div class="pill">Return page</div>
            <h1>Checkout returned to Laravel.</h1>
            <p>
                Paddle has redirected back to your app. Final subscription state depends on webhook delivery,
                so confirm the endpoint is reachable from the Paddle dashboard.
            </p>

            <section class="panel">
                <p><strong>Demo customer:</strong> {{ $user?->email ?? 'Not created yet' }}</p>
                <p><strong>Local subscription:</strong> {{ $subscription?->status ?? 'Waiting for webhook sync' }}</p>
                <p><strong>Billing record:</strong> {{ $billingRecord?->paddle_transaction_id ?? 'Waiting for successful payment webhook' }}</p>
            </section>

            <p>After Paddle confirms a successful charge, this app now stores a billing record in your local database.</p>
            <a href="{{ route('home') }}">Back to the setup page</a>
        </main>
    </body>
</html>

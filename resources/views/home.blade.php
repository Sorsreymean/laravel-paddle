<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name') }}</title>
        <style>
            :root {
                --ink: #17202a;
                --muted: #586574;
                --line: rgba(23, 32, 42, 0.12);
                --paper: rgba(255, 255, 255, 0.84);
                --accent: #0f766e;
                --accent-strong: #115e59;
                --sun: #f59e0b;
                --rose: #fb7185;
                --bg-a: #fef3c7;
                --bg-b: #e0f2fe;
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
                    radial-gradient(circle at top left, rgba(245, 158, 11, 0.32), transparent 26%),
                    radial-gradient(circle at top right, rgba(15, 118, 110, 0.18), transparent 24%),
                    linear-gradient(135deg, var(--bg-a), var(--bg-b));
            }

            .shell {
                width: min(1100px, calc(100% - 32px));
                margin: 0 auto;
                padding: 48px 0 72px;
            }

            .hero,
            .panel {
                background: var(--paper);
                backdrop-filter: blur(14px);
                border: 1px solid var(--line);
                border-radius: 28px;
                box-shadow: 0 24px 80px rgba(23, 32, 42, 0.12);
            }

            .hero {
                padding: 40px;
                display: grid;
                gap: 24px;
            }

            .eyebrow {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                padding: 8px 14px;
                border-radius: 999px;
                background: rgba(255, 255, 255, 0.72);
                border: 1px solid var(--line);
                font-size: 13px;
                letter-spacing: 0.08em;
                text-transform: uppercase;
            }

            h1 {
                margin: 0;
                max-width: 12ch;
                font-size: clamp(2.6rem, 8vw, 5rem);
                line-height: 0.95;
            }

            p {
                margin: 0;
                color: var(--muted);
                line-height: 1.7;
            }

            .cta-row {
                display: flex;
                flex-wrap: wrap;
                gap: 14px;
            }

            .button,
            .ghost {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 999px;
                padding: 14px 22px;
                font-weight: 600;
                text-decoration: none;
            }

            .button {
                color: white;
                background: linear-gradient(135deg, var(--accent), var(--accent-strong));
                box-shadow: 0 18px 40px rgba(17, 94, 89, 0.22);
            }

            .ghost {
                color: var(--ink);
                border: 1px solid var(--line);
                background: rgba(255, 255, 255, 0.7);
            }

            .grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 20px;
                margin-top: 20px;
            }

            .panel {
                padding: 24px;
            }

            .panel h2 {
                margin: 0 0 12px;
                font-size: 1.1rem;
            }

            ul {
                margin: 16px 0 0;
                padding-left: 18px;
                color: var(--muted);
                line-height: 1.8;
            }

            code {
                font-family: Consolas, monospace;
                font-size: 0.95em;
                color: var(--accent-strong);
            }

            .status {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                margin-top: 18px;
                padding: 10px 14px;
                border-radius: 14px;
                background: rgba(255, 255, 255, 0.72);
                border: 1px solid var(--line);
                font-size: 14px;
            }

            .dot {
                width: 10px;
                height: 10px;
                border-radius: 999px;
            }

            .dot-ready {
                background: #16a34a;
            }

            .dot-warn {
                background: #f59e0b;
            }

            .meta {
                display: grid;
                gap: 10px;
                margin-top: 18px;
                font-size: 14px;
            }

            .meta span {
                color: var(--ink);
                font-weight: 600;
            }

            @media (max-width: 800px) {
                .shell {
                    width: min(100% - 20px, 1100px);
                    padding: 20px 0 40px;
                }

                .hero,
                .panel {
                    border-radius: 22px;
                }

                .hero {
                    padding: 24px;
                }

                .grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </head>
    <body>
        <main class="shell">
            <section class="hero">
                <div class="eyebrow">Laravel 13 x Paddle Billing</div>
                <h1>Ship subscriptions with Paddle faster.</h1>
                <p>
                    This starter project already includes Laravel Cashier Paddle, the billing tables,
                    webhook CSRF exclusions, and a demo checkout flow you can use to validate your Paddle setup.
                </p>

                <div class="cta-row">
                    <a class="button" href="{{ route('billing.checkout') }}">Open demo checkout</a>
                    <a class="ghost" href="{{ $webhookUrl }}">Webhook endpoint</a>
                </div>

                <div class="status">
                    <span class="{{ $paddleReady ? 'dot dot-ready' : 'dot dot-warn' }}"></span>
                    {{ $paddleReady ? 'Paddle values detected. You can test checkout now.' : 'Add your Paddle keys and price ID in .env before testing checkout.' }}
                </div>

                <div class="meta">
                    <div><span>Environment:</span> {{ $sandbox ? 'Sandbox' : 'Live' }}</div>
                    <div><span>Webhook:</span> <code>{{ $webhookUrl }}</code></div>
                    <div><span>Price ID:</span> <code>{{ $priceId ?: 'missing PADDLE_DEFAULT_PRICE_ID' }}</code></div>
                </div>
            </section>

            <section class="grid">
                <article class="panel">
                    <h2>What is already wired</h2>
                    <ul>
                        <li><code>laravel/cashier-paddle</code> installed and published</li>
                        <li>Billing tables migrated for customers, subscriptions, items, and transactions</li>
                        <li><code>Billable</code> added to the default <code>User</code> model</li>
                        <li><code>paddle/*</code> excluded from CSRF verification for webhook delivery</li>
                    </ul>
                </article>

                <article class="panel">
                    <h2>What you still need</h2>
                    <ul>
                        <li>Paste <code>PADDLE_CLIENT_SIDE_TOKEN</code>, <code>PADDLE_API_KEY</code>, and <code>PADDLE_WEBHOOK_SECRET</code> into <code>.env</code></li>
                        <li>Set <code>PADDLE_DEFAULT_PRICE_ID</code> to a real Paddle price</li>
                        <li>Point Paddle webhooks to <code>{{ $webhookUrl }}</code></li>
                        <li>Replace the demo billing user with your real authenticated user flow</li>
                    </ul>
                </article>
            </section>
        </main>
    </body>
</html>

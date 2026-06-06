<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Checkout | {{ config('app.name') }}</title>
        @paddleJS
        <style>
            :root {
                --ink: #102032;
                --muted: #607081;
                --line: rgba(16, 32, 50, 0.12);
                --card: rgba(255, 255, 255, 0.88);
                --accent: #0f766e;
                --accent-strong: #155e75;
                --bg: #eff6ff;
            }

            * { box-sizing: border-box; }

            body {
                margin: 0;
                min-height: 100vh;
                display: grid;
                place-items: center;
                padding: 20px;
                font-family: "Segoe UI", sans-serif;
                color: var(--ink);
                background:
                    radial-gradient(circle at top left, rgba(56, 189, 248, 0.28), transparent 24%),
                    radial-gradient(circle at bottom right, rgba(16, 185, 129, 0.2), transparent 28%),
                    linear-gradient(160deg, #f8fafc, var(--bg));
            }

            .card {
                width: min(620px, 100%);
                padding: 36px;
                border-radius: 30px;
                border: 1px solid var(--line);
                background: var(--card);
                backdrop-filter: blur(14px);
                box-shadow: 0 24px 80px rgba(16, 32, 50, 0.12);
            }

            .tag {
                display: inline-block;
                margin-bottom: 18px;
                padding: 8px 14px;
                border-radius: 999px;
                background: rgba(15, 118, 110, 0.1);
                color: var(--accent);
                font-size: 13px;
                font-weight: 700;
                letter-spacing: 0.08em;
                text-transform: uppercase;
            }

            h1 {
                margin: 0 0 14px;
                font-size: clamp(2rem, 7vw, 3.6rem);
                line-height: 0.95;
            }

            p {
                margin: 0;
                line-height: 1.7;
                color: var(--muted);
            }

            .stack {
                display: grid;
                gap: 14px;
                margin: 24px 0 28px;
            }

            .row {
                padding: 14px 16px;
                border-radius: 16px;
                background: rgba(255, 255, 255, 0.8);
                border: 1px solid var(--line);
            }

            .row strong,
            code {
                color: var(--ink);
            }

            code {
                font-family: Consolas, monospace;
            }

            .actions {
                display: flex;
                flex-wrap: wrap;
                gap: 14px;
                align-items: center;
            }

            .back {
                color: var(--accent-strong);
                text-decoration: none;
                font-weight: 600;
            }

            .paddle-button {
                border: 0;
                border-radius: 999px;
                padding: 15px 24px;
                color: white;
                font-size: 15px;
                font-weight: 700;
                background: linear-gradient(135deg, var(--accent), var(--accent-strong));
                box-shadow: 0 18px 36px rgba(15, 118, 110, 0.22);
                cursor: pointer;
            }

            @media (max-width: 640px) {
                .card {
                    padding: 24px;
                    border-radius: 22px;
                }
            }
        </style>
    </head>
    <body>
        <main class="card">
            <div class="tag">Demo checkout</div>
            <h1>Launch the Paddle overlay.</h1>
            <p>
                This route uses a seeded demo user so you can confirm Cashier and Paddle are connected before
                swapping the flow into your real authenticated billing pages.
            </p>

            <section class="stack">
                <div class="row"><strong>Demo customer:</strong> {{ $user->email }}</div>
                <div class="row"><strong>Subscription type:</strong> <code>default</code></div>
                <div class="row"><strong>Paddle price:</strong> <code>{{ $priceId }}</code></div>
            </section>

            <div class="actions">
                <x-paddle-button :checkout="$checkout" class="paddle-button">
                    Start subscription
                </x-paddle-button>

                <a class="back" href="{{ route('home') }}">Back to setup guide</a>
            </div>
        </main>
    </body>
</html>

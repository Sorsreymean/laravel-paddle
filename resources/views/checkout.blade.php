<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Checkout Preview | {{ config('app.name') }}</title>
        @paddleJS
        <style>
            :root {
                --ink: #102032;
                --muted: #607081;
                --line: rgba(16, 32, 50, 0.12);
                --card: rgba(255, 255, 255, 0.9);
                --accent: #0f766e;
                --accent-strong: #155e75;
                --bg: #eff6ff;
                --soft: #f8fbff;
            }

            * { box-sizing: border-box; }

            body {
                margin: 0;
                min-height: 100vh;
                padding: 20px;
                display: grid;
                place-items: center;
                font-family: "Segoe UI", sans-serif;
                color: var(--ink);
                background:
                    radial-gradient(circle at top left, rgba(56, 189, 248, 0.28), transparent 24%),
                    radial-gradient(circle at bottom right, rgba(16, 185, 129, 0.2), transparent 28%),
                    linear-gradient(160deg, #f8fafc, var(--bg));
            }

            .card {
                width: min(760px, 100%);
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
                font-size: clamp(2rem, 7vw, 3.2rem);
                line-height: 0.95;
            }

            p {
                margin: 0;
                line-height: 1.7;
                color: var(--muted);
            }

            .product-card {
                display: grid;
                grid-template-columns: minmax(0, 1.35fr) minmax(220px, 0.9fr);
                gap: 20px;
                margin-top: 28px;
            }

            .panel {
                padding: 22px;
                border-radius: 22px;
                border: 1px solid var(--line);
                background: var(--soft);
            }

            .eyebrow {
                margin-bottom: 8px;
                color: var(--accent);
                font-size: 12px;
                font-weight: 700;
                letter-spacing: 0.08em;
                text-transform: uppercase;
            }

            .product-title {
                margin: 0 0 12px;
                font-size: clamp(1.5rem, 4vw, 2.1rem);
                line-height: 1.05;
            }

            .product-meta {
                display: grid;
                gap: 12px;
            }

            .meta-row {
                padding: 14px 16px;
                border-radius: 16px;
                background: rgba(255, 255, 255, 0.82);
                border: 1px solid var(--line);
            }

            .meta-label {
                display: block;
                margin-bottom: 4px;
                color: var(--muted);
                font-size: 12px;
                text-transform: uppercase;
                letter-spacing: 0.08em;
            }

            .meta-value {
                color: var(--ink);
                font-size: 1rem;
                font-weight: 700;
            }

            .description {
                margin-top: 14px;
            }

            .preview-image {
                width: 100%;
                aspect-ratio: 4 / 3;
                object-fit: cover;
                border-radius: 18px;
                border: 1px solid var(--line);
                background: linear-gradient(135deg, #dbeafe, #ecfdf5);
            }

            .placeholder {
                display: grid;
                place-items: center;
                width: 100%;
                aspect-ratio: 4 / 3;
                padding: 18px;
                border-radius: 18px;
                border: 1px dashed var(--line);
                color: var(--muted);
                text-align: center;
                background: linear-gradient(135deg, #f8fafc, #f0fdf4);
            }

            .actions {
                display: flex;
                flex-wrap: wrap;
                gap: 14px;
                align-items: center;
                margin-top: 28px;
            }

            .pay-button,
            .back {
                border-radius: 999px;
                padding: 15px 24px;
                font-size: 15px;
                font-weight: 700;
                text-decoration: none;
            }

            .pay-button {
                border: 0;
                color: white;
                background: linear-gradient(135deg, var(--accent), var(--accent-strong));
                box-shadow: 0 18px 36px rgba(15, 118, 110, 0.22);
                cursor: pointer;
            }

            .back {
                color: var(--accent-strong);
                border: 1px solid var(--line);
                background: white;
            }

            .helper {
                margin-top: 12px;
                font-size: 0.95rem;
            }

            @media (max-width: 720px) {
                .card {
                    padding: 24px;
                    border-radius: 22px;
                }

                .product-card {
                    grid-template-columns: 1fr;
                }
            }
            select {
                width: 100%;
                padding: 5px 10px;
                border-radius: 10px;
                border: 1px solid var(--line);
                background: rgba(255, 255, 255, 0.92);
                color: var(--ink);
                font: inherit;
            }
        </style>
    </head>
    <body>
        @php
        // dd($user);
            $checkoutOptions = array_replace_recursive($checkout->options(), [
                'settings' => [
                    'displayMode' => 'overlay',
                    'theme' => 'light',
                    'variant' => 'one-page',
                    'locale' => 'en',
                    'allowLogout' => false,
                    'successUrl' => route('billing.success'),
                ],
            ]);

            // $amount = (int) data_get($selectedPrice, 'unit_price.amount', 0);
            // $currency = data_get($selectedPrice, 'unit_price.currency_code', 'USD');
            // $interval = data_get($selectedPrice, 'billing_cycle.interval', 'one-time');
            // $frequency = data_get($selectedPrice, 'billing_cycle.frequency');
            // $billingLabel = $frequency ? 'Every '.$frequency.' '.$interval : $interval;
            // $trial_period = data_get($selectedPrice, 'trial_period.frequency') ?? null;
            // $trial_interval = data_get($selectedPrice, 'trial_period.interval') ?? null;
            // dd($selectedPrice);
        @endphp

        <main >
            <div class="tag">Checkout Form</div>
            <h1>Connecting to Paddle checkout ...</h1>
            {{-- <p>
                Confirm the selected Paddle item first, then continue to the payment form.
            </p> --}}

            <section class="product-card">
                Connecting to Paddle ...
            </section>

            {{-- <div class="actions">
                <button class="pay-button" type="button" id="manual-checkout-button">Continue to payment</button>
                <a class="back" href="{{ route('home')}}">Back</a>
            </div> --}}
        </main>

        <script type="application/json" id="checkout-options">@json($checkoutOptions)</script>
        <script>
            window.addEventListener('load', function () {
                var checkoutOptionsNode = document.getElementById('checkout-options');
                var hasOpened = false;

                function openCheckout() {
                    if (hasOpened) {
                        return;
                    }

                    hasOpened = true;

                    var checkoutOptions = JSON.parse(checkoutOptionsNode.textContent);

                    Paddle.Checkout.open(checkoutOptions);
                }

                if (!checkoutOptionsNode) {
                    return;
                }

                openCheckout();
            });
        </script>
    </body>
</html>

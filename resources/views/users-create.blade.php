<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Create User | {{ config('app.name') }}</title>
        <style>
            :root {
                --ink: #1b2430;
                --muted: #667085;
                --line: rgba(27, 36, 48, 0.12);
                --surface: rgba(255, 255, 255, 0.84);
                --accent: #0f766e;
                --accent-strong: #115e59;
                --warn: #b45309;
                --danger: #b42318;
                --success: #166534;
                --success-soft: rgba(22, 101, 52, 0.08);
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

            .shell { width: min(1080px, calc(100% - 32px)); margin: 0 auto; padding: 32px 0 56px; }
            .hero, .form-card, .summary-card {
                background: var(--surface);
                backdrop-filter: blur(14px);
                border: 1px solid var(--line);
                border-radius: 28px;
                box-shadow: var(--shadow);
            }
            .hero, .summary-card, .form-card { padding: 28px; }
            .hero { display: grid; gap: 20px; }
            .eyebrow, .nav-link, .status, .pill {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                width: fit-content;
                border-radius: 999px;
                border: 1px solid var(--line);
                background: rgba(255, 255, 255, 0.72);
            }
            .eyebrow, .nav-link, .pill {
                padding: 8px 14px;
                font-size: 12px;
                font-weight: 700;
                letter-spacing: 0.1em;
                text-transform: uppercase;
            }
            .nav-row { display: flex; flex-wrap: wrap; gap: 12px; }
            .nav-link { color: var(--ink); text-decoration: none; }
            h1, h2, p { margin: 0; }
            h1 { font-size: clamp(2rem, 5vw, 3rem); line-height: 0.94; }
            p { color: var(--muted); line-height: 1.7; }
            .hero-grid, .content { display: grid; gap: 22px; }
            .hero-grid { grid-template-columns: minmax(0, 1.3fr) minmax(280px, 0.8fr); }
            .content { margin-top: 22px; grid-template-columns: minmax(0, 1.05fr) minmax(300px, 0.95fr); align-items: start; }
            .summary-card { display: grid; gap: 16px; background: linear-gradient(180deg, rgba(15, 118, 110, 0.11), rgba(255, 255, 255, 0.74)); }
            .meta-label { display: block; margin-bottom: 6px; font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; color: var(--muted); }
            code { font-family: Consolas, monospace; color: var(--accent-strong); word-break: break-all; }
            .status { padding: 10px 14px; font-size: 14px; font-weight: 600; }
            .dot { width: 10px; height: 10px; border-radius: 999px; background: #16a34a; }
            .dot-warn { background: var(--warn); }
            .card-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 22px; }
            .card-head h2 { font-size: 1.35rem; }
            .form-grid { display: grid; gap: 18px; }
            label { display: grid; gap: 8px; font-size: 14px; font-weight: 600; }
            input {
                width: 100%;
                padding: 14px 16px;
                border-radius: 16px;
                border: 1px solid var(--line);
                background: rgba(255, 255, 255, 0.92);
                color: var(--ink);
                font: inherit;
            }
            input:focus { outline: 2px solid rgba(15, 118, 110, 0.16); border-color: rgba(15, 118, 110, 0.5); }
            .hint, .error-text, .notice, .detail-list { font-size: 14px; }
            .hint, .detail-list { color: var(--muted); }
            .error-text { color: var(--danger); }
            .notice { padding: 14px 16px; border-radius: 18px; border: 1px solid var(--line); }
            .notice.success { color: var(--success); background: var(--success-soft); }
            .notice.warn { color: var(--warn); background: rgba(180, 83, 9, 0.08); }
            .action-row { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 4px; }
            .button, .button-secondary {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
                padding: 13px 18px;
                border-radius: 999px;
                border: 1px solid transparent;
                font: inherit;
                font-weight: 700;
                text-decoration: none;
                cursor: pointer;
            }
            .button { background: linear-gradient(135deg, var(--accent), #155e75); color: #fff; box-shadow: 0 16px 36px rgba(15, 118, 110, 0.2); }
            .button-secondary { color: var(--ink); border-color: var(--line); background: rgba(255, 255, 255, 0.86); }
            .detail-list { display: grid; gap: 14px; }
            .detail-item { padding: 16px 18px; border-radius: 18px; border: 1px solid var(--line); background: rgba(255, 255, 255, 0.72); }
            strong { color: var(--ink); }

            @media (max-width: 900px) {
                .shell { width: min(100% - 20px, 1080px); padding: 20px 0 40px; }
                .hero, .form-card, .summary-card { border-radius: 22px; padding: 22px; }
                .hero-grid, .content { grid-template-columns: 1fr; }
            }
        </style>
    </head>
    <body>
        <main class="shell">
            <section class="hero">
                <div class="eyebrow">Paddle Customer Sync</div>
                <div class="nav-row">
                    <a class="nav-link" href="{{ route('home') }}">Products</a>
                    <a class="nav-link" href="{{ route('transactions.index') }}">Transactions</a>
                    <a class="nav-link" href="{{ route('subscriptions.index') }}">Subscriptions</a>
                    <a class="nav-link" href="{{ route('subscriptions.create') }}">Create Subscription</a>
                    <a class="nav-link" href="{{ route('users.create') }}">Create User</a>
                </div>

                <div class="hero-grid">
                    <div>
                        <h1>Create a user and sync to Paddle sandbox</h1>
                        <p>Save a local user, fire the creation event, and let the listener create the matching Paddle customer automatically.</p>
                    </div>

                    <aside class="summary-card">
                        <div>
                            <span class="meta-label">Mode</span>
                            <strong>{{ $sandbox ? 'Sandbox' : 'Live' }}</strong>
                        </div>
                        <div>
                            <span class="meta-label">API key</span>
                            <strong>{{ filled(config('cashier.api_key')) ? 'Configured' : 'Missing' }}</strong>
                        </div>
                        <div>
                            <span class="meta-label">Webhook</span>
                            <code>{{ $webhookUrl }}</code>
                        </div>
                    </aside>
                </div>

                <div class="status">
                    <span class="dot {{ filled(config('cashier.api_key')) ? '' : 'dot-warn' }}"></span>
                    {{ filled(config('cashier.api_key')) ? 'Paddle customer sync is ready for new users.' : 'Add your Paddle API key before creating sandbox customers.' }}
                </div>
            </section>

            <section class="content">
                <div class="form-card">
                    <div class="card-head">
                        <div>
                            <h2>Create user</h2>
                            <p>Submitting this form creates the local user first, then the Paddle customer event runs automatically.</p>
                        </div>
                        <span class="pill">{{ $sandbox ? 'Sandbox' : 'Live' }}</span>
                    </div>

                    @if (session('status'))
                        <div class="notice success">{{ session('status') }}</div>
                    @endif

                    @if (session('warning'))
                        <div class="notice warn" style="margin-top: 12px;">{{ session('warning') }}</div>
                    @endif

                    <form class="form-grid" method="POST" action="{{ route('users.store') }}" style="margin-top: 18px;">
                        @csrf

                        <label>
                            Full name
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Jane Doe">
                            @error('name')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </label>

                        <label>
                            Email address
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="jane@example.com">
                            @error('email')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </label>

                        <div class="hint">A random password is generated automatically for this demo user.</div>

                        <div class="action-row">
                            <button class="button" type="submit">Create user</button>
                            <a class="button-secondary" href="{{ route('home') }}">Back to products</a>
                        </div>
                    </form>
                </div>

                <aside class="summary-card">
                    <div>
                        <h2 style="font-size: 1.25rem;">What happens next</h2>
                        <p>After save, the app dispatches the user-created event and the listener asks Paddle for a matching customer or creates one if needed.</p>
                    </div>

                    <div class="detail-list">
                        <div class="detail-item">
                            <span class="meta-label">Local record</span>
                            <strong>`users`</strong> gets the new account.
                        </div>
                        <div class="detail-item">
                            <span class="meta-label">Paddle sync</span>
                            <strong>`customers`</strong> stores the linked Paddle customer ID.
                        </div>
                        <div class="detail-item">
                            <span class="meta-label">Requirement</span>
                            <strong>`cashier.api_key`</strong> must be set for remote customer creation.
                        </div>
                    </div>
                </aside>
            </section>
        </main>
    </body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <title>{{ $title ?? 'Dashboard' }} | {{ config('app.name', 'AI Plag') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: linear-gradient(135deg, #f4f7ff, #f4ecff);
            --panel: #ffffff;
            --text: #1c2a38;
            --muted: #5d6b80;
            --primary: #5c6af0;
            --accent: #1cb58f;
            --radius: 14px;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Manrope', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text);
        }
        a { color: var(--primary); text-decoration: none; }
        .wrapper {
            min-height: 100vh;
            padding: 32px 16px 48px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }
        .card {
            background: var(--panel);
            border-radius: var(--radius);
            padding: 28px;
            width: min(1400px, 100%);
            box-shadow: 0 20px 60px rgba(16, 24, 40, 0.12);
            border: 1px solid #e6e9f0;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            color: var(--text);
        }
        .brand img {
            height: 42px;
            width: auto;
            display: block;
        }
        .impersonation {
            display: flex;
            gap: 10px;
            align-items: center;
            padding: 8px 12px;
            background: #fff4f3;
            border: 1px solid #f2c8c3;
            border-radius: 10px;
            color: #b91c1c;
            font-weight: 700;
        }
        .brand-badge {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, #1cb58f, #5c6af0);
            color: #fff;
            font-weight: 800;
        }
        .status {
            background: #f2f7ff;
            border: 1px solid #d9e4ff;
            color: #3753e5;
            padding: 10px 14px;
            border-radius: 12px;
            font-weight: 600;
        }
        form {
            display: grid;
            gap: 18px;
        }
        label {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text);
            display: block;
            margin-bottom: 6px;
        }
        input, textarea {
            width: 100%;
            padding: 12px 14px;
            border-radius: 10px;
            border: 1px solid #dfe3eb;
            background: #f9fafc;
            font-family: inherit;
            font-size: 1rem;
            color: var(--text);
        }
        input:focus, textarea:focus {
            outline: 2px solid rgba(92, 106, 240, 0.35);
            border-color: #c9d3ff;
        }
        .row {
            display: grid;
            gap: 16px;
        }
        @media (min-width: 720px) {
            .row.two { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        .actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            align-items: center;
        }
        .btn {
            border: none;
            border-radius: 12px;
            padding: 12px 18px;
            font-weight: 700;
            cursor: pointer;
            transition: transform 150ms ease, box-shadow 150ms ease;
        }
        .btn-primary {
            background: linear-gradient(120deg, #5c6af0, #1cb58f);
            color: #fff;
            box-shadow: 0 12px 28px rgba(92, 106, 240, 0.3);
        }
        .btn-ghost {
            background: #d6872dff;
            color: var(--text);
        }
        .btn:hover { transform: translateY(-1px); }
        .error {
            color: #c81e1e;
            font-size: 0.95rem;
        }
        .preview {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: #f8fafc;
            border: 1px dashed #dce2ea;
            border-radius: 12px;
        }
        .preview img {
            max-height: 64px;
            width: auto;
            display: block;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <header>
                @php
                    $panelSettings = \App\Models\SiteSetting::first();
                    $panelLogo = $panelSettings?->logoUrl();
                @endphp
                <div class="brand" style="gap:8px;">
                    @if ($panelLogo)
                        <img src="{{ $panelLogo }}" alt="{{ $panelSettings->site_name ?? config('app.name', 'AI Plag') }} logo">
                    @else
                        <div class="brand-badge">AI</div>
                    @endif
                </div>
                @if (session('impersonator_id'))
                    <form class="impersonation" action="{{ route('impersonate.stop') }}" method="POST">
                        @csrf
                        <button class="btn btn-ghost" type="submit" style="padding:8px 10px;">Return to admin</button>
                    </form>
                @endif
            </header>

            @yield('content')
        </div>
    </div>
</body>
</html>

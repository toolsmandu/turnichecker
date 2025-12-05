<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    @php
        $metaTitle = $settings->meta_title ?? ($settings->site_name ?? config('app.name', 'AI Plag'));
        $metaDescription = $settings->meta_description ?? ($settings->hero_subtitle ?? '');
        $shareImage = $settings->share_image ?? asset('logo.webp');
        $pageUrl = url()->current();
    @endphp
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $pageUrl }}">
    <meta property="og:title" content="TurniChecker Plagiarism + AI Service">
    <meta property="og:description" content="Provide fast, accurate, and affordable plagiarism+ai detection">
    <meta property="og:image" content="{{ $shareImage }}">
    <meta property="og:site_name" content="{{ $settings->site_name ?? config('app.name', 'TurniChecker') }}">
    <title>{{ $settings->site_name ?? config('app.name', 'AI Plag') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #5c6af0;
            --accent: #1cb58f;
            --dark: #1b2a3b;
            --muted: #5a6475;
            --surface: #ffffff;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Manrope', system-ui, -apple-system, sans-serif;
            background: radial-gradient(circle at 15% 20%, rgba(92, 106, 240, 0.08), transparent 25%),
                        radial-gradient(circle at 80% 10%, rgba(28, 181, 143, 0.1), transparent 25%),
                        radial-gradient(circle at 70% 90%, rgba(255, 114, 182, 0.12), transparent 28%),
                        #f5f7ff;
            color: var(--dark);
        }
        a { text-decoration: none; color: inherit; }
        header {
            width: 100%;
            padding: 20px 24px;
            position: sticky;
            top: 0;
            z-index: 10;
            background: rgba(255,255,255,0.86);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid #e7edf6;
        }
        .nav {
            max-width: 1160px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 1.2rem;
        }
        .brand img {
            height: clamp(36px, 6vw, 52px);
            width: auto;
            max-width: 100%;
        }
        .brand-fallback {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, #1cb58f, #5c6af0);
            display: grid;
            place-items: center;
            color: #fff;
            font-weight: 800;
            letter-spacing: 0.5px;
        }
        .links {
            display: flex;
            align-items: center;
            gap: 18px;
            color: var(--muted);
            font-weight: 600;
        }
        .cta {
            padding: 10px 16px;
            background: #1cb58f;
            color: #fff;
            border-radius: 12px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            box-shadow: 0 12px 26px rgba(28, 181, 143, 0.35);
            transition: transform 150ms ease, box-shadow 150ms ease;
        }
        .cta:hover { transform: translateY(-1px); box-shadow: 0 16px 32px rgba(28, 181, 143, 0.4); }
        main { max-width: 1160px; margin: 32px auto 72px; padding: 0 18px; text-align: center; }
        h1 {
            margin: 16px 0 12px;
            font-size: clamp(2.2rem, 3vw + 1rem, 3.8rem);
            font-weight: 800;
            background: linear-gradient(120deg, #3b67f6, #7b2dfa);
            -webkit-background-clip: text;
            color: transparent;
        }
        .chips {
            display: flex;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
            margin: 10px auto 22px;
        }
        .chip {
            background: #f7f8fc;
            border-radius: 999px;
            padding: 12px 18px;
            font-weight: 800;
            color: #3a4457;
            box-shadow: 0 10px 26px rgba(0,0,0,0.08);
            border: 1px solid #e5e9f5;
        }
        .subtext {
            max-width: 820px;
            margin: 0 auto 26px;
            color: var(--muted);
            font-size: 1.05rem;
            line-height: 1.7;
        }
        .big-cta {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: linear-gradient(120deg, #3369ff, #7b2dfa);
            color: #fff;
            padding: 16px 26px;
            border-radius: 999px;
            font-weight: 800;
            box-shadow: 0 18px 38px rgba(51, 105, 255, 0.35);
            transition: transform 150ms ease, box-shadow 150ms ease;
        }
        .big-cta:hover { transform: translateY(-2px); box-shadow: 0 22px 48px rgba(51, 105, 255, 0.42); }
        .pill-icon {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: rgba(255,255,255,0.14);
            display: grid; place-items: center;
        }
        .faq-grid {
            margin: 48px auto 0;
            max-width: 900px;
            display: grid;
            gap: 14px;
        }
        .faq-item {
            background: #ffffff;
            border-radius: 14px;
            padding: 18px 20px;
            text-align: left;
            box-shadow: 0 12px 34px rgba(0, 0, 0, 0.06);
            border: 1px solid #e9edf5;
        }
        .faq-q {
            font-weight: 800;
            color: #273047;
            margin-bottom: 6px;
        }
        .faq-a {
            color: #5a6475;
            line-height: 1.6;
        }
        .howto {
            margin: 52px auto 0;
            max-width: 1080px;
            background: #ffffff;
            border-radius: 16px;
            padding: 24px 24px 12px;
            box-shadow: 0 18px 50px rgba(41, 72, 152, 0.12);
            border: 1px solid #e9edf5;
        }
        .howto h2 {
            text-align: center;
            margin: 0 0 18px;
            font-size: 1.9rem;
            color: #0f2f3d;
            font-weight: 800;
        }
        .video-wrap {
            margin-top: 16px;
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            border-radius: 14px;
            box-shadow: 0 18px 50px rgba(41, 72, 152, 0.12);
            border: 1px solid #e9edf5;
        }
        .video-wrap iframe, .video-wrap embed {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        @media (max-width: 720px) {
            header { padding: 16px 14px; }
            .nav { flex-wrap: nowrap; gap: 10px; }
            .links { width: auto; justify-content: flex-end; }
            .cta { padding: 9px 14px; font-size: 0.95rem; white-space: nowrap; }
        }
    </style>
</head>
<body>
    <header>
        <div class="nav">
            <a href="{{ url('/') }}" class="brand" style="gap:0;">
                <img src="{{ asset('logo.webp') }}" alt="{{ $settings->site_name ?? 'Logo' }} logo">
            </a>
            <div class="links">
       
                @auth
                    <a class="cta" href="{{ url('/dashboard') }}" style="background:#273047;box-shadow:none;">My Dashboard</a>
                @else
                    <a class="cta" href="{{ url('/login') }}">Login</a>
                @endauth
            </div>
        </div>
    </header>

    <main>
        <h1>{{ $settings->hero_title }}</h1>

        <div class="chips">
            @forelse ($featureTags as $tag)
                <div class="chip">{{ strtoupper($tag) }}</div>
            @empty
                <div class="chip">CHEAPEST</div>
                <div class="chip">FASTEST</div>
                <div class="chip">AFFORDABLE</div>
                <div class="chip">AI ADVANCED</div>
            @endforelse
        </div>

        <p class="subtext">{{ $settings->hero_subtitle }}</p>

        <a class="big-cta" href="{{ $settings->button_link }}">
            <span class="pill-icon">🚀</span>
            <span>{{ $settings->button_text }}</span>
            <span aria-hidden="true">→</span>
        </a>

        @if (($settings->howto_title ?? null) || ($settings->howto_embed ?? null))
            <section class="howto">
                @if ($settings->howto_title)
                    <h2>{{ $settings->howto_title }}</h2>
                @endif
                @if ($settings->howto_video_text)
                    <div style="color:#0f8bff;font-weight:800;margin-bottom:8px;">{{ $settings->howto_video_text }}</div>
                @endif
                @if ($settings->howto_embed)
                    <div class="video-wrap">
                        {!! $settings->howto_embed !!}
                    </div>
                @endif
            </section>
        @endif

        @if (!empty($settings->faqs))
            <h2 style="margin:40px 0 8px;font-size:1.8rem;font-weight:800;color:#273047;">Frequently Asked Questions</h2>
            <div class="faq-grid">
                @foreach ($settings->faqs as $faq)
                    <div class="faq-item">
                        <div class="faq-q">{{ $faq['question'] ?? '' }}</div>
                        <div class="faq-a">{{ $faq['answer'] ?? '' }}</div>
                    </div>
                @endforeach
            </div>
        @endif
    </main>
</body>
</html>

@extends('layouts.panel')

@section('content')
    @php
        $tagString = old('feature_tags', isset($settings->feature_tags) ? implode(', ', $settings->feature_tags) : '');
        $faqString = old('faqs', isset($settings->faqs) ? collect($settings->faqs)->map(fn($f)=>($f['question'] ?? '').' | '.($f['answer'] ?? ''))->implode(PHP_EOL) : '');
        $howtoSteps = old('howto_steps', isset($settings->howto_steps) ? implode(PHP_EOL, $settings->howto_steps) : '');
    @endphp
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
        <div>
            <div style="font-weight:800;font-size:1.4rem;">Homepage Settings</div>
            <p style="color:#5d6b80;margin-top:6px;">Update the hero content, feature chips, and CTA without touching code.</p>
        </div>
        <form action="{{ route('logout') }}" method="POST" style="display:flex;gap:8px;align-items:center;">
            @csrf
            <a class="btn btn-ghost" href="{{ route('dashboard') }}">Back to Dashboard</a>
            <button class="btn btn-ghost" type="submit">Logout</button>
        </form>
    </div>

    @if ($errors->any())
        <div class="status" style="background:#fff4f3;border-color:#f2c8c3;color:#b91c1c;margin-bottom:12px;">
            <strong>Fix the highlighted fields:</strong>
            <ul style="margin:8px 0 0 16px;padding-left:8px;">
                @foreach ($errors->all() as $error)
                    <li style="color:#b91c1c;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row two">
            <div>
                <label for="site_name">Site Name</label>
                <input id="site_name" name="site_name" value="{{ old('site_name', $settings->site_name) }}" required>
            </div>
            <div>
                <label for="hero_title">Hero Title</label>
                <input id="hero_title" name="hero_title" value="{{ old('hero_title', $settings->hero_title) }}" required>
            </div>
        </div>

        <div class="row two">
            <div>
                <label for="document_count">Document Count</label>
                <input id="document_count" name="document_count" value="{{ old('document_count', $settings->document_count) }}" required>
            </div>
            <div>
                <label for="document_label">Document Label</label>
                <input id="document_label" name="document_label" value="{{ old('document_label', $settings->document_label) }}" required>
            </div>
        </div>

        <div class="row two">
            <div>
                <label for="live_label">Live Label</label>
                <input id="live_label" name="live_label" value="{{ old('live_label', $settings->live_label) }}" required>
            </div>
            <div>
                <label for="button_text">Button Text</label>
                <input id="button_text" name="button_text" value="{{ old('button_text', $settings->button_text) }}" required>
            </div>
        </div>

        <div class="row two">
            <div>
                <label for="button_link">Button Link</label>
                <input id="button_link" name="button_link" value="{{ old('button_link', $settings->button_link) }}" required>
            </div>
            <div>
                <label for="feature_tags">Feature Tags (comma separated)</label>
                <input id="feature_tags" name="feature_tags" value="{{ $tagString }}" placeholder="Cheapest, Fastest, Affordable, AI Advanced">
            </div>
        </div>

        <div>
            <label for="hero_subtitle">Hero Subtitle</label>
            <textarea id="hero_subtitle" name="hero_subtitle" rows="4" required>{{ old('hero_subtitle', $settings->hero_subtitle) }}</textarea>
        </div>

        <div>
            <label for="faqs">FAQs (one per line as "Question | Answer")</label>
            <textarea id="faqs" name="faqs" rows="4" placeholder="How fast are results? | Reports generate in under 60 seconds.">{{ $faqString }}</textarea>
            <p style="color:#5d6b80;font-size:0.95rem;margin-top:6px;">Tip: Use the vertical bar (|) to separate question and answer.</p>
        </div>

        <div>
            <label for="logo">Logo</label>
            <input id="logo" type="file" name="logo" accept="image/*">
            @if ($settings->logo_path)
                <div class="preview">
                    <strong>Current:</strong>
                    <img src="{{ $settings->logoUrl() }}" alt="Current logo">
                </div>
            @endif
        </div>

        <div class="actions">
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>

        <hr style="border:none;border-top:1px solid #e5e7eb;margin:12px 0;">

        <div style="font-weight:800;font-size:1.2rem;">How-to Section</div>
        <p style="color:#5d6b80;margin-top:4px;">Embed a YouTube video for the walkthrough.</p>

        <div class="row two">
            <div>
                <label for="howto_title">How-to Title</label>
                <input id="howto_title" name="howto_title" value="{{ old('howto_title', $settings->howto_title) }}" placeholder="How to Use TurniChecker">
            </div>
            <div>
                <label for="howto_video_text">Video Text (optional)</label>
                <input id="howto_video_text" name="howto_video_text" value="{{ old('howto_video_text', $settings->howto_video_text) }}" placeholder="Watch the walkthrough">
            </div>
        </div>

        <div>
            <label for="howto_embed">YouTube Embed Code</label>
            <textarea id="howto_embed" name="howto_embed" rows="4" placeholder='<iframe width="560" height="315" src="https://www.youtube.com/embed/..." title="YouTube video player" frameborder="0" allowfullscreen></iframe>'>{{ old('howto_embed', $settings->howto_embed) }}</textarea>
            <p style="color:#5d6b80;font-size:0.95rem;margin-top:6px;">Paste the full iframe embed code from YouTube.</p>
        </div>
    </form>
@endsection

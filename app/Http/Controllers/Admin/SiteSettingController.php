<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    public function edit(): View
    {
        $settings = SiteSetting::first() ?? new SiteSetting([
            'hero_subtitle' => 'We provide fast, accurate, and affordable plagiarism detection powered by cutting-edge AI. Whether you\'re a student, researcher, or professional, our tools ensure originality and integrity in your work. Get instant results, seamless reports, and a hassle-free experience—all at the best price.',
            'feature_tags' => ['Cheapest', 'Fastest', 'Affordable', 'AI Advanced'],
            'notice_header' => 'Plan Reminder',
            'notice_body' => 'Do not buy a new plan until your existing slots reach zero. If you buy a new plan early, your current slots will be removed and only the new plan’s slots will be added.',
        ]);

        return view('admin.settings', ['settings' => $settings]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'document_count' => ['required', 'string', 'max:255'],
            'document_label' => ['required', 'string', 'max:255'],
            'live_label' => ['required', 'string', 'max:255'],
            'hero_title' => ['required', 'string', 'max:255'],
            'hero_subtitle' => ['required', 'string'],
            'feature_tags' => ['nullable', 'string'],
            'faqs' => ['nullable', 'string'],
            'button_text' => ['required', 'string', 'max:255'],
            'button_link' => ['required', 'string', 'max:255'],
            'howto_title' => ['nullable', 'string', 'max:255'],
            'howto_steps' => ['nullable', 'string'],
            'howto_button_text' => ['nullable', 'string', 'max:255'],
            'howto_button_link' => ['nullable', 'string', 'max:255'],
            'howto_video_text' => ['nullable', 'string', 'max:255'],
            'howto_image' => ['nullable', 'image', 'max:4096'],
            'howto_embed' => ['nullable', 'string'],
            'notice_header' => ['nullable', 'string', 'max:255'],
            'notice_body' => ['nullable', 'string'],
        ]);

        $settings = SiteSetting::first() ?? new SiteSetting();

        $settings->fill([
            'site_name' => $data['site_name'],
            'document_count' => $data['document_count'],
            'document_label' => $data['document_label'],
            'live_label' => $data['live_label'],
            'hero_title' => $data['hero_title'],
            'hero_subtitle' => $data['hero_subtitle'],
            'button_text' => $data['button_text'],
            'button_link' => $data['button_link'],
            'howto_title' => $data['howto_title'] ?? null,
            'howto_button_text' => $data['howto_button_text'] ?? null,
            'howto_button_link' => $data['howto_button_link'] ?? null,
            'howto_video_text' => $data['howto_video_text'] ?? null,
            'howto_embed' => $data['howto_embed'] ?? null,
            'notice_header' => $data['notice_header'] ?? null,
            'notice_body' => $data['notice_body'] ?? null,
        ]);

        $tags = collect(explode(',', $data['feature_tags'] ?? ''))
            ->map(fn ($tag) => trim($tag))
            ->filter()
            ->values()
            ->all();

        $settings->feature_tags = $tags;

        $faqs = collect(preg_split('/\r\n|\r|\n/', $data['faqs'] ?? ''))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->map(function ($line) {
                $parts = explode('|', $line, 2);
                return [
                    'question' => trim($parts[0]),
                    'answer' => isset($parts[1]) ? trim($parts[1]) : '',
                ];
            })
            ->filter(fn ($faq) => $faq['question'] !== '')
            ->values()
            ->all();

        $settings->faqs = $faqs;

        $howtoSteps = collect(preg_split('/\r\n|\r|\n/', $data['howto_steps'] ?? ''))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();

        $settings->howto_steps = $howtoSteps;

        if ($request->hasFile('howto_image')) {
            if ($settings->howto_image_path) {
                Storage::disk('public')->delete($settings->howto_image_path);
            }

            $settings->howto_image_path = $request->file('howto_image')->store('howto', 'public');
        }

        $settings->save();

        return back()->with('status', 'Homepage content updated.');
    }
}

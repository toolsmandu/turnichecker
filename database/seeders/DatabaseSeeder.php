<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\SiteSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminEmail = env('ADMIN_EMAIL', 'admin@example.com');
        $adminPassword = env('ADMIN_PASSWORD', 'password');

        User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Admin',
                'password' => Hash::make($adminPassword),
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'Test Customer',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'email_verified_at' => now(),
            ]
        );

        SiteSetting::firstOrCreate(
            ['id' => 1],
            [
                'site_name' => 'TurnitDetect',
                'document_count' => '46,753,647+',
                'document_label' => 'Documents Checked',
                'live_label' => 'Live Count',
                'hero_title' => 'TurnitDetect',
                'hero_subtitle' => 'We provide fast, accurate, and affordable plagiarism detection powered by cutting-edge AI. Whether you\'re a student, researcher, or professional, our tools ensure originality and integrity in your work. Get instant results, seamless reports, and a hassle-free experience—all at the best price.',
                'feature_tags' => ['Cheapest', 'Fastest', 'Affordable', 'AI Advanced'],
                'faqs' => [
                    ['question' => 'How fast are results?', 'answer' => 'Reports generate in under 60 seconds for typical documents.'],
                    ['question' => 'Is there a free trial?', 'answer' => 'Yes, you can start for free and upgrade when you need more credits.'],
                    ['question' => 'Do you detect AI-written text?', 'answer' => 'Our detectors are tuned for both plagiarism and AI-generated content.'],
                ],
                'howto_title' => 'How to Use T-detector',
                'howto_steps' => [
                    'Upload your document and click “Start Checking”',
                    'Wait for processing on the History page',
                    'Download your official report',
                    'Watch the full demo video',
                ],
                'howto_button_text' => 'Start Checking',
                'howto_button_link' => '/register',
                'howto_video_text' => 'Watch the Full Demo Video',
                'button_text' => 'Get Started Free',
                'button_link' => '/register',
            ]
        );
    }
}

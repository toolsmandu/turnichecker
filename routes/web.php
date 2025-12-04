<?php

use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\PackController;
use App\Http\Controllers\Admin\SubmissionAdminController;
use App\Http\Controllers\Admin\ImpersonationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NewPasswordController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PasswordResetLinkController;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $settings = SiteSetting::first() ?? new SiteSetting([
        'site_name' => 'TurnitDetect',
        'document_count' => '46,753,647+',
        'document_label' => 'Documents Checked',
        'live_label' => 'Live Count',
        'hero_title' => 'TurnitDetect',
        'hero_subtitle' => 'We provide fast, accurate, and affordable plagiarism detection powered by cutting-edge AI. Whether you\'re a student, researcher, or professional, our tools ensure originality and integrity in your work. Get instant results, seamless reports, and a hassle-free experience—all at the best price.',
        'feature_tags' => ['Cheapest', 'Fastest', 'Affordable', 'AI Advanced'],
        'button_text' => 'Get Started Free',
        'button_link' => '/register',
    ]);

    return view('welcome', [
        'settings' => $settings,
        'featureTags' => $settings->feature_tags ?? [],
    ]);
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/submit', [DashboardController::class, 'submit'])->name('dashboard.submit');
    Route::delete('/dashboard/submissions/{submission}', [DashboardController::class, 'destroy'])->name('dashboard.submissions.destroy');
    Route::get('/submissions/{submission}/similarity', [DashboardController::class, 'downloadSimilarity'])->name('submissions.download.similarity');
    Route::get('/submissions/{submission}/ai', [DashboardController::class, 'downloadAi'])->name('submissions.download.ai');
    Route::get('/edit-details', [PasswordController::class, 'edit'])->name('account.password.edit');
    Route::post('/edit-details', [PasswordController::class, 'update'])->name('account.password.update');
    Route::get('/purchases', [DashboardController::class, 'purchases'])->name('account.purchases');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/settings', [SiteSettingController::class, 'edit'])->name('settings.edit');
    Route::post('/settings', [SiteSettingController::class, 'update'])->name('settings.update');
    Route::get('/packs', [PackController::class, 'index'])->name('packs.index');
    Route::post('/packs', [PackController::class, 'store'])->name('packs.store');
    Route::patch('/packs/{pack}', [PackController::class, 'update'])->name('packs.update');
    Route::delete('/packs/{pack}', [PackController::class, 'destroy'])->name('packs.destroy');
    Route::post('/packs/assign', [PackController::class, 'assign'])->name('packs.assign');
    Route::post('/packs/customers', [PackController::class, 'createCustomer'])->name('packs.customers.create');
    Route::post('/impersonate/{user}', [ImpersonationController::class, 'start'])->name('impersonate.start');
    Route::post('/customers/{user}/quota', [DashboardController::class, 'updateQuota'])->name('customers.quota.update');
    Route::post('/customers/{user}/subscription', [DashboardController::class, 'updateSubscription'])->name('customers.subscription.update');
    Route::post('/submissions/{submission}', [SubmissionAdminController::class, 'update'])->name('submissions.update');
    Route::delete('/submissions/{submission}', [SubmissionAdminController::class, 'destroy'])->name('submissions.destroy');
});

Route::post('/impersonate/stop', [ImpersonationController::class, 'stop'])->middleware('auth')->name('impersonate.stop');
Route::get('/customers', [DashboardController::class, 'customers'])->middleware(['auth', 'admin'])->name('admin.customers');

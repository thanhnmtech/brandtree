<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\BrandMemberController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LadipageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\SepayWebhookController;
use App\Http\Controllers\SubscriptionController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

// Ladipage API endpoint (outside localization)
Route::post('/api/ladipage/store', [LadipageController::class, 'store'])->name('ladipage.store');

// Sepay webhook (outside localization, no CSRF)
Route::post('/webhook/sepay', [SepayWebhookController::class, 'handle'])->name('webhook.sepay');

Route::group(['prefix' => LaravelLocalization::setLocale()], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(['auth', 'verified'])
        ->name('dashboard');

    Route::middleware('auth')->group(function () {
        // Profile routes
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // Brand routes
        Route::resource('brands', BrandController::class);

        // Brand Members routes
        Route::get('brands/{brand}/members', [BrandMemberController::class, 'index'])->name('brands.members.index');
        Route::get('brands/{brand}/members/create', [BrandMemberController::class, 'create'])->name('brands.members.create');
        Route::post('brands/{brand}/members', [BrandMemberController::class, 'store'])->name('brands.members.store');
        Route::put('brands/{brand}/members/{member}', [BrandMemberController::class, 'update'])->name('brands.members.update');
        Route::delete('brands/{brand}/members/{member}', [BrandMemberController::class, 'destroy'])->name('brands.members.destroy');

        // Subscription routes
        Route::get('brands/{brand}/subscription', [SubscriptionController::class, 'show'])->name('brands.subscription.show');
        Route::get('brands/{brand}/subscription/upgrade', [SubscriptionController::class, 'create'])->name('brands.subscription.create');
        Route::post('brands/{brand}/subscription', [SubscriptionController::class, 'store'])->name('brands.subscription.store');
        Route::delete('brands/{brand}/subscription', [SubscriptionController::class, 'destroy'])->name('brands.subscription.destroy');

        // Plans route (public pricing page)
        Route::get('plans', [PlanController::class, 'index'])->name('plans.index');

        // Payment routes
        Route::get('brands/{brand}/payments', [PaymentController::class, 'index'])->name('brands.payments.index');
        Route::get('brands/{brand}/payments/create', [PaymentController::class, 'create'])->name('brands.payments.create');
        Route::post('brands/{brand}/payments', [PaymentController::class, 'store'])->name('brands.payments.store');
        Route::get('brands/{brand}/payments/{payment}', [PaymentController::class, 'show'])->name('brands.payments.show');
        Route::post('brands/{brand}/payments/{payment}/check', [PaymentController::class, 'checkStatus'])->name('brands.payments.check');

        // Credit usage routes
        Route::get('brands/{brand}/credits', [CreditController::class, 'index'])->name('brands.credits.index');
        Route::get('brands/{brand}/credits/statistics', [CreditController::class, 'statistics'])->name('brands.credits.statistics');
        Route::get('brands/{brand}/credits/export', [CreditController::class, 'export'])->name('brands.credits.export');
    });

    require __DIR__ . '/auth.php';
});

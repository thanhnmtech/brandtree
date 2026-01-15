<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LadipageController;
use App\Http\Controllers\BrandTreeController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\BrandMemberController;
use App\Http\Controllers\SepayWebhookController;
use App\Http\Controllers\SubscriptionController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

// Ladipage API endpoint (outside localization)
Route::post('/api/ladipage/store', [LadipageController::class, 'store'])->name('ladipage.store');

// Sepay webhook (outside localization, no CSRF)
Route::post('/webhook/sepay', [SepayWebhookController::class, 'handle'])->name('webhook.sepay');

// Chat API Routes
Route::post('/api/chat_stream', [App\Http\Controllers\ChatStreamController::class, 'stream'])->name('api.chat.stream');
Route::post('/api/chat/save_message', [App\Http\Controllers\ChatStreamController::class, 'saveMessage'])->name('api.chat.save');

Route::group(['prefix' => LaravelLocalization::setLocale()], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/app', [DashboardController::class, 'index'])
        ->middleware(['auth', 'verified'])
        ->name('dashboard');

    Route::get('/app/filter', [DashboardController::class, 'filter'])
        ->middleware(['auth', 'verified'])
        ->name('dashboard.filter');

    Route::middleware('auth')->group(function () {
        // Profile routes
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // Brand resource routes (index, create, store don't need brand.access)
        Route::resource('brands', BrandController::class)->only(['store']);

        // Routes that require brand access check
        Route::prefix('brands/{brand}')
            ->middleware('brand.access')
            ->group(function () {
                // Brand resource routes (show, update, destroy)
                Route::get('/', [BrandController::class, 'show'])->name('brands.show');
                Route::put('/', [BrandController::class, 'update'])->name('brands.update');
                Route::patch('/', [BrandController::class, 'update']);
                Route::delete('/', [BrandController::class, 'destroy'])->name('brands.destroy');

                // Brand Members routes
                Route::get('members', [BrandMemberController::class, 'index'])->name('brands.members.index');
                Route::get('members/create', [BrandMemberController::class, 'create'])->name('brands.members.create');
                Route::post('members', [BrandMemberController::class, 'store'])->name('brands.members.store');
                Route::put('members/{member}', [BrandMemberController::class, 'update'])->name('brands.members.update');
                Route::delete('members/{member}', [BrandMemberController::class, 'destroy'])->name('brands.members.destroy');

                // Subscription routes
                Route::get('subscription', [SubscriptionController::class, 'show'])->name('brands.subscription.show');
                Route::get('subscription/upgrade', [SubscriptionController::class, 'create'])->name('brands.subscription.create');
                Route::post('subscription', [SubscriptionController::class, 'store'])->name('brands.subscription.store');
                Route::delete('subscription', [SubscriptionController::class, 'destroy'])->name('brands.subscription.destroy');

                // Payment routes
                Route::get('payments', [PaymentController::class, 'index'])->name('brands.payments.index');
                Route::get('payments/create', [PaymentController::class, 'create'])->name('brands.payments.create');
                Route::post('payments', [PaymentController::class, 'store'])->name('brands.payments.store');
                Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('brands.payments.show');
                Route::post('payments/{payment}/check', [PaymentController::class, 'checkStatus'])->name('brands.payments.check');
                Route::get('payments/{payment}/status', [PaymentController::class, 'checkStatusAjax'])->name('brands.payments.status');

                // Credit usage routes
                Route::get('credits', [CreditController::class, 'index'])->name('brands.credits.stats');
                Route::get('credits/index', [CreditController::class, 'index'])->name('brands.credits.index');
                Route::get('credits/export', [CreditController::class, 'export'])->name('brands.credits.export');

                //Build Brand Tree
                Route::get('root', [BrandTreeController::class, 'root'])->name('brands.root.show');
                Route::get('trunk', [BrandTreeController::class, 'trunk'])->name('brands.trunk.show');
                Route::get('canopy', [BrandTreeController::class, 'canopy'])->name('brands.canopy.show');

            });
        Route::view('/dashboard-goc', 'dashboard.dashboard-goc');
        Route::get('/chat/{agentType?}/{agentId?}/{convId?}', function ($agentType = null, $agentId = null, $convId = null) {
            return view('chat.chat', compact('agentType', 'agentId', 'convId'));
        })->name('chat');
    });

    require __DIR__ . '/auth.php';
});

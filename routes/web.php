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
Route::post('/api/chat_stream_gemini', [App\Http\Controllers\GeminiChatController::class, 'stream'])->name('api.chat.stream.gemini');
Route::get('/api/gemini/test', [App\Http\Controllers\GeminiTestController::class, 'test'])->name('api.gemini.test');
Route::post('/api/chat/save_message', [App\Http\Controllers\ChatStreamController::class, 'saveMessage'])->name('api.chat.save');
Route::get('/api/chat/history', [App\Http\Controllers\ChatStreamController::class, 'history'])->name('api.chat.history');
Route::put('/api/chat/{id}/rename', [App\Http\Controllers\ChatStreamController::class, 'rename'])->name('api.chat.rename');

// File Upload API Routes (RAG)
Route::middleware('auth')->group(function () {
    Route::post('/api/files/upload', [App\Http\Controllers\FileUploadController::class, 'uploadForChat'])->name('api.files.upload');
    Route::get('/api/files/{file}/status', [App\Http\Controllers\FileUploadController::class, 'status'])->name('api.files.status');
    Route::delete('/api/files/{file}', [App\Http\Controllers\FileUploadController::class, 'destroy'])->name('api.files.destroy');
    Route::get('/api/files/chat/{chatId}', [App\Http\Controllers\FileUploadController::class, 'listForChat'])->name('api.files.chat');
    Route::get('/api/files/agent/{agentId}', [App\Http\Controllers\FileUploadController::class, 'listForAgent'])->name('api.files.agent');
    // Upload file cho Agent (canopy)
    Route::post('/brands/{brand}/agents/{agent}/files', [App\Http\Controllers\FileUploadController::class, 'uploadForAgent'])->name('api.agents.files.upload');
});

// Chat Route (Standalone, No Localization Prefix)
Route::get('/brands/{brand:slug}/chat/{agentType?}/{agentId?}/{convId?}', function (App\Models\Brand $brand, $agentType = null, $agentId = null, $convId = null) {

    // Override agentId for System Agents
    $systemTypes = ['root1', 'root2', 'root3', 'trunk1', 'trunk2'];
    if ($agentType && in_array($agentType, $systemTypes)) {
        $systemAgent = \App\Models\AgentSystem::where('agent_type', $agentType)
            ->latest()
            ->first();
        if ($systemAgent) {
            $agentId = $systemAgent->id;
        }
    }
    
    //Lấy menu items cho agent type hiện tại
    $dataPlatformMenuItems = config("data_platform_menus.{$agentType}", []);

    return view('chat.chat', compact('brand', 'agentType', 'agentId', 'convId', 'dataPlatformMenuItems'));
})->middleware(['auth', 'brand.access'])->name('chat');

Route::post('/brands/{brand:slug}/chat/save-data', [\App\Http\Controllers\BrandDataController::class, 'store'])
    ->middleware(['auth', 'brand.access'])
    ->name('brands.chat.save');

Route::post('/brands/{brand:slug}/update-section', [\App\Http\Controllers\BrandDataController::class, 'updateSection'])
    ->middleware(['auth', 'brand.access'])
    ->name('brands.update_section');

Route::post('/brands/{brand:slug}/agents', [\App\Http\Controllers\BrandAgentController::class, 'store'])
    ->middleware(['auth', 'brand.access'])
    ->name('brands.agents.store');

Route::post('/brands/{brand:slug}/agents/from-template', [\App\Http\Controllers\BrandAgentController::class, 'storeFromTemplate'])
    ->middleware(['auth', 'brand.access'])
    ->name('brands.agents.store-from-template');

Route::delete('/brands/{brand:slug}/agents/{agent}', [\App\Http\Controllers\BrandAgentController::class, 'destroy'])
    ->middleware(['auth', 'brand.access'])
    ->name('brands.agents.destroy');

Route::put('/brands/{brand:slug}/agents/{agent}', [\App\Http\Controllers\BrandAgentController::class, 'update'])
    ->middleware(['auth', 'brand.access'])
    ->name('brands.agents.update');

// TEMPORARY: Run migrations via link (For agent_type column)
Route::get('/run-pending-migrations', function () {
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    return "Migrations run successfully (Check DB): " . \Illuminate\Support\Facades\Artisan::output();
});

Route::get('/debug/rag-logs', function () {
    $path = storage_path('logs/rag_debug.log');
    if (!file_exists($path)) {
        return "Log file not found. Upload a file to generate logs.";
    }
    $content = file_get_contents($path);
    return response($content)->header('Content-Type', 'text/plain');
});

Route::get('/debug/chat-ai-logs', [App\Http\Controllers\LogViewerController::class, 'index'])->middleware('auth');







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
        // Avatar routes
        Route::patch('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
        Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');
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
                Route::post('subscription/renew', [SubscriptionController::class, 'renew'])->name('brands.subscription.renew');
                Route::delete('subscription', [SubscriptionController::class, 'destroy'])->name('brands.subscription.destroy');

                // Payment routes
                Route::get('payments', [PaymentController::class, 'index'])->name('brands.payments.index');
                Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('brands.payments.show');
                Route::post('payments/{payment}/check', [PaymentController::class, 'checkStatus'])->name('brands.payments.check');
                Route::get('payments/{payment}/status', [PaymentController::class, 'checkStatusAjax'])->name('brands.payments.status');

                // Credit usage routes
                Route::get('credits', [CreditController::class, 'index'])->name('brands.credits.stats');
                Route::get('credits/index', [CreditController::class, 'index'])->name('brands.credits.index');
                Route::get('credits/export', [CreditController::class, 'export'])->name('brands.credits.export');

                //Build Brand Tree
                Route::get('root', [BrandTreeController::class, 'root'])->name('brands.root.show');

                // Route::get('root/{step}', [BrandTreeController::class, 'step'])->name('brands.root.step');
    
                Route::get('trunk', [BrandTreeController::class, 'trunk'])->name('brands.trunk.show');
                Route::get('canopy', [BrandTreeController::class, 'canopy'])->name('brands.canopy.show');

            });

        Route::view('/dashboard-goc', 'dashboard.dashboard-goc');

    });

    require __DIR__ . '/auth.php';
});

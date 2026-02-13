<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\RagServiceInterface;
use App\Services\Rag\LocalRagService;
use App\Services\Rag\ApiRagService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     * Đăng ký các service bindings
     */
    public function register(): void
    {
        // Đăng ký RAG service dựa trên config driver
        // - 'local': Dùng LocalRagService (MariaDB Vector + Gemini Embedding)
        // - 'api': Dùng ApiRagService (gọi đến microservice riêng)
        $this->app->singleton(RagServiceInterface::class, function ($app) {
            return match (config('rag.driver')) {
                'api' => $app->make(ApiRagService::class),
                default => $app->make(LocalRagService::class),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

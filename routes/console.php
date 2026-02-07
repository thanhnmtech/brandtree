<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Expire subscriptions - runs daily at 1:00 AM to mark expired subscriptions
Schedule::command('subscriptions:expire')
    ->dailyAt('01:00')
    ->timezone('Asia/Ho_Chi_Minh')
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Failed to expire subscriptions');
    })
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Subscription expiration check completed');
    });

// Reset monthly credits - runs daily at 1:05 AM to check subscriptions that need monthly reset
Schedule::command('credits:reset-monthly')
    ->dailyAt('01:05')
    ->timezone('Asia/Ho_Chi_Minh')
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Failed to reset monthly credits');
    })
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Monthly credits reset completed');
    });

<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Reset monthly credits - runs every hour to check subscriptions that need reset
Schedule::command('credits:reset-monthly')->hourly();

// Expire subscriptions - runs daily at midnight
Schedule::command('subscriptions:expire')->daily();

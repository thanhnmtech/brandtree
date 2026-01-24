<?php

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class DynamicConfigServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadDynamicConfig();
    }

    /**
     * Load dynamic configuration from database.
     */
    protected function loadDynamicConfig(): void
    {
        try {
            if (!Schema::hasTable('settings')) {
                return;
            }

            $settings = DB::table('settings')
                ->whereIn('group', ['email', 'account', 'finance'])
                ->pluck('payload', 'name')
                ->map(fn($value) => json_decode($value, true))
                ->toArray();

            if (empty($settings)) {
                return;
            }

            $this->configureMailSettings($settings);
            $this->configureGoogleOAuth($settings);
            $this->configureSepay($settings);
        } catch (\Exception $e) {
            // Silently fail during migrations or when table doesn't exist
        }
    }

    /**
     * Configure mail settings from database.
     */
    protected function configureMailSettings(array $settings): void
    {
        if (!isset($settings['smtp_host'])) {
            return;
        }

        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.host', $settings['smtp_host'] ?? config('mail.mailers.smtp.host'));
        Config::set('mail.mailers.smtp.port', $settings['smtp_port'] ?? config('mail.mailers.smtp.port'));
        Config::set('mail.mailers.smtp.username', $settings['smtp_username'] ?? config('mail.mailers.smtp.username'));
        Config::set('mail.mailers.smtp.password', $settings['smtp_password'] ?? config('mail.mailers.smtp.password'));
        Config::set('mail.mailers.smtp.encryption', $settings['smtp_encryption'] ?? config('mail.mailers.smtp.encryption'));

        Config::set('mail.from.address', $settings['mail_from_address'] ?? config('mail.from.address'));
        Config::set('mail.from.name', $settings['mail_from_name'] ?? config('mail.from.name'));
    }

    /**
     * Configure Google OAuth settings from database.
     */
    protected function configureGoogleOAuth(array $settings): void
    {
        if (!isset($settings['google_client_id'])) {
            return;
        }

        Config::set('services.google.client_id', $settings['google_client_id'] ?? config('services.google.client_id'));
        Config::set('services.google.client_secret', $settings['google_client_secret'] ?? config('services.google.client_secret'));
        Config::set('services.google.redirect', $settings['google_redirect_uri'] ?? config('services.google.redirect'));
    }

    /**
     * Configure Sepay settings from database.
     */
    protected function configureSepay(array $settings): void
    {
        if (!isset($settings['sepay_webhook_secret'])) {
            return;
        }

        Config::set('services.sepay.webhook_secret', $settings['sepay_webhook_secret'] ?? config('services.sepay.webhook_secret'));
        Config::set('services.sepay.bank_account_number', $settings['payment_account_number'] ?? config('services.sepay.bank_account_number'));
        Config::set('services.sepay.bank_name', $settings['payment_bank_name'] ?? config('services.sepay.bank_name'));
        Config::set('services.sepay.bank_account_name', $settings['payment_account_name'] ?? config('services.sepay.bank_account_name'));
    }
}

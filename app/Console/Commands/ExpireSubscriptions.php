<?php

namespace App\Console\Commands;

use App\Models\BrandSubscription;
use Illuminate\Console\Command;

class ExpireSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark expired subscriptions as expired';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking for expired subscriptions...');

        $count = BrandSubscription::where('status', BrandSubscription::STATUS_ACTIVE)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->update(['status' => BrandSubscription::STATUS_EXPIRED]);

        $this->info("Marked {$count} subscriptions as expired.");

        return Command::SUCCESS;
    }
}

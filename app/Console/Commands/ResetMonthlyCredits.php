<?php

namespace App\Console\Commands;

use App\Services\CreditService;
use Illuminate\Console\Command;

class ResetMonthlyCredits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'credits:reset-monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset monthly credits for active subscriptions';

    /**
     * Execute the console command.
     */
    public function handle(CreditService $creditService): int
    {
        $this->info('Starting monthly credit reset...');

        $count = $creditService->resetMonthlyCredits();

        $this->info("Successfully reset credits for {$count} subscriptions.");

        return Command::SUCCESS;
    }
}

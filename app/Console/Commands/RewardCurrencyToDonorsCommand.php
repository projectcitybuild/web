<?php

namespace App\Console\Commands;

use Domain\CurrencyRewarder\UseCases\RewardCurrencyUseCase;
use Illuminate\Console\Command;

class RewardCurrencyToDonorsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'donor-perks:reward-currency';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rewards currency to accounts that are currently donating';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(RewardCurrencyUseCase $rewardCurrencyUseCase)
    {
        $rewardCurrencyUseCase->execute();

        return 0;
    }
}

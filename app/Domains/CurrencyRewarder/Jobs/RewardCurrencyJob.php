<?php

namespace App\Domains\CurrencyRewarder\Jobs;

use App\Domains\CurrencyRewarder\CurrencyRewarder;
use App\Models\DonationPerk;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RewardCurrencyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected DonationPerk $perk
    ) {
    }

    /**
     * Execute the job.
     *
     * @throws \Exception
     */
    public function handle(CurrencyRewarder $currencyRewarder): void
    {
        $currencyRewarder->rewardTo($this->perk);
    }
}

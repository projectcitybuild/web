<?php

namespace App\Core\Domains\Discord;

use App\Models\DonationPerk;

final class DiscordDonationPerkObserver
{
    public function __construct(
        private readonly DiscordRoleSyncDispatcher $dispatcher,
    ) {}

    public function created(DonationPerk $donationPerk): void
    {
        $this->dispatcher->dispatchForAccountId((int) $donationPerk->account_id);
    }

    public function updated(DonationPerk $donationPerk): void
    {
        $previousAccountId = (int) $donationPerk->getOriginal('account_id');
        $accountId = (int) $donationPerk->account_id;

        if ($previousAccountId !== $accountId) {
            $this->dispatcher->dispatchForAccountId($previousAccountId);
        }

        $this->dispatcher->dispatchForAccountId($accountId);
    }

    public function deleted(DonationPerk $donationPerk): void
    {
        $this->dispatcher->dispatchForAccountId((int) $donationPerk->account_id);
    }
}

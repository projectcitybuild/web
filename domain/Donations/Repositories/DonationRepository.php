<?php

namespace Domain\Donations\Repositories;

use App\Entities\Models\Eloquent\Donation;
use Domain\Donations\Entities\PaidAmount;

final class DonationRepository
{
    public function create(
        int $accountId,
        PaidAmount $paidAmount,
    ): Donation {
        return Donation::create([
            'account_id' => $accountId,
            'amount' => $paidAmount->toDollars(),
        ]);
    }
}

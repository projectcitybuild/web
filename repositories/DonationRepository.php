<?php

namespace Repositories;

use App\Domains\Donations\Entities\PaidAmount;
use App\Models\Donation;

/**
 * @deprecated
 */
class DonationRepository
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

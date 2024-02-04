<?php

namespace Repositories;

use Domain\Donations\Entities\PaidAmount;
use Entities\Models\Eloquent\Donation;

/**
 * @final
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

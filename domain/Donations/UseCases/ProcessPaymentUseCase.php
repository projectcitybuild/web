<?php

namespace Domain\Donations\UseCases;

use App\Entities\Models\Eloquent\Account;
use App\Entities\Models\Eloquent\DonationPerk;
use App\Entities\Notifications\DonationPerkStartedNotification;
use Domain\Donations\DonationGroupSyncService;
use Domain\Donations\Entities\DonationType;
use Domain\Donations\Entities\PaidAmount;
use Domain\Donations\Repositories\DonationPerkRepository;
use Domain\Donations\Repositories\DonationRepository;
use Domain\Donations\Repositories\PaymentRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

final class ProcessPaymentUseCase
{
    public function __construct(
        private DonationGroupSyncService $groupSyncService,
        private PaymentRepository $paymentRepository,
        private DonationPerkRepository $donationPerkRepository,
        private DonationRepository $donationRepository,
    ) {}

    public function execute(
        Account $account,
        string $productId,
        string $priceId,
        int $donationTierId,
        PaidAmount $paidAmount,
        int $quantity,
        DonationType $donationType,
    ) {
        $existingPerk = $this->donationPerkRepository->first(
            accountId: $account->getKey(),
            donationTierId: $donationTierId,
        );

        $expiryDate = $this->calculateExpiryDate(
            numberOfMonths: $quantity,
            existingPerk: $existingPerk,
        );

        DB::beginTransaction();
        try {
            $this->paymentRepository->create(
                accountId: $account->getKey(),
                productId: $productId,
                priceId: $priceId,
                paidAmount: $paidAmount,
                quantity: $quantity,
                isSubscription: $donationType == DonationType::SUBSCRIPTION,
            );

            $donation = $this->donationRepository->create(
                accountId: $account->getKey(),
                paidAmount: $paidAmount,
            );

            $this->donationPerkRepository->create(
                donationId: $donation->getKey(),
                donationTierId: $donationTierId,
                accountId: $account->getKey(),
                expiresAt: $expiryDate,
            );

            DB::commit();
        }
        catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        if (! env(key: 'IS_E2E_TEST', default: false)) {
            // Too hard to reliable call this in E2E testing with our current set up
            $this->groupSyncService->addToDonorGroup($account);
        }

        $notification = new DonationPerkStartedNotification($expiryDate);
        $account->notify($notification);
    }

    /**
     * Calculates an expiry date `$numberOfMonths` into the future either from
     * now or from the given donation perk's expiry date. Whichever is furthest
     * into the future.
     *
     * This is to ensure the user gets their full duration of perks even if
     * they donated before their previous perks had expired.
     *
     * @param int $numberOfMonths Number of months to add to the base date
     * @param DonationPerk|null $existingPerk The user's latest DonationPerk
     * @return Carbon Expiry date
     */
    private function calculateExpiryDate(int $numberOfMonths, ?DonationPerk $existingPerk): Carbon
    {
        $monthsFromNow = now()->addMonths($numberOfMonths);

        if ($existingPerk == null) {
            return $monthsFromNow;
        }

        $monthsFromLastExpiry = $existingPerk->expires_at->addMonths($numberOfMonths);

        return $monthsFromLastExpiry->gt($monthsFromNow)
            ? $monthsFromLastExpiry
            : $monthsFromNow;
    }
}

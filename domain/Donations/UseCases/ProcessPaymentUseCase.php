<?php

namespace Domain\Donations\UseCases;

use Domain\Donations\Entities\PaidAmount;
use Domain\Donations\Entities\PaymentType;
use Domain\Donations\Repositories\DonationPerkRepository;
use Domain\Donations\Repositories\DonationRepository;
use Domain\Donations\Repositories\PaymentRepository;
use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\DonationPerk;
use Entities\Models\Eloquent\Group;
use Entities\Notifications\DonationPerkStartedNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Shared\Groups\GroupsManager;

final class ProcessPaymentUseCase
{
    public function __construct(
        private GroupsManager $groupsManager,
        private PaymentRepository $paymentRepository,
        private DonationPerkRepository $donationPerkRepository,
        private DonationRepository $donationRepository,
        private Group $donorGroup,
    ) {}

    public function execute(
        Account $account,
        string $productId,
        string $priceId,
        int $donationTierId,
        PaidAmount $paidAmount,
        int $quantity,
        PaymentType $donationType,
    ) {
        $existingPerk = $this->donationPerkRepository->lastToExpire(
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
                isSubscription: $donationType == PaymentType::SUBSCRIPTION,
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

        $this->groupsManager->addMember(group: $this->donorGroup, account: $account);

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

        $monthsFromLastExpiry = $existingPerk->expires_at->copy()->addMonths($numberOfMonths);

        return $monthsFromLastExpiry->gt($monthsFromNow)
            ? $monthsFromLastExpiry
            : $monthsFromNow;
    }
}

<?php

namespace Domain\Donations\UseCases;

use App\Exceptions\Http\BadRequestException;
use Domain\Donations\Entities\PaidAmount;
use Domain\Donations\Entities\PaymentType;
use Domain\Donations\Events\DonationPerkCreated;
use Domain\Donations\Exceptions\StripeProductNotFoundException;
use Domain\Donations\Repositories\DonationPerkRepository;
use Domain\Donations\Repositories\DonationRepository;
use Domain\Donations\Repositories\PaymentRepository;
use Domain\Donations\Repositories\StripeProductRepository;
use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\DonationPerk;
use Entities\Models\Eloquent\Group;
use Entities\Notifications\DonationPerkStartedNotification;
use Illuminate\Support\Carbon;
use Shared\Groups\GroupsManager;

final class ProcessPaymentUseCase
{
    public function __construct(
        private GroupsManager $groupsManager,
        private PaymentRepository $paymentRepository,
        private DonationPerkRepository $donationPerkRepository,
        private DonationRepository $donationRepository,
        private StripeProductRepository $stripeProductRepository,
        private Group $donorGroup,
    ) {}

    /**
     * @throws StripeProductNotFoundException if productId does not exist in the StripeProducts table
     * @throws BadRequestException if quantity or paidAmount is invalid
     */
    public function execute(
        Account $account,
        string $productId,
        string $priceId,
        PaidAmount $paidAmount,
        int $quantity,
        PaymentType $donationType,
    ) {
        // Sanity checks
        if ($paidAmount->toCents() <= 0) {
            throw new BadRequestException(id: 'invalid_amount', message: 'Amount paid was zero');
        }
        if ($quantity <= 0) {
            throw new BadRequestException(id: 'invalid_quantity', message: 'Quantity purchased was zero');
        }

        $product = $this->stripeProductRepository->first(productId: $productId, priceId: $priceId)
            ?? throw new StripeProductNotFoundException();

        $donation = $this->donationRepository->create(
            accountId: $account->getKey(),
            paidAmount: $paidAmount,
        );

        $this->paymentRepository->create(
            accountId: $account->getKey(),
            productId: $productId,
            priceId: $priceId,
            paidAmount: $paidAmount,
            quantity: $quantity,
            isSubscription: $donationType == PaymentType::SUBSCRIPTION,
        );

        $donationTier = $product->donationTier;
        if ($donationTier !== null) {
            $existingPerk = $this->donationPerkRepository->lastToExpire(
                accountId: $account->getKey(),
                donationTierId: $donationTier->getKey(),
            );

            $expiryDate = $this->calculateExpiryDate(
                numberOfMonths: $quantity,
                existingPerk: $existingPerk,
            );

            $newPerk = $this->donationPerkRepository->create(
                donationId: $donation->getKey(),
                donationTierId: $donationTier?->getKey(),
                accountId: $account->getKey(),
                expiresAt: $expiryDate,
            );

            $this->groupsManager->addMember(group: $this->donorGroup, account: $account);

            $notification = new DonationPerkStartedNotification($expiryDate);
            $account->notify($notification);

            DonationPerkCreated::dispatch($newPerk);
        }
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

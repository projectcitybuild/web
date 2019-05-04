<?php
namespace App\Services\Donations;

use App\Library\Stripe\StripePaymentProvider;
use App\Entities\Accounts\Models\Account;
use App\Entities\Groups\GroupEnum;
use App\Entities\Donations\Repositories\DonationRepository;
use App\Entities\Payments\Repositories\AccountPaymentRepository;
use App\Entities\Payments\AccountPaymentType;
use App\Entities\Groups\Repositories\GroupRepository;
use App\Services\Groups\DiscourseGroupSyncService;
use Illuminate\Support\Facades\DB;
use App\Library\API\APIClientProvider;
use App\Requests\Discourse\DiscourseExternalUserIdRequest;

final class DonationProvider
{
    /**
     * Amount that needs to be donated to be granted
     * lifetime perks
     */
    private const LIFETIME_REQUIRED_AMOUNT = 30;

    private $donationRepository;
    private $paymentRepository;
    private $groupRepository;
    private $stripePaymentProvider;
    private $discourseGroupSyncService;
    private $apiClient;


    public function __construct(
        DonationRepository $donationRepository,
        AccountPaymentRepository $paymentRepository,
        GroupRepository $groupRepository,
        StripePaymentProvider $stripePaymentProvider,
        APIClientProvider $apiClient,
        DiscourseGroupSyncService $discourseGroupSyncService
    ) {
        $this->donationRepository = $donationRepository;
        $this->paymentRepository = $paymentRepository;
        $this->stripePaymentProvider = $stripePaymentProvider;
        $this->groupRepository = $groupRepository;
        $this->discourseGroupSyncService = $discourseGroupSyncService;
        $this->apiClient = $apiClient;
    }

    public function beginDonationSession()
    {
        
    }

    public function performDonation(string $stripeToken, string $email, int $amountInCents, ?Account $account = null)
    {
        $amountInDollars = (float)($amountInCents / 100);

        try {
            $this->stripePaymentProvider->charge($amountInCents, $stripeToken, null, $email, 'PCB Contribution');
        } catch (\Exception $e) {
            throw $e;
        }

        $isLifetime = $amountInDollars >= self::LIFETIME_REQUIRED_AMOUNT;
        if ($isLifetime) {
            $donationExpiry = null;
        } else {
            $donationExpiry = now()->addMonths(floor($amountInDollars / 3));
        }

        $pcbId = $account !== null ? $account->getKey() : null;

        DB::beginTransaction();
        try {
            $donation = $this->donationRepository->create(
                $pcbId,
                $amountInDollars,
                $donationExpiry,
                $isLifetime
            );
            $this->paymentRepository->create(
                new AccountPaymentType(AccountPaymentType::Donation),
                $donation->getKey(),
                $amountInCents,
                $stripeToken,
                $pcbId,
                true
            );
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

         // add user to donator group if they're logged in
         if ($account !== null) {
            $group = new GroupEnum(GroupEnum::Donator);
            $donatorGroup = $this->groupRepository->getGroupByName(GroupEnum::Donator);
            $donatorGroupId = $donatorGroup->getKey();
            
            if ($account->groups->contains($donatorGroupId) === false) {
                $request = new DiscourseExternalUserIdRequest($account->getKey());
                $discourseUser = $this->apiClient->call($request);
                $discourseId = $discourseUser['user']['id'];
    
                $this->discourseGroupSyncService->addUserToGroup($discourseId, $account, $group);
            }
        }

        return $donation;
    }
}
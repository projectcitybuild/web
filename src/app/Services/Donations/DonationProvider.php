<?php
namespace App\Services\Donations;

use App\Library\Stripe\StripePaymentProvider;
use App\Entities\Groups\GroupEnum;
use App\Entities\Donations\Repositories\DonationRepository;
use App\Entities\Payments\Repositories\AccountPaymentRepository;
use App\Entities\Payments\AccountPaymentType;
use App\Entities\Groups\Repositories\GroupRepository;
use App\Services\Groups\DiscourseGroupSyncService;
use Illuminate\Support\Facades\DB;
use App\Library\API\APIClientProvider;
use App\Requests\Discourse\DiscourseExternalUserIdRequest;
use App\Library\Stripe\StripeLineItem;
use App\Entities\AcceptedCurrencyType;
use App\Entities\Payments\Models\PaymentSession;
use App\Entities\Payments\Repositories\PaymentSessionRepository;
use App\Entities\Accounts\Repositories\AccountRepository;
use App\Entities\Accounts\Models\Account;
use Illuminate\Support\Str;

final class DonationProvider
{
    /**
     * Amount that needs to be donated to be granted
     * lifetime perks
     */
    private const LIFETIME_REQUIRED_AMOUNT = 30;

    /**
     * Every X dollars grants 1 month of donator perks
     */
    private const DOLLARS_PER_MONTH = 3;

    private $donationRepository;
    private $paymentRepository;
    private $groupRepository;
    private $paymentSessionRepository;
    private $accountRepository;
    private $stripePaymentProvider;
    private $discourseGroupSyncService;
    private $apiClient;


    public function __construct(
        DonationRepository $donationRepository,
        AccountPaymentRepository $paymentRepository,
        GroupRepository $groupRepository,
        PaymentSessionRepository $paymentSessionRepository,
        AccountRepository $accountRepository,
        StripePaymentProvider $stripePaymentProvider,
        APIClientProvider $apiClient,
        DiscourseGroupSyncService $discourseGroupSyncService
    ) {
        $this->donationRepository = $donationRepository;
        $this->paymentRepository = $paymentRepository;
        $this->stripePaymentProvider = $stripePaymentProvider;
        $this->groupRepository = $groupRepository;
        $this->paymentSessionRepository = $paymentSessionRepository;
        $this->accountRepository = $accountRepository;
        $this->discourseGroupSyncService = $discourseGroupSyncService;
        $this->apiClient = $apiClient;
    }

    public function beginDonationSession(?Account $account, int $amountInCents) : string
    {
        $stripeSessionId = $this->stripePaymentProvider->beginSession(
            route('front.donate.complete'),
            route('front.donate'),
            Str::uuid()->toString(),
            [
                new StripeLineItem(
                    $amountInCents, 
                    new AcceptedCurrencyType(AcceptedCurrencyType::CURRENCY_AUD),
                    'PCB Contribution',
                    1,
                    'To help pay for the ongoing server/running costs of our servers'
                ),
            ]
        );

        // stores a session in our database so that when the payment has
        // been processed by Stripe, we have all the information needed to
        // complete the process (ie. give the user their perks)
        $internalSession = new DonationPaymentSession($account, $amountInCents);
        $serializedSession = json_encode($internalSession);
        
        $this->paymentSessionRepository->create(
            $stripeSessionId,
            $serializedSession,
            now()->addHours(12)
        );

        return $stripeSessionId;
    }

    public function fulfillDonation(string $signatureHeader, string $payload)
    {
        $event = $this->stripePaymentProvider->interceptAndVerifyWebhook($payload, $signatureHeader);

        if ($event->type !== 'checkout.session.completed') {
            return;
        }

        $session = $event->data->object;
        $clientReferenceId = $session->client_reference_id;
        
        $internalSession = $this->paymentSessionRepository->getByExternalSessionId($session->id);
        if ($internalSession === null) {
            throw new \Exception('No internal session found for incoming Stripe session webhook');
        }

        $this->assignPerksToUser($clientReferenceId, $internalSession);
    }

    private function assignPerksToUser(string $clientReferenceId, PaymentSession $session)
    {
        $donationSession = DonationPaymentSession::createFromJSON($session->data);
        $amountInDollars = (float)($donationSession->getAmountInCents() / 100);
        
        $isLifetime = $amountInDollars >= self::LIFETIME_REQUIRED_AMOUNT;
        if ($isLifetime) {
            $donationExpiry = null;
        } else {
            $donationExpiry = now()->addMonths(floor($amountInDollars / self::DOLLARS_PER_MONTH));
        }

        DB::beginTransaction();
        try {
            $donation = $this->donationRepository->create(
                $donationSession->getAccountId(),
                $amountInDollars,
                $donationExpiry,
                $isLifetime
            );
            
            $this->paymentRepository->create(
                new AccountPaymentType(AccountPaymentType::Donation),
                $donation->getKey(),
                $donationSession->getAmountInCents(),
                $clientReferenceId,
                $donationSession->getAccountId(),
                true
            );
            
            $this->paymentSessionRepository->deleteById($session->getKey());

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        $account = $this->accountRepository->getById($donationSession->getAccountId());

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
    }
}
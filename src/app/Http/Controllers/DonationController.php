<?php

namespace App\Http\Controllers;

use App\Entities\Eloquent\Donations\Repositories\DonationRepository;
use App\Services\Donations\DonationCreationService;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard as Auth;
use App\Entities\Eloquent\Groups\GroupEnum;
use App\Services\Groups\DiscourseGroupSyncService;
use App\Entities\Eloquent\Groups\Repositories\GroupRepository;
use App\Entities\Requests\Discourse\DiscourseUserFetchAPIRequest;
use App\Http\WebController;
use App\Library\APIClient\APIClient;

class DonationController extends WebController
{
    /**
     * @var DonationRepository
     */
    private $donationRepository;

    /**
     * @var DonationCreationService
     */
    private $donationCreationService;

    /**
     * @var APIClient
     */
    private $apiClient;

    /**
     * @var DiscourseGroupSyncService
     */
    private $groupSyncService;

    /**
     * @var GroupRepository
     */
    private $groupRepository;

    /**
     * @var Auth
     */
    private $auth;


    public function __construct(
        DonationRepository $donationRepository,
        DonationCreationService $donationCreationService,
        APIClient $apiClient,
        DiscourseGroupSyncService $groupSyncService,
        GroupRepository $groupRepository,
        Auth $auth
    ) {
        $this->donationRepository = $donationRepository;
        $this->donationCreationService = $donationCreationService;
        $this->apiClient = $apiClient;
        $this->groupSyncService = $groupSyncService;
        $this->groupRepository = $groupRepository;
        $this->auth = $auth;
    }

    public function getView()
    {
        return view('front.pages.donate.donate');
    }

    public function donate(Request $request)
    {
        $email = $request->get('stripe_email');
        $stripeToken = $request->get('stripe_token');
        $amount = $request->get('stripe_amount_in_cents');

        if ($amount <= 0) {
            abort(401, "Attempted to donate zero dollars");
        }

        $account = $this->auth->user();
        $accountId = $account !== null ? $account->getKey() : null;

        try {
            $donation = $this->donationCreationService->donate($stripeToken, $email, $amount, $accountId);
        } catch (\Stripe\Error\Card $exception) {
            $body = $exception->getJsonBody();
            $message = $body['error']['message'];

            return view('front.pages.donate.donate-error', [
                'message' => $message
            ]);
        } catch (\Stripe\Error\Base $e) {
            app('sentry')->captureException($e);
            return view('front.pages.donate.donate-error', [
                'message' => "There was a problem processing your transaction, please try again later."
            ]);
        }

        // add user to donator group if they're logged in
        if ($account !== null) {
            $group = new GroupEnum(GroupEnum::Donator);
            $donatorGroup = $this->groupRepository->getGroupByName(GroupEnum::Donator);
            $donatorGroupId = $donatorGroup->getKey();
            
            if ($account->groups->contains($donatorGroupId) === false) {
                $request = new DiscourseUserFetchAPIRequest($account->getKey());
                $discourseUser = $this->apiClient->request($request);
                $discourseId = $discourseUser['user']['id'];
    
                $this->groupSyncService->addUserToGroup($discourseId, $account, $group);
            }
        }

        return view('front.pages.donate.donate-thanks', [
            'donation' => $donation,
        ]);
    }
}

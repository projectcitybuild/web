<?php

namespace App\Http\Controllers;

use App\Entities\Donations\Repositories\DonationRepository;
use App\Services\Donations\DonationCreationService;
use App\Services\Donations\DonationStatsService;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard as Auth;
use App\Entities\Groups\GroupEnum;
use App\Library\Discourse\Api\DiscourseUserApi;
use App\Services\Groups\DiscourseGroupSyncService;
use App\Entities\Groups\Repositories\GroupRepository;
use App\Http\WebController;

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
     * @var DiscourseUserApi
     */
    private $discourseUserApi;

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
        DiscourseUserApi $discourseUserApi,
        DiscourseGroupSyncService $groupSyncService,
        GroupRepository $groupRepository,
        Auth $auth
    ) {
        $this->donationRepository = $donationRepository;
        $this->donationCreationService = $donationCreationService;
        $this->discourseUserApi = $discourseUserApi;
        $this->groupSyncService = $groupSyncService;
        $this->groupRepository = $groupRepository;
        $this->auth = $auth;
    }

    public function getView()
    {
        abort(503);
        // return view('front.pages.donate.donate');
    }

    public function donate(Request $request)
    {
        $email = $request->get('stripe_email');
        $stripeToken = $request->get('stripe_token');
        $amount = $request->get('stripe_amount');

        if ($amount <= 0) {
            abort(401, "Attempted to donate zero dollars");
        }
        $amount *= 100;

        $account = $this->auth->user();
        $accountId = $account !== null ? $account->getKey() : null;

        $donation = $this->donationCreationService->donate($stripeToken, $email, $amount, $accountId);

        // add user to donator group if they're logged in
        if ($account !== null) {
            $group = new GroupEnum(GroupEnum::Donator);
            $donatorGroup = $this->groupRepository->getGroupByName(GroupEnum::Donator);
            $donatorGroupId = $donatorGroup->getKey();
            
            if ($account->groups->contains($donatorGroupId) === false) {
                $discourseUser = $this->discourseUserApi->fetchUserByPcbId($account->getKey());
                $discourseId = $discourseUser['user']['id'];
    
                $this->groupSyncService->addUserToGroup($discourseId, $account, $group);
            }
        }

        return view('front.pages.donate.donate-thanks', [
            'donation' => $donation,
        ]);
    }
}

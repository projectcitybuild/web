<?php
namespace App\Services\Donations;

use App\Entities\Eloquent\Donations\Repositories\DonationRepository;
use App\Entities\Requests\Discourse\DiscourseUserFetchAPIRequest;
use App\Library\APIClient\APIClient;
use App\Library\Discourse\Api\DiscourseAdminApi;

class DonationDeactivatorService
{
    /**
     * @var DonationRepository
     */
    private $donationRepository;

    /**
     * @var DiscourseAdminApi
     */
    private $discourseAdminApi;

    /**
     * @var APIClient
     */
    private $apiClient;


    public function __construct(
        DonationRepository $donationRepository,
        DiscourseAdminApi $discourseAdminApi,
        APIClient $apiClient
    ) {
        $this->donationRepository = $donationRepository;
        $this->discourseAdminApi = $discourseAdminApi;
        $this->apiClient = $apiClient;
    }

    public function execute()
    {
        $expiredDonations = $this->donationRepository->getExpiredDonations();
        if (!$expiredDonations) {
            return;
        }

        // get list of unique donator account ids
        $deactivatedDonators = [];
        foreach ($expiredDonations as $expiredDonation) {
            $pcbAccountId = $expiredDonation->account_id;

            if (in_array($pcbAccountId, $deactivatedDonators) === false) {
                $request = new DiscourseUserFetchAPIRequest($pcbAccountId);
                $result = $this->apiClient->request($request);
                
                $discourseAccountId = $result['user']['id'];
                if ($discourseAccountId === null) {
                    throw new \Exception('Failed to get Discourse account id');
                }

                $this->discourseAdminApi->deleteUserFromGroup($discourseAccountId, 46);
                $deactivatedDonators[] = $pcbAccountId;
            }

            $expiredDonation->is_active = false;
            $expiredDonation->save();
        }
    }
}
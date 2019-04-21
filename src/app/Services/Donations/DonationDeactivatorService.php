<?php
namespace App\Services\Donations;

use App\Entities\Donations\Repositories\DonationRepository;
use App\Library\Discourse\Api\DiscourseAdminApi;
use App\Library\Discourse\Api\DiscourseUserApi;

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
     * @var DiscourseUserApi
     */
    private $discourseUserApi;


    public function __construct(DonationRepository $donationRepository,
                                DiscourseAdminApi $discourseAdminApi,
                                DiscourseUserApi $discourseUserApi)
    {
        $this->donationRepository = $donationRepository;
        $this->discourseAdminApi = $discourseAdminApi;
        $this->discourseUserApi = $discourseUserApi;
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
                $result = $this->discourseUserApi->fetchUserByPcbId($pcbAccountId);
                
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
<?php

namespace App\Routes\Http\Web\Controllers;

use Illuminate\Support\Facades\View;
use App\Modules\Donations\Services\DonationStatsService;
use App\Routes\Http\Web\WebController;
use App\Modules\Forums\Services\Retrieve\OfflineRetrieve;

class HomeController extends WebController
{
    /**
     * @var ForumRetrieveInterface
     */
    private $forumRetrieveService;

    /**
     * @var DonationStatsService
     */
    private $donationStatsService;

    
    public function __construct(
        OfflineRetrieve $forumRetrieveService, 
        DonationStatsService $donationStatsService
    ) {
        $this->forumRetrieveService = $forumRetrieveService;
        $this->donationStatsService = $donationStatsService;
    }

    public function getView() {
        $announcements = $this->forumRetrieveService->getAnnouncements([]);
        $donations = $this->donationStatsService->getAnnualPercentageStats();

        return view('home', [
            'announcements' => $announcements,
            'donations'     => $donations,
        ]);
    }
}

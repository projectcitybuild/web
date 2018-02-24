<?php

namespace App\Routes\Http\Web\Controllers;

use Illuminate\Support\Facades\View;
use App\Modules\Donations\Services\DonationStatsService;
use App\Routes\Http\Web\WebController;

class HomeController extends WebController {
    /**
     * @var DonationStatsService
     */
    private $donationStatsService;

    
    public function __construct(DonationStatsService $donationStatsService) {
        $this->donationStatsService = $donationStatsService;
    }

    public function getView() {
        $donations = $this->donationStatsService->getAnnualPercentageStats();

        return view('home', [
            'donations'     => $donations,
        ]);
    }
}

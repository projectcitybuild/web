<?php

namespace App\Routes\Http\Web\Controllers;

use Illuminate\Support\Facades\View;
use App\Modules\Donations\Services\DonationStatsService;
use App\Routes\Http\Web\WebController;
use App\Modules\Players\Models\MinecraftPlayer;
use App\Modules\Accounts\Models\Account;
use Illuminate\Support\Facades\Cache;
use App\Modules\Donations\Repositories\DonationRepository;

class DonationController extends WebController {

    /**
     * @var DonationRepository
     */
    private $donationRepository;


    public function __construct(DonationRepository $donationRepository) {
        $this->donationRepository = $donationRepository;
    }

    private function getRgbBetween($rgbStart, $rgbEnd, $percent) {
        $w = $percent * 2 - 1;

        $w1 = ($w + 1) / 2.0;
        $w2 = 1 - $w1;

        return [
            round($rgbStart[0] * $w1 + $rgbEnd[0] * $w2),
            round($rgbStart[1] * $w1 + $rgbEnd[1] * $w2),
            round($rgbStart[2] * $w1 + $rgbEnd[2] * $w2),
        ];
    }

    public function getView() {
        $donations = $this->donationRepository->getAll();

        $lastYear = date('Y') - 1;

        $thisYearSum    = $this->donationRepository->getAnnualSum();
        $thisYearAvg    = $this->donationRepository->getAnnualAverage();
        $thisYearCount  = $this->donationRepository->getAnnualCount();
        $lastYearSum    = $this->donationRepository->getAnnualSum($lastYear);
        $lastYearAvg    = $this->donationRepository->getAnnualAverage($lastYear);
        $lastYearCount  = $this->donationRepository->getAnnualCount($lastYear);

        $colorBad = [136, 223, 36];
        $colorGood = [255, 90, 61];

        // shows the 'bad' color until the given amount is reached
        $colorBadThreshold = 400;
        
        $colorScale = floor($thisYearSum - $colorBadThreshold) / (1000 - $colorBadThreshold);
        $color = $this->getRgbBetween($colorBad, $colorGood, $colorScale);

        return view('donation-list', [
            'donations'     => $donations,
            'thisYearSum'   => $thisYearSum,
            'thisYearAvg'   => $thisYearAvg,
            'thisYearCount' => $thisYearCount,
            'lastYearSum'   => $lastYearSum,
            'lastYearAvg'   => $lastYearAvg,
            'lastYearCount' => $lastYearCount,
            'figureColor'   => implode(',', $color),
        ]);
    }
}

<?php

namespace Interfaces\Web\Controllers;

use Domains\Modules\Donations\Repositories\DonationRepository;
use Domains\Services\Donations\DonationCreationService;
use Domains\Services\Donations\DonationStatsService;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;

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


    public function __construct(DonationRepository $donationRepository,
                                DonationCreationService $donationCreationService)
    {
        $this->donationRepository = $donationRepository;
        $this->donationCreationService = $donationCreationService;
    }

    public function getView()
    {
        return view('front.pages.donate');
    }

    public function donate(Request $request)
    {
        $email = $request->get('stripeEmail');
        $stripeToken = $request->get('stripeToken');
        $amount = 500;

        if ($amount <= 0) {
            return redirect()->back();
        }

        $account = $request->user();
        $accountId = $account !== null ? $account->getKey() : null;

        $this->donationCreationService->donate($stripeToken, $email, $amount, $accountId);
    }

    private function getRgbBetween($rgbStart, $rgbEnd, $percent)
    {
        $w = $percent * 2 - 1;

        $w1 = ($w + 1) / 2.0;
        $w2 = 1 - $w1;

        return [
            round($rgbStart[0] * $w1 + $rgbEnd[0] * $w2),
            round($rgbStart[1] * $w1 + $rgbEnd[1] * $w2),
            round($rgbStart[2] * $w1 + $rgbEnd[2] * $w2),
        ];
    }

    public function getListView()
    {
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

        return view('front.pages.donation-list', [
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

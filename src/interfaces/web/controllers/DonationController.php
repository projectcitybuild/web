<?php

namespace Interfaces\Web\Controllers;

use Illuminate\Support\Facades\View;
use Domains\Modules\Donations\Services\DonationStatsService;
use Domains\Modules\Players\Models\MinecraftPlayer;
use Domains\Modules\Accounts\Models\Account;
use Illuminate\Support\Facades\Cache;
use Domains\Modules\Donations\Repositories\DonationRepository;
use Illuminate\Http\Request;
use Domains\Library\Stripe\StripeHandler;
use Domains\Modules\Payments\Repositories\AccountPaymentRepository;
use Illuminate\Database\Connection;
use Domains\Modules\Payments\AccountPaymentType;

class DonationController extends WebController
{
    /**
     * @var DonationRepository
     */
    private $donationRepository;

    /**
     * @var AccountPaymentRepository
     */
    private $paymentRepository;

    /**
     * @var Connection
     */
    private $connection;


    public function __construct(DonationRepository $donationRepository,
                                AccountPaymentRepository $paymentRepository,
                                Connection $connection)
    {
        $this->donationRepository = $donationRepository;
        $this->paymentRepository = $paymentRepository;
        $this->connection = $connection;
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

        $account = $request->user();
        $accountId = $account !== null ? $account->getKey() : null;
        
        $stripeHandler = new StripeHandler();

        try {
            $charge = $stripeHandler->charge($amount, $stripeToken, $accountId, $email, 'PCB Contribution');
        } catch (\Exception $e) {
            throw $e;
        }

        $amount = (float)($amount / 100);

        $isLifetime = $amount >= 30;
        if ($isLifetime) {
            $donationExpiry = null;
        } else {
            $donationExpiry = now()->addMonths(floor($amount / 3));
        }

        $this->connection->beginTransaction();
        try {
            $donation = $this->donationRepository->create($accountId,
                                                          $amount,
                                                          $donationExpiry,
                                                          $isLifetime);

            $payment = $this->paymentRepository->create(new AccountPaymentType(AccountPaymentType::Donation),
                                                        $donation->getKey(),
                                                        $amount / 100,
                                                        $stripeToken,
                                                        $accountId,
                                                        true);
            $this->connection->commit();
            
        } catch (\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }

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

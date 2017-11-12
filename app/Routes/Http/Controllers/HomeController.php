<?php

namespace App\Routes\Http\Controllers;

use Illuminate\Support\Facades\View;
use App\Modules\Forums\Repositories\ForumActivityRepository;
use App\Modules\Donations\Repositories\DonationRepository;
use App\Modules\Forums\Services\SMF\Smf;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * @var ForumActivityRepository
     */
    private $activityRepository;

    /**
     * @var DonationRepository
     */
    private $donationRepository;

    public function __construct(ForumActivityRepository $activityRepository, DonationRepository $donationRepository) {
        $this->activityRepository = $activityRepository;
        $this->donationRepository = $donationRepository;
    }

    public function getView(Smf $smf) {
        $user = $smf->getUser();
        $groups = $user->getUserGroupsFromDatabase();

        $announcements  = $this->activityRepository->getRecentTopicsByBoardId($groups, 2, 3);
        $recentActivity = $this->activityRepository->getRecentPostsGroupedByTopic($groups);

        $donations = $this->donationRepository->getAnnualSum();
        $lastDayOfYear = new Carbon('last day of december');
        $now = Carbon::now();
        $percentage = round(($donations / 1000) * 100);

        return view('home', [
            'announcements'     => $announcements,
            'recentActivity'    => $recentActivity,
            'donations' => [
                'total'         => $donations ?: 0,
                'remainingDays' => $lastDayOfYear->diff($now)->days,
                'percentage'    => max(3, $percentage) ?: 0,
            ],
        ]);
    }
}

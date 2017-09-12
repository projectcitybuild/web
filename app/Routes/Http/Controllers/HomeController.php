<?php

namespace App\Routes\Http\Controllers;

use Illuminate\Support\Facades\View;
use App\Modules\Forums\Repositories\ForumActivityRepository;
use App\Modules\Forums\Services\SMF\Smf;

class HomeController extends Controller
{
    private $repository;

    public function __construct(ForumActivityRepository $repository) {
        $this->repository = $repository;
    }

    public function getView(Smf $smf) {
        $user = $smf->getUser();
        $groups = $user->getUserGroupsFromDatabase()
            ->pluck('id_group')
            ->toArray();

        $announcements = $this->repository->getRecentTopicsByBoardId($groups, 2, 3);
        $recentActivity = $this->repository->getRecentPostsGroupedByTopic($groups);

        // dd($recentActivity->toArray());

        return view('home', [
            'announcements' => $announcements,
        ]);
    }
}

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

    public function getView(Smf $smf, \App\Modules\Servers\Services\Querying\ServerQueryService $test) {
        $user = $smf->getUser();
        $groups = $user->getUserGroupsFromDatabase();

        $announcements = $this->repository->getRecentTopicsByBoardId($groups, 2, 3);
        $recentActivity = $this->repository->getRecentPostsGroupedByTopic($groups);

        $test->queryAllServers();        

        return view('home', [
            'announcements'     => $announcements,
            'recentActivity'    => $recentActivity,
        ]);
    }
}

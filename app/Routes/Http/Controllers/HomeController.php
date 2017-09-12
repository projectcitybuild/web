<?php

namespace App\Routes\Http\Controllers;

use Illuminate\Support\Facades\View;
use App\Modules\Forums\Repositories\ForumActivityRepository;

class HomeController extends Controller
{
    protected $repository;

    public function __construct(ForumActivityRepository $repository) {
        $this->repository = $repository;
    }

    protected function getView() {
        $announcements = $this->repository->getRecentTopicsByBoardId(2, 3);
        $recentActivity = $this->repository->getRecentPostsGroupedByTopic();

        // dd($recentActivity->toArray());

        return view('home', [
            'announcements' => $announcements,
        ]);
    }
}

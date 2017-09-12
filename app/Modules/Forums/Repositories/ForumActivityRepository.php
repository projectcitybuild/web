<?php
namespace App\Modules\Forums\Repositories;

use App\Modules\Forums\Models\{ForumTopic, ForumPost};
use Illuminate\Support\Collection;
use DB;

class ForumActivityRepository {

    private $topicModel;
    private $postModel;

    public function __construct(ForumTopic $topicModel, ForumPost $postModel) {
        $this->topicModel = $topicModel;
        $this->postModel = $postModel;
    }


    /**
     * Returns a collection of the most recent ForumTopic from the given board
     *
     * @param array $groups     Array of group ids the current user belongs to
     * @param int $boardId      Board id to fetch topics from
     * @param int $take         Number of ForumTopic to return
     * @return Collection
     */
    public function getRecentTopicsByBoardId(array $groups, int $boardId, int $take = 5) : Collection {
        return $this->topicModel
            ->where('id_board', $boardId)
            ->with(['firstPost', 'poster'])
            ->orderBy('id_topic', 'DESC')
            ->take( min($take, 10) )
            ->get();
    }

    /**
     * Returns a collection of the most recent topics posted in.
     *
     * @param array $groups     Array of group ids the current user belongs to
     * @param int $take         Number of ForumTopic to return
     * @return Collection
     */
    public function getRecentPostsGroupedByTopic(array $groups, int $take = 10) : Collection {
        return $this->postModel
            // ->select( DB::raw('id_topic, *') )
            // ->groupBy('id_topic')
            ->orderBy('id_topic', 'DESC')
            ->take( min($take, 10) )
            ->get();
    }

}
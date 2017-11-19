<?php
namespace App\Modules\Forums\Services\Retrieve;

use App\Modules\Forums\Repositories\ForumActivityRepository;
use Illuminate\Cache\Repository as Cache;

class DatabaseRetrieve implements ForumRetrieveInterface {

    /**
     * @var ForumActivityRepository
     */
    private $activityRepository;

    /**
     * @var Cache
     */
    private $cache;

    public function __construct(ForumActivityRepository $activityRepository, Cache $cache) {
        $this->activityRepository = $activityRepository;
        $this->cache = $cache;
    }

    public function getRecentActivity(array $groups = []) {
        // TODO: change cache expiry
        return $this->cache->remember('forum-activity', 360, function() use($groups) {
            return $this->activityRepository->getRecentPostsGroupedByTopic($groups);
        });
    }

    public function getAnnouncements(array $groups = []) {
        // TODO: change cache expiry
        return $this->cache->remember('forum-announcements', 360, function() use($groups) {
            return $this->activityRepository->getRecentTopicsByBoardId($groups, 2, 3);
        });
    }

}
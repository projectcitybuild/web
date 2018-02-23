<?php
namespace App\Modules\Forums\Services\Retrieve;

use App\Modules\Forums\Models\ForumTopic;
use App\Modules\Forums\Models\ForumPost;
use App\Modules\Forums\Models\ForumUser;
use Illuminate\Contracts\Filesystem\Filesystem;
use \Illuminate\Support\Collection;

/**
 * Implementation that returns fake data instead of hitting the repository.
 * For use with offline dev since only the live server will have the forum database credentials
 */
class OfflineRetrieve {

    /**
     * @var Filesystem
     */
    private $storage;

    public function __construct(Filesystem $storage) {
        $this->storage = $storage;
    }

    public function getRecentActivity(array $groups = []) {
        return collect();
    }

    public function getAnnouncements(array $groups = []) {
        $announcement1 = ForumTopic::make([
            'num_views' => 1329,
            'num_replies' => 14,
        ]);

        $post1 = ForumPost::make([
            'poster_time' => 1516633766,
            'subject' => 'A test announcement thread',
            'body' => 'This is a test topic that can also utilises [b]bbcode[/b].'
        ]);

        $poster1 = ForumUser::make([
            'real_name' => '_andy',
        ]);

        $announcement1->setRelation('firstPost', $post1);
        $announcement1->setRelation('poster', $poster1);

        $announcements = [$announcement1, $announcement1, $announcement1];
        return $announcements;
    }

}
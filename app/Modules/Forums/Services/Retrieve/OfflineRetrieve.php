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
class OfflineRetrieve implements ForumRetrieveInterface {

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
        return collect();
    }

}
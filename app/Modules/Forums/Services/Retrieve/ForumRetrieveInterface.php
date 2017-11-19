<?php
namespace App\Modules\Forums\Services\Retrieve;

interface ForumRetrieveInterface {

    public function getRecentActivity(array $groups = []);

    public function getAnnouncements(array $groups = []);

}
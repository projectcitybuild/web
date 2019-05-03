<?php

namespace App\Http\Controllers\Api;

use App\Http\ApiController;
use App\Entities\Groups\Repositories\GroupRepository;
use App\Entities\Groups\Resources\GroupResource;

final class GroupApiController extends ApiController
{
    /**
     * @var GroupRepository
     */
    private $groupRepository;

    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    public function getAll()
    {
        $groups = $this->groupRepository->getAll();
        $response = GroupResource::collection($groups);

        return $response;
    }
}

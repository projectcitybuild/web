<?php

namespace Interfaces\Api\Controllers;

use Interfaces\Api\ApiController;
use Entities\Groups\Repositories\GroupRepository;
use Entities\Groups\Resources\GroupResource;

class GroupApiController extends ApiController
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

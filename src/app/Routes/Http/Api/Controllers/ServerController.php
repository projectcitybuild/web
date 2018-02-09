<?php

namespace App\Routes\Api\Controllers;

use Illuminate\Http\Request;
use App\Modules\Servers\Repositories\ServerRepository;
use App\Modules\Servers\Repositories\ServerCategoryRepository;
use App\Modules\Servers\Transformers\ServerCategoryResource;
use App\Routes\Api\ApiController;

class ServerController extends ApiController {

    /**
     * @var ServerRepository
     */
    private $serverRepository;

    /**
     * @var ServerCategoryRepository
     */
    private $serverCategoryRepository;

    public function __construct(ServerRepository $serverRepository, ServerCategoryRepository $serverCategoryRepository) {
        $this->serverRepository = $serverRepository;
        $this->serverCategoryRepository = $serverCategoryRepository;
    }

    public function getAllServers(Request $request) {
        $categories = $this->serverCategoryRepository
            ->getAll('servers.latestStatus');

        return [
            'status_code' => 200,
            'data' => ServerCategoryResource::collection($categories),
        ];
    }
}

<?php

namespace App\Routes\Api\Controllers;

use Illuminate\Http\Request;
use App\Modules\Servers\Repositories\ServerRepository;
use App\Modules\Servers\Repositories\ServerCategoryRepository;

class ServerController extends Controller {

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
            ->getAll('servers');

        return [
            'status_code' => 200,
            'data' => $categories,
        ];
    }
}

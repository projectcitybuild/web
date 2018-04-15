<?php
namespace App\Routes\Http\Web\ViewComposers;

use Illuminate\View\View;
use App\Repositories\UserRepository;
use App\Modules\Servers\Repositories\ServerCategoryRepository;

class MasterViewComposer {

    private $serverCategoryRepository;
    
    public function __construct(ServerCategoryRepository $serverCategoryRepository) {
        $this->serverCategoryRepository = $serverCategoryRepository;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view) {
        $servers = $this->serverCategoryRepository->getAllVisible();
        $view->with('serverCategories', $servers);
    }
}
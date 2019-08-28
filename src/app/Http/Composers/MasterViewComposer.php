<?php

namespace App\Http\Composers;

use Illuminate\View\View;
use App\Entities\Eloquent\Servers\Repositories\ServerCategoryRepositoryContract;

final class MasterViewComposer
{
    /**
     * @var ServerCategoryRepositoryContract
     */
    private $serverCategoryRepository;
    
    
    public function __construct(ServerCategoryRepositoryContract $serverCategoryRepository)
    {
        $this->serverCategoryRepository = $serverCategoryRepository;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $servers = $this->serverCategoryRepository->allVisible();
        $view->with('serverCategories', $servers);
    }
}

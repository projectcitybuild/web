<?php

namespace App\Http\Composers;

use App\Entities\Servers\Repositories\ServerCategoryRepositoryContract;
use Illuminate\View\View;

final class MasterViewComposer
{
    private ServerCategoryRepositoryContract $serverCategoryRepository;

    public function __construct(ServerCategoryRepositoryContract $serverCategoryRepository)
    {
        $this->serverCategoryRepository = $serverCategoryRepository;
    }

    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $servers = $this->serverCategoryRepository->allVisible();

        $view->with('serverCategories', $servers);
    }
}

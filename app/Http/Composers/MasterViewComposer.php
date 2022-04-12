<?php

namespace App\Http\Composers;

use App\Entities\Repositories\ServerCategoryRepository;
use Illuminate\View\View;

/**
 * @deprecated
 */
final class MasterViewComposer
{
    public function __construct(
        private ServerCategoryRepository $serverCategoryRepository
    ) {}

    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $servers = $this->serverCategoryRepository->allVisible();

        $view->with('serverCategories', $servers);
    }
}

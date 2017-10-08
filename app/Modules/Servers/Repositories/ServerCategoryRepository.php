<?php
namespace App\Modules\Servers\Repositories;

use App\Modules\Servers\Models\ServerCategory;

class ServerCategoryRepository {

    private $categoryModel;

    public function __construct(ServerCategory $categoryModel) {
        $this->categoryModel = $categoryModel;
    }
    
    /**
     * Gets a collection of all server categories
     *
     * @return ServerCategory
     */
    public function getAll($with = null) {
        return $this->categoryModel
            ->when($with, function($q) use($with) {
                $q->with($with);
            })
            ->get();
    }

}
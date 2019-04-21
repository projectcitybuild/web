<?php
namespace Entities\Servers\Repositories;

use Entities\Servers\Models\ServerCategory;

class ServerCategoryRepository
{
    private $categoryModel;

    public function __construct(ServerCategory $categoryModel)
    {
        $this->categoryModel = $categoryModel;
    }
    
    /**
     * Gets a collection of all server categories
     *
     * @return ServerCategory
     */
    public function getAll(array $with = [])
    {
        return $this->categoryModel
            ->with($with)
            ->get();
    }

    public function getAllVisible(array $with = [])
    {
        return $this->categoryModel
            ->with(['servers' => function ($q) {
                $q->where('is_visible', true)
                  ->with('status');
            }])
            ->get();
    }
}

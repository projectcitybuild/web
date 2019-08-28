<?php

namespace App\Entities\Eloquent\Servers\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface ServerCategoryRepositoryContract 
{
    function all(array $with = []) : Collection;
    function allVisible(array $with = []) : Collection;
}
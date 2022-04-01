<?php

namespace App\Entities\Repositories;

use App\Entities\Models\Eloquent\ServerKey;
use App\Repository;

/**
 * @deprecated Use ServerKey model facade instead
 */
class ServerKeyRepository extends Repository
{
    protected $model = ServerKey::class;

    public function getByToken(string $token): ?ServerKey
    {
        return $this->getModel()
            ->where('token', $token)
            ->first();
    }
}

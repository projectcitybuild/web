<?php
namespace App\Entities\Eloquent\ServerKeys\Repositories;

use App\Entities\Eloquent\ServerKeys\Models\ServerKey;
use App\Repository;

class ServerKeyRepository extends Repository
{
    protected $model = ServerKey::class;

    public function getByToken(string $token) : ?ServerKey
    {
        return $this->getModel()
            ->where('token', $token)
            ->first();
    }    
}

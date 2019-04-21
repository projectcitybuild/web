<?php
namespace App\Entities\ServerKeys\Repositories;

use App\Entities\ServerKeys\Models\ServerKey;
use Application\Contracts\Repository;

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

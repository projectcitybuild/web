<?php
namespace Entities\ServerKeys\Repositories;

use Entities\ServerKeys\Models\ServerKey;
use Application\Repository;

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

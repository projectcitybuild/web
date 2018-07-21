<?php
namespace Application\Modules\ServerKeys\Repositories;

use Application\Modules\ServerKeys\Models\ServerKey;

class ServerKeyRepository
{
    private $keyModel;

    public function __construct(ServerKey $keyModel)
    {
        $this->keyModel = $keyModel;
    }

    public function getById(int $keyId) : ?ServerKey
    {
        return $this->keyModel->find($keyId);
    }
}

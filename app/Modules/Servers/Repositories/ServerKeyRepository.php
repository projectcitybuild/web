<?php
namespace App\Modules\Servers\Repositories;

use App\Modules\Servers\Models\ServerKey;

class ServerKeyRepository {

    private $keyModel;

    public function __construct(ServerKey $keyModel) {
        $this->keyModel = $keyModel;
    }

    public function getById(int $keyId) : ?ServerKey {
        return $this->keyModel->find($keyId);
    }

}
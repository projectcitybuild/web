<?php
namespace App\Modules\ServerKeys\Repositories;

use App\Modules\ServerKeys\Models\ServerKeyToken;

class ServerKeyTokenRepository {

    private $keyTokenModel;

    public function __construct(ServerKeyToken $keyTokenModel) {
        $this->keyTokenModel = $keyTokenModel;
    }

    /**
     * Generates a new server key token and returns it
     *
     * @param int $serverKeyId  ServerKey id
     * @return ServerKeyToken
     */
    public function generateToken(int $serverKeyId) : ServerKeyToken {
        $token = bin2hex(random_bytes(30));
        return $this->keyTokenModel->create([
            'server_key_id'     => $serverKeyId,
            'token_hash'        => $token,
            'is_blacklisted'    => false,
        ]);
    }

    public function getByToken(string $token) : ?ServerKeyToken {
        return $this->keyTokenModel
            ->where('token_hash', $token)
            ->first();
    }

}
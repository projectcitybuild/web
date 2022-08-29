<?php

namespace Repositories;

use Entities\Models\Eloquent\ServerKey;

/**
 * @final
 *
 * @deprecated
 */
class ServerKeyRepository
{
    public function getByToken(string $token): ?ServerKey
    {
        return ServerKey::where('token', $token)->first();
    }
}

<?php
namespace App\Modules\Bans\Services;

use App\Modules\Servers\Models\ServerKey;
use App\Modules\Bans\Exceptions\UnauthorisedKeyActionException;
use App\Modules\Bans\Models\GameBan;

class BanAuthorisationService {

    public function validateBan(bool $isGlobalBan, ServerKey $serverKey) {
        if($isGlobalBan && !$serverKey->can_global_ban) {
            throw new UnauthorisedKeyActionException('This key does not have permission to create a global ban');
        }
        if(!$isGlobalBan && !$serverKey->can_local_ban) {
            throw new UnauthorisedKeyActionException('This key does not have permission to create a local ban');
        }
        return true;
    }

    public function validateUnban(GameBan $ban, ServerKey $serverKey) {
        if($ban->server_id === $serverKey->server_id) {
            // can remove local bans or global bans from this server?
            if(!$serverKey->can_local_ban) {
                throw new UnauthorisedKeyActionException('This key does not have permission to remove local bans');
            }
        } else {
            // can remove global ban from other servers?
            if($ban->is_global_ban && !$serverKey->can_global_ban) {
                throw new UnauthorisedKeyActionException('This key does not have permission to remove global bans');
            }
        }
    }
}
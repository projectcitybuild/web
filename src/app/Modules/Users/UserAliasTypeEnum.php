<?php
namespace App\Modules\Users;

use App\Shared\Enum;

class UserAliasTypeEnum extends Enum {
    const MINECRAFT_UUID = 1;
    const MINECRAFT_NAME = 2;
    const STEAM_ID = 3;
}
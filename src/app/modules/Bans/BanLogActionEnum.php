<?php
namespace App\Modules\Bans;

use App\Support\Enum;

class BanLogActionEnum extends Enum
{
    const CREATE_BAN = 1;
    const CREATE_UNBAN = 2;
}

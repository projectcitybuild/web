<?php
namespace Domains\Modules\Bans;

use Application\LegacyEnum;

class BanLogActionEnum extends LegacyEnum
{
    const CREATE_BAN = 1;
    const CREATE_UNBAN = 2;
}

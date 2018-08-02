<?php
namespace Domains\Modules\Bans;

use Infrastructure\LegacyEnum;

class BanLogActionEnum extends LegacyEnum
{
    const CREATE_BAN = 1;
    const CREATE_UNBAN = 2;
}

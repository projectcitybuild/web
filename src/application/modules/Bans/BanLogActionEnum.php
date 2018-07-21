<?php
namespace Application\Modules\Bans;

use Infrastructure\Enum;

class BanLogActionEnum extends Enum
{
    const CREATE_BAN = 1;
    const CREATE_UNBAN = 2;
}

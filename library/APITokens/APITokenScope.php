<?php

namespace Library\APITokens;

use Helpers\ValueJoinable;

enum APITokenScope: string
{
    use ValueJoinable;

//    case BAN_UPDATE = 'ban:update';
//    case BAN_LOOKUP = 'ban:lookup';

    case ACCOUNT_BALANCE_SHOW = 'balance:show';
    case ACCOUNT_BALANCE_DEDUCT = 'balance:deduct';
}

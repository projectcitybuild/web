<?php

namespace App\Domains\ServerTokens;

use App\Core\Utilities\Traits\ValueJoinable;

enum ScopeKey: string
{
    use ValueJoinable;

    case BAN_UPDATE = 'ban:update';
    case BAN_LOOKUP = 'ban:lookup';

    case WARNING_UPDATE = 'warning:update';
    case WARNING_LOOKUP = 'warning:lookup';

    case ACCOUNT_BALANCE_SHOW = 'balance:show';
    case ACCOUNT_BALANCE_DEDUCT = 'balance:deduct';

    case SHOWCASE_WARPS_SHOW = 'showcase-warps:show';
    case SHOWCASE_WARPS_UPDATE = 'showcase-warps:update';

    case TELEMETRY = 'telemetry:all';
}
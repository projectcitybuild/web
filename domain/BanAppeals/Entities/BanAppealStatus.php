<?php

namespace Domain\BanAppeals\Entities;

enum BanAppealStatus: int
{
    case PENDING = 0;
    case ACCEPTED_UNBAN = 1;
    case ACCEPTED_TEMPBAN = 2;
    case DENIED = 3;
}

<?php

namespace App\Domains\Bans\Exceptions;

use App\Core\Data\Exceptions\PredefinedHttpException;

final class NotBannedException extends PredefinedHttpException
{
    protected string $id = 'player_not_banned';
    protected string $errorMessage = 'This player is not currently banned';
    protected int $status = 400;
}

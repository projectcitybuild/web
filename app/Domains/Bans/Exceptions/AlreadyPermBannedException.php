<?php

namespace App\Domains\Bans\Exceptions;

use App\Core\Data\Exceptions\PredefinedHttpException;

final class AlreadyPermBannedException extends PredefinedHttpException
{
    protected string $id = 'player_already_banned';
    protected string $errorMessage = 'Player is already permanently banned';
    protected int $status = 400;
}

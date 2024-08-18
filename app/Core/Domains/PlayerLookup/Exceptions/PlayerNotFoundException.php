<?php

namespace App\Core\Domains\PlayerLookup\Exceptions;

use App\Core\Data\Exceptions\PredefinedHttpException;

final class PlayerNotFoundException extends PredefinedHttpException
{
    protected string $id = 'player_not_found';
    protected string $errorMessage = 'Cannot find this player';
    protected int $status = 404;
}

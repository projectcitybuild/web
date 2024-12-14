<?php

namespace App\Domains\Manage\Data;

enum PanelGroupScope: string
{
    case ACCESS_PANEL = 'access-panel';

    public function toMiddleware(): string
    {
        return 'scope:'.$this->value;
    }
}

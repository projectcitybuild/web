<?php

namespace App\Domains\Manage\Data;

enum PanelGroupScope: string
{
    case ACCESS_PANEL = 'access-panel';

    case VIEW_ACTIVITY = 'panel-view-activity';

    public function toMiddleware(): string
    {
        return 'scope:'.$this->value;
    }
}

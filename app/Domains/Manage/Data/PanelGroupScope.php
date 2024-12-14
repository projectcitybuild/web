<?php

namespace App\Domains\Manage\Data;

enum PanelGroupScope: string
{
    case ACCESS_PANEL = 'access-panel';

    case MANAGE_SHOWCASE_WARPS = 'panel-manage-showcase-warps';

    case VIEW_ACTIVITY = 'panel-view-activity';

    case REVIEW_APPEALS = 'panel-review-appeals';
    case REVIEW_BUILD_RANK_APPS = 'panel-review-build-rank-app';

    public function toMiddleware(): string
    {
        return 'scope:'.$this->value;
    }
}

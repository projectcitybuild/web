<?php

namespace Entities\Models;

enum PanelGroupScope: string
{
    case ACCESS_PANEL = 'access-panel';

    case MANAGE_ACCOUNTS = 'panel-manage-accounts';
    case MANAGE_GROUPS = 'panel-manage-groups';
    case MANAGE_PAGES = 'panel-manage-pages';
    case MANAGE_SERVERS = 'panel-manage-servers';
    case MANAGE_DONATIONS = 'panel-manage-donations';

    case REVIEW_APPEALS = 'panel-review-appeals';
    case REVIEW_BUILD_RANK_APPS = 'panel-review-build-rank-app';

    public function toMiddleware(): string
    {
        return 'can:'.$this->value;
    }
}
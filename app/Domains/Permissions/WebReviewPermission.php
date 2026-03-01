<?php

namespace App\Domains\Permissions;

enum WebReviewPermission: string
{
    case BAN_APPEALS_VIEW = 'web.review.ban_appeals.view';
    case BAN_APPEALS_DECIDE = 'web.review.ban_appeals.decide';
    case BUILD_RANK_APPS_VIEW = 'web.review.build_rank_apps.view';
    case BUILD_RANK_APPS_DECIDE = 'web.review.build_rank_apps.decide';
}

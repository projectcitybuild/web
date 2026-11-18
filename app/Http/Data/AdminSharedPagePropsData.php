<?php

namespace App\Http\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class AdminSharedPagePropsData extends Data
{
    /**
     * @param  list<string>|null  $permissions
     */
    public function __construct(
        public Optional|AdminAccountSummaryData|null $account,
        public Optional|string|null $success,
        public Optional|int|null $pending_appeals,
        public Optional|int|null $pending_build_rank_apps,
        public bool $is_admin,
        public Optional|array|null $permissions,
    ) {}
}

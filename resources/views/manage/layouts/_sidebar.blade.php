<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->is('manage') ? 'active' : '' }}" aria-current="page"
                   href="{{ route('manage.index') }}">
                    <i class="fas fa-home fa-fw"></i>
                    Home
                </a>
            </li>
        </ul>

        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Users</span>
        </h6>
        <ul class="nav flex-column mb-2">
            @scope(App\Domains\Manage\Data\PanelGroupScope::MANAGE_ACCOUNTS)
            <li class="nav-item">
                <a class="nav-link {{ request()->is('manage/accounts*') ? 'active' : '' }}"
                   href="{{ route('manage.accounts.index') }}">
                    <i class="fas fa-users fa-fw"></i>
                    Accounts
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('manage/minecraft-players*') ? 'active' : '' }} "
                   href="{{ route('manage.minecraft-players.index') }}">
                    <i class="fas fa-cube fa-fw"></i>
                    Minecraft Players
                </a>
            </li>
            @endscope
            @can('viewAny', App\Models\Group::class)
            <li class="nav-item">
                <a class="nav-link {{ request()->is('manage/groups*') ? 'active' : '' }} "
                   href="{{ route('manage.groups.index') }}">
                    <i class="fas fa-users fa-fw"></i>
                    Groups
                </a>
            </li>
            @endcan
            @can('viewAny', App\Models\Badge::class)
            <li class="nav-item">
                <a class="nav-link {{ request()->is('manage/badges*') ? 'active' : '' }}"
                   href="{{ route('manage.badges.index') }}">
                    <i class="fas fa-medal fa-fw"></i>
                    Badges
                </a>
            </li>
            @endcan
            @scope(App\Domains\Manage\Data\PanelGroupScope::VIEW_ACTIVITY)
            <li class="nav-item">
                <a class="nav-link {{ request()->is('manage/activity*') ? 'active' : '' }}"
                   href="{{ route('manage.activity.index') }}">
                    <i class="fas fa-binoculars fa-fw"></i>
                    Activity
                </a>
            </li>
            @endscope
        </ul>

        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Infractions</span>
        </h6>
        <ul class="nav flex-column mb-2">
            @scope(App\Domains\Manage\Data\PanelGroupScope::MANAGE_WARNINGS)
            <li class="nav-item">
                <a class="nav-link {{ request()->is('manage/warnings*') ? 'active' : '' }}"
                   href="{{ route('manage.warnings.index') }}">
                    <i class="fas fa-exclamation-triangle fa-fw"></i>
                    Warnings
                </a>
            </li>
            @endscope
            @scope(App\Domains\Manage\Data\PanelGroupScope::MANAGE_BANS)
            <li class="nav-item">
                <a class="nav-link {{ request()->is('manage/player-bans*') ? 'active' : '' }}"
                   href="{{ route('manage.player-bans.index') }}">
                    <i class="fas fa-user-slash fa-fw"></i>
                    Banned Players
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('manage/ip-bans*') ? 'active' : '' }}"
                   href="{{ route('manage.ip-bans.index') }}">
                    <i class="fas fa-ban fa-fw"></i>
                    Banned IPs
                </a>
            </li>
            @endscope
        </ul>

        @scope(App\Domains\Manage\Data\PanelGroupScope::MANAGE_SHOWCASE_WARPS)
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Minecraft</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link {{ request()->is('manage/minecraft/warps*') ? 'active' : '' }} "
                   href="{{ route('manage.minecraft.warps.index') }}">
                    <i class="fas fa-flag fa-fw"></i>
                    Warps
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('manage/showcase-warps*') ? 'active' : '' }} "
                   href="{{ route('manage.showcase-warps.index') }}">
                    <i class="fas fa-flag fa-fw"></i>
                    Showcase Warps
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('manage/minecraft/config*') ? 'active' : '' }} "
                   href="{{ route('manage.minecraft.config.create') }}">
                    <i class="fas fa-flag fa-fw"></i>
                    Config
                </a>
            </li>
        </ul>
        @endscope

        @can('viewAny', App\Models\Server::class)
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Servers</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link {{ request()->is('manage/servers*') ? 'active' : '' }} "
                   href="{{ route('manage.servers.index') }}">
                    <i class="fas fa-server fa-fw"></i>
                    Servers
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('manage/server-tokens*') ? 'active' : '' }} "
                   href="{{ route('manage.server-tokens.index') }}">
                    <i class="fas fa-key fa-fw"></i>
                    Server Tokens
                </a>
            </li>
        </ul>
        @endcan

        @can('viewAny', App\Models\Donation::class)
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Transactions</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link {{ request()->is('manage/donations*') ? 'active' : '' }}"
                   href="{{ route('manage.donations.index') }}">
                    <i class="fas fa-credit-card fa-fw"></i>
                    Donations
                </a>
            </li>
        </ul>
        @endcan

        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Review</span>
        </h6>
        <ul class="nav flex-column mb-2">
            @scope(App\Domains\Manage\Data\PanelGroupScope::REVIEW_BUILD_RANK_APPS)
            <li class="nav-item">
                <a class="nav-link {{ request()->is('manage/builder-ranks*') ? 'active' : '' }}"
                   href="{{ route('manage.builder-ranks.index') }}">
                    <i class="fas fa-hammer fa-fw"></i>
                    Builder Rank Applications
                    @if ($outgoing_rank_apps > 0)
                        <span class="badge bg-danger">{{ $outgoing_rank_apps }}</span>
                    @endif
                </a>
            </li>
            @endscope
            @scope(App\Domains\Manage\Data\PanelGroupScope::REVIEW_APPEALS)
            <li class="nav-item">
                <a class="nav-link {{ request()->is('manage/ban-appeals*') ? 'active' : '' }}"
                   href="{{ route('manage.ban-appeals.index') }}">
                    <i class="fas fa-gavel fa-fw"></i>
                    Ban Appeals
                    @if ($outstanding_ban_appeals > 0)
                        <span class="badge bg-danger">{{ $outstanding_ban_appeals }}</span>
                    @endif
                </a>
            </li>
            @endscope
        </ul>
    </div>
</nav>

<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->is('panel') ? 'active' : '' }}" aria-current="page"
                   href="{{ route('front.panel.index') }}">
                    <i class="fas fa-home fa-fw"></i>
                    Home
                </a>
            </li>
        </ul>

        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Users</span>
        </h6>
        <ul class="nav flex-column mb-2">
            @scope(Entities\Models\PanelGroupScope::MANAGE_ACCOUNTS)
            <li class="nav-item">
                <a class="nav-link {{ request()->is('panel/accounts*') ? 'active' : '' }}"
                   href="{{ route('front.panel.accounts.index') }}">
                    <i class="fas fa-users fa-fw"></i>
                    Accounts
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('panel/minecraft-players*') ? 'active' : '' }} "
                   href="{{ route('front.panel.minecraft-players.index') }}">
                    <i class="fas fa-cube fa-fw"></i>
                    Minecraft Players
                </a>
            </li>
            @endscope
            @scope(Entities\Models\PanelGroupScope::MANAGE_GROUPS)
            <li class="nav-item">
                <a class="nav-link {{ request()->is('panel/groups*') ? 'active' : '' }} "
                   href="{{ route('front.panel.groups.index') }}">
                    <i class="fas fa-users fa-fw"></i>
                    Groups
                </a>
            </li>
            @endscope
            @scope(Entities\Models\PanelGroupScope::MANAGE_BADGES)
            <li class="nav-item">
                <a class="nav-link {{ request()->is('panel/badges*') ? 'active' : '' }}"
                   href="{{ route('front.panel.badges.index') }}">
                    <i class="fas fa-medal fa-fw"></i>
                    Badges
                </a>
            </li>
            @endscope
            @scope(Entities\Models\PanelGroupScope::VIEW_ACTIVITY)
            <li class="nav-item">
                <a class="nav-link {{ request()->is('panel/activity*') ? 'active' : '' }}"
                   href="{{ route('front.panel.activity.index') }}">
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
            @scope(Entities\Models\PanelGroupScope::MANAGE_WARNINGS)
            <li class="nav-item">
                <a class="nav-link {{ request()->is('panel/warnings*') ? 'active' : '' }}"
                   href="{{ route('front.panel.warnings.index') }}">
                    <i class="fas fa-exclamation-triangle fa-fw"></i>
                    Warnings
                </a>
            </li>
            @endscope
            @scope(Entities\Models\PanelGroupScope::MANAGE_BANS)
            <li class="nav-item">
                <a class="nav-link {{ request()->is('panel/player-bans*') ? 'active' : '' }}"
                   href="{{ route('front.panel.player-bans.index') }}">
                    <i class="fas fa-user-slash fa-fw"></i>
                    Banned Players
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('panel/ip-bans*') ? 'active' : '' }}"
                   href="{{ route('front.panel.ip-bans.index') }}">
                    <i class="fas fa-ban fa-fw"></i>
                    Banned IPs
                </a>
            </li>
            @endscope
        </ul>

        @scope(Entities\Models\PanelGroupScope::MANAGE_SHOWCASE_WARPS)
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Minecraft</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link {{ request()->is('panel/showcase-warps*') ? 'active' : '' }} "
                   href="{{ route('front.panel.showcase-warps.index') }}">
                    <i class="fas fa-flag fa-fw"></i>
                    Showcase Warps
                </a>
            </li>
        </ul>
        @endscope

        @scope(Entities\Models\PanelGroupScope::MANAGE_PAGES)
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Content</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link {{ request()->is('panel/pages*') ? 'active' : '' }} "
                   href="{{ route('front.panel.pages.index') }}">
                    <i class="fas fa-book fa-fw"></i>
                    Pages
                </a>
            </li>
        </ul>
        @endscope

        @scope(Entities\Models\PanelGroupScope::MANAGE_SERVERS)
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Servers</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link {{ request()->is('panel/servers*') ? 'active' : '' }} "
                   href="{{ route('front.panel.servers.index') }}">
                    <i class="fas fa-server fa-fw"></i>
                    Servers
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('panel/server-tokens*') ? 'active' : '' }} "
                   href="{{ route('front.panel.server-tokens.index') }}">
                    <i class="fas fa-key fa-fw"></i>
                    Server Tokens
                </a>
            </li>
        </ul>
        @endscope

        @scope(Entities\Models\PanelGroupScope::MANAGE_DONATIONS)
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Transactions</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link {{ request()->is('panel/donations*') ? 'active' : '' }}"
                   href="{{ route('front.panel.donations.index') }}">
                    <i class="fas fa-credit-card fa-fw"></i>
                    Donations
                </a>
            </li>
        </ul>
        @endscope

        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Review</span>
        </h6>
        <ul class="nav flex-column mb-2">
            @scope(Entities\Models\PanelGroupScope::REVIEW_BUILD_RANK_APPS)
            <li class="nav-item">
                <a class="nav-link {{ request()->is('panel/builder-ranks*') ? 'active' : '' }}"
                   href="{{ route('front.panel.builder-ranks.index') }}">
                    <i class="fas fa-hammer fa-fw"></i>
                    Builder Rank Applications
                    @if ($outstandingRankApps > 0)
                        <span class="badge bg-danger">{{ $outstandingRankApps }}</span>
                    @endif
                </a>
            </li>
            @endscope
            @scope(Entities\Models\PanelGroupScope::REVIEW_SHOWCASE_APPS)
            <li class="nav-item">
                <a class="nav-link {{ request()->is('panel/showcase-apps*') ? 'active' : '' }}"
                   href="{{ route('front.panel.showcase-apps.index') }}">
                    <i class="fas fa-certificate fa-fw"></i>
                    Showcase Applications
                    @if ($outstandingShowcaseApps > 0)
                        <span class="badge bg-danger">{{ $outstandingShowcaseApps }}</span>
                    @endif
                </a>
            </li>
            @endscope
            @scope(Entities\Models\PanelGroupScope::REVIEW_APPEALS)
            <li class="nav-item">
                <a class="nav-link {{ request()->is('panel/ban-appeals*') ? 'active' : '' }}"
                   href="{{ route('front.panel.ban-appeals.index') }}">
                    <i class="fas fa-gavel fa-fw"></i>
                    Ban Appeals
                    @if ($outstandingBanAppeals > 0)
                        <span class="badge bg-danger">{{ $outstandingBanAppeals }}</span>
                    @endif
                </a>
            </li>
            @endscope
        </ul>
    </div>
</nav>

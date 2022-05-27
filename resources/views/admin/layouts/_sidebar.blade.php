<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->is('panel') ? 'active' : '' }}" aria-current="page" href="{{ route('front.panel.index') }}">
                    <i class="fas fa-home fa-fw"></i>
                    Home
                </a>
            </li>
        </ul>

        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Users</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link {{ request()->is('panel/accounts*') ? 'active' : '' }}" href="{{ route('front.panel.accounts.index') }}">
                    <i class="fas fa-users fa-fw"></i>
                    Accounts
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('panel/minecraft-players*') ? 'active' : '' }} " href="{{ route('front.panel.minecraft-players.index') }}">
                    <i class="fas fa-cube"></i>
                    Minecraft Players
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('panel/groups*') ? 'active' : '' }} " href="{{ route('front.panel.groups.index') }}">
                    <i class="fas fa-users"></i>
                    Groups
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('panel/pages*') ? 'active' : '' }} " href="{{ route('front.panel.pages.index') }}">
                    <i class="fas fa-book"></i>
                    Pages
                </a>
            </li>
        </ul>

        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Transactions</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link {{ request()->is('panel/donations*') ? 'active' : '' }}" href="{{ route('front.panel.donations.index') }}">
                    <i class="fas fa-credit-card fa-fw"></i>
                    Donations
                </a>
            </li>
        </ul>

        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Review</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link {{ request()->is('panel/builder-ranks*') ? 'active' : '' }}" href="{{ route('front.panel.builder-ranks.index') }}">
                    <i class="fas fa-hammer fa-fw"></i>
                    Builder Rank Applications
                    @if ($outgoing_rank_apps > 0)
                        <span class="badge bg-danger">{{ $outgoing_rank_apps }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('panel/ban-appeals*') ? 'active' : '' }}" href="{{ route('front.panel.ban-appeals.index') }}">
                    <i class="fas fa-gavel fa-fw"></i>
                    Ban Appeals
                    @if ($outstanding_ban_appeals > 0)
                        <span class="badge bg-danger">{{ $outstanding_ban_appeals }}</span>
                    @endif
                </a>
            </li>
        </ul>
    </div>
</nav>

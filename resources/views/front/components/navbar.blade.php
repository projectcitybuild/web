<nav class="navbar">
    <div class="container">
        <div class="logo">
            <a href="{{ route('front.home') }}">
                <img srcset="{{ Vite::asset('resources/images/logo-1x.png') }},
                             {{ Vite::asset('resources/images/logo-2x.png') }} 2x"
                     src="{{ Vite::asset('resources/images/logo-1x.png') }}"
                     alt="Project City Build"
                />
            </a>
        </div>

        <ul class="nav-links">
            <li><a href="{{ route('front.home') }}">Home</a></li>
            <li><a href="https://portal.projectcitybuild.com/">Portal</a></li>
            <li><a href="{{ route('front.maps') }}">Live Maps</a></li>
            <li><a href="{{ route('vote') }}">Vote For Us</a></li>
            <li><a href="{{ route('front.donate') }}">Donate</a></li>
            <li>
                <a href="javascript:void(0)" class="nav-dropdown">More <i class="fas fa-caret-down"></i></a>
                <ul class="dropdown multi">
                    <li>
                        <h5>Information</h5>
                        <ul>
                            <li><a href="{{ route('rules') }}">Rules & Guidelines</a></li>
                            <li><a href="{{ route('ranks') }}">Ranks</a></li>
                            <li><a href="{{ route('staff') }}">Staff</a></li>
                        </ul>
                    </li>
                    <li>
                        <h5>Community</h5>
                        <ul>
                            <li><a href="{{ route('map-archive') }}">Map Archive</a></li>
                            <li><a href="{{ route('3d-maps') }}">3d Map</a></li>
                            <li><a href="{{ route('front.banlist') }}">Ban List</a></li>
                        </ul>
                    </li>
                    <li>
                        <h5>Apply</h5>
                        <ul>
                            <li><a href="{{ route('front.appeal') }}">Appeal a Ban</a></li>
                            <li><a href="{{ route('report') }}">Report a Player</a></li>
                            <li><a href="{{ route('front.rank-up') }}">Apply for Build Rank</a></li>
                            <li class="divider"><a href="https://goo.gl/forms/UodUsKQBZJdCzNWk1">Apply for Staff</a></li>
                        </ul>
                    </li>
                </ul>
            </li>

            <li class="spacer"></li>

            @if($isLoggedIn)
                <li>
                    <a href="javascript:void(0)" class="nav-dropdown">Account <i class="fas fa-caret-down"></i></a>
                    <ul class="dropdown single">
                        <li class="divider"><a href="{{ route('front.account.profile') }}">Account Settings</a></li>
                        @if($canAccessPanel)
                            <li class="divider"><a href="{{ route('front.panel.index') }}">Staff Panel</a></li>
                        @endif
                        <li>
                            <a href="{{ route('front.logout') }}">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    </ul>
                </li>
            @else
                <li><a href="{{ route('front.register') }}">Join Us</a></li>
                <li><a href="{{ route('front.login') }}">Sign In</a></li>
            @endif
        </ul>

        <div class="hamburger">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </div>
    </div>
</nav>

<nav class="navbar">
    <div class="container">
        <div class="logo">
            <a href="{{ route('front.home') }}">
                <img srcset="/assets/images/logo-1x.png,
                             /assets/images/logo-2x.png 2x"
                     src="/assets/images/logo-1x.png"
                     alt="Project City Build"
                />
            </a>
        </div>

        <ul class="nav-links">
            <li><a href="{{ route('front.home') }}">Home</a></li>
            <li>
                <a href="javascript:void(0)" class="nav-dropdown">Server <i class="fas fa-caret-down"></i></a>
                <ul class="dropdown multi">
                    <li>
                        <h5>Information</h5>
                        <ul>
                            <li><a href="https://forums.projectcitybuild.com/t/pcb-community-rules/22928">Rules & Guidelines</a></li>
                            <li><a href="https://forums.projectcitybuild.com/t/pcb-ranks/32812">Ranks</a></li>
                            <li><a href="https://wiki.projectcitybuild.com/wiki/List_of_Staff_Members">Staff</a></li>
                            <li><a href="https://forums.projectcitybuild.com/t/vote-for-our-server/18568">Vote For Us</a></li>
                            <li><a href="https://forums.projectcitybuild.com/t/map-archive/32793">Map Archive</a></li>
                        </ul>
                    </li>
                    <li>
                        <h5>Maps</h5>
                        <ul>
                            <li><a href="{{ route('maps') }}" target="_blank" rel="noopener noreferrer">Real-Time Maps</a></li>
                            <li><a href="{{ route('3d-maps') }}" target="_blank" rel="noopener noreferrer">3D Maps</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li>
                <a href="javascript:void(0)" class="nav-dropdown">Community <i class="fas fa-caret-down"></i></a>
                <ul class="dropdown single">
                    <li><a href="{{ route('front.appeal') }}">Appeal a Ban</a></li>
                    <li><a href="{{ route('report') }}">Report a Player</a></li>
                    <li><a href="{{ route('front.rank-up') }}">Apply for Build Rank</a></li>
                    <li class="divider"><a href="https://goo.gl/forms/UodUsKQBZJdCzNWk1">Apply for Staff</a></li>
                    <li><a href="https://forums.projectcitybuild.com/" target="_blank" rel="noopener">Forums (archived)</a></li>
                    <li class="divider"><a href="https://wiki.projectcitybuild.com/" target="_blank" rel="noopener">Community Wiki</a></li>
                    <li><a href="{{ route('front.banlist') }}">Ban List</a></li>
                </ul>
            </li>
            <li><a href="https://www.instagram.com/projectcitybuild" target="_blank" rel="noopener noreferrer">Media</a></li>
            <li><a href="{{ route('front.donate') }}">Donate</a></li>

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

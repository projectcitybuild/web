<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google-site-verification" content="Sp9E55tVkNph_ttvggLD52MY-ACeGfeivQbmWp7CWfo">
    <meta name="description" content="@yield('description')">
    <meta name="theme-color" content="#524641">
    <meta name="apple-mobile-web-app-title" content="PCB">

    <meta property="og:url" content="https://www.projectcitybuild.com">
    <meta property="og:title" content="@yield('title', 'Project City Build')">
    <meta property="og:description" content="@yield('description')">
    <meta property="og:site_name" content="Project City Build">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="en_US">

    <meta name="twitter:card" content="@yield('description')">
    <meta name="twitter:site" content="@PCB_Minecraft">


    <title>@yield('title', 'Project City Build')</title>

    <link rel="stylesheet" href="{{ mix('assets/css/app-v2.css') }}">
    <link rel="icon" type="type/x-icon" href="{{ asset('assets/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="https://i.imgur.com/g1OfIGT.png"/>

    <script defer src="https://use.fontawesome.com/releases/v5.10.2/js/brands.js"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.10.2/js/solid.js"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.10.2/js/fontawesome.js"></script>

    <script defer src="{{ mix('assets/js/manifest.js') }}"></script>
    <script defer src="{{ mix('assets/js/vendor.js') }}"></script>
    <script defer src="{{ mix('assets/js/app.js') }}"></script>

    @stack('head')
</head>
<body>

<nav class="navbar">
    <div class="container">
        <ul>
            <li><img src="assets/images/logo.png" /></li>
            <li><a href="{{ route('front.home') }}">Home</a></li>
            <li><a href="https://forums.projectcitybuild.com/">Forums</a></li>
            <li>
                <a href="#" class="nav-dropdown">Servers <i class="fas fa-caret-down"></i></a>
                <ul class="menu-sideway">
                    <li>
                        <h5>Minecraft</h5>
                        <ul>
                            <li><a href="https://forums.projectcitybuild.com/t/pcb-community-rules/22928">Rules &
                                    Guidelines</a></li>
                            <li><a href="https://forums.projectcitybuild.com/t/pcb-ranks/32812">Ranks</a></li>
                            <li><a href="https://wiki.projectcitybuild.com/wiki/List_of_Staff_Members">Staff</a></li>
                            <li><a href="http://pcbmc.co:8123/" target="_blank">Real-Time Map</a></li>
                        </ul>
                    </li>
                    <li>
                        <h5>Feed the Beast</h5>
                        <ul>
                            <li>
                                <a href="https://forums.projectcitybuild.com/t/modded-is-back-with-our-custom-false-hope-pack/34989/3">Rules
                                    & Guidelines</a></li>
                            <li>
                                <a href="https://forums.projectcitybuild.com/t/modded-is-back-with-our-custom-false-hope-pack/34989/2">Installation
                                    Guide</a></li>
                        </ul>
                    </li>
                    <li>
                        <h5>Terraria</h5>
                        <ul>
                            <li><a href="#">Rules & Guidelines</a></li>
                            <li><a href="#">Installation Guide</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#" class="nav-dropdown">Community <i class="fas fa-caret-down"></i></a>
                <ul>
                    <li><a href="https://forums.projectcitybuild.com/t/banned-read-me/12145">Appeal a Ban</a></li>
                    <li><a href="https://forums.projectcitybuild.com/c/support/grief-player-reports">Report a Player</a>
                    </li>
                    <li class="divider"><a href="https://goo.gl/forms/UodUsKQBZJdCzNWk1">Apply for Staff</a></li>
                    <li class="divider"><a href="{{ route('front.donate') }}">Donate</a></li>
                    <li><a href="https://wiki.projectcitybuild.com/" target="_blank" rel="noopener">Community Wiki</a>
                    </li>
                </ul>
            </li>
            <li><a href="{{ route('front.banlist') }}">Ban List</a></li>
        </ul>

        <ul>
            @if(Auth::check())
                <li>
                    <a href="#" class="nav-dropdown">Account <i class="fas fa-caret-down"></i></a>
                    <ul>
                        <li><a href="{{ route('front.account.settings') }}">Account Settings</a></li>
                        <li class="divider"><a href="https://forums.projectcitybuild.com/my/preferences/account">Forum
                                Settings</a></li>
                        <li>
                            <a href="{{ route('front.logout') }}">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    </ul>
                </li>
            @else
                <li><a href="{{ route('front.register') }}">Create an Account</a></li>
                <li><a href="{{ route('front.login') }}">Sign In</a></li>
            @endif
        </ul>
    </div>
</nav>

<div id="app">
    <header class="hero">
        <div class="container">
            <div class="hero__cta">
                <h1>We Build Stuff.<br />Come Join Us!</h1>
                <h2>
                    One of the world's longest-running Minecraft servers; we're a <br />
                    community of creative players and city builders
                </h2>

                <a href="" class="button button__outlined">
                    <i class="fas fa-mouse-pointer"></i>
                    Join Now
                </a>
            </div>

            <div class="hero__server-feed">
                <div class="server-feed__server online">
                    <span class="server-feed__title">Minecraft (Java)</span>
                    <span class="server-feed__player-count"><i class="fas fa-user"></i> 10/80</span>
                    <span class="server-feed__address">pcbmc.co</span>
                </div>
                <div class="server-feed__server offline">
                    <span class="server-feed__title">Feed The Beast</span>
                    <span class="server-feed__address">51.254.81.134</span>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section class="news-bar">
            <div class="container">
                <span class="news-bar__date">
                    <i class="fas fa-calendar-day"></i> Jan 1st, 2020
                </span>

                <a class="news-bar__title" href="#">Announcement post title will go here</a>
                <a class="news-bar__view-more" href="#"><i class="fas fa-chevron-right"></i> All Announcements</a>
            </div>
        </section>

        <section class="introduction">
            <div class="container">
                <h1>Minecraft 24/7</h1>

                <div class="introduction__content">
                    <div class="introduction__text">
                        With our free-build Creative and Survival multiplayer maps, we offer a fun platform & building experience like no other. You can visit and build in established towns & cities or start your own.
                    </div>

                    <ul class="introduction__points fa-ul">
                        <li><span class="fa-li"><i class="fas fa-cube"></i></span> Free-build maps</li>
                        <li><span class="fa-li"><i class="fas fa-cube"></i></span> Grief protection</li>
                        <li><span class="fa-li"><i class="fas fa-cube"></i></span> Friendly community</li>
                        <li><span class="fa-li"><i class="fas fa-cube"></i></span> Earn trust based perks</li>
                        <li><span class="fa-li"><i class="fas fa-cube"></i></span> Helpful staff</li>
                        <li><span class="fa-li"><i class="fas fa-cube"></i></span> Events and competitions</li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="server-overview">
            <div class="server-overview__server">
                <div class="server-image"></div>
                <div class="server-text">
                    <h1>Survival</h1>

                    <div class="server-text__desc">
                        Some text about this particular world can go here blah blah blah. Some text about this particular world can go here blah blah blah.
                    </div>
                    <div class="server-text__links">
                        <ul>
                            <li>
                                <a href="#"><i class="fas fa-chevron-right"></i> Read More</a>
                            </li>
                            <li>
                                <a href="#"><i class="fas fa-chevron-right"></i> Real-Time Map</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="server-overview__server right">
                <div class="server-text">
                    <h1>Creative</h1>

                    <div class="server-text__desc">
                        Some text about this particular world can go here blah blah blah. Some text about this particular world can go here blah blah blah.
                    </div>
                    <div class="server-text__links">
                        <ul>
                            <li>
                                <a href="#"><i class="fas fa-chevron-right"></i> Read More</a>
                            </li>
                            <li>
                                <a href="#"><i class="fas fa-chevron-right"></i> Real-Time Map</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="server-image"></div>
            </div>

            <div class="server-overview__server">
                <div class="server-image"></div>
                <div class="server-text">
                    <h1>Monarch</h1>

                    <div class="server-text__desc">
                        Some text about this particular world can go here blah blah blah. Some text about this particular world can go here blah blah blah.
                    </div>
                    <div class="server-text__links">
                        <ul>
                            <li>
                                <a href="#"><i class="fas fa-chevron-right"></i> Read More</a>
                            </li>
                            <li>
                                <a href="#"><i class="fas fa-chevron-right"></i> Real-Time Map</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="server-overview__server right">
                <div class="server-text">
                    <h1>Arcade</h1>

                    <div class="server-text__desc">
                        Some text about this particular world can go here blah blah blah. Some text about this particular world can go here blah blah blah.
                    </div>
                    <div class="server-text__links">
                        <ul>
                            <li>
                                <a href="#"><i class="fas fa-chevron-right"></i> Read More</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="server-image"></div>
            </div>
        </section>

        <footer>
            <section class="footer-sitemap">
                <div class="container">
                    <h1>Explore More</h1>

                    <div class="footer-links">
                        <div class="footer-links__category">
                            <h2>Server</h2>
                            <ul>
                                <li><i class="fas fa-cube"></i> <a href="#">Rules & Guidelines</a></li>
                                <li><i class="fas fa-cube"></i> <a href="#">Ranks</a></li>
                                <li><i class="fas fa-cube"></i> <a href="#">Commands</a></li>
                            </ul>
                        </div>

                        <div class="footer-links__category">
                            <h2>Community</h2>
                            <ul>
                                <li><i class="fas fa-cube"></i> <a href="#">Community Wiki</a></li>
                                <li><i class="fas fa-cube"></i> <a href="#">Vote For Our Server</a></li>
                            </ul>
                        </div>

                        <div class="footer-links__category">
                            <h2>Social Media</h2>
                            <ul>
                                <li><i class="fas fa-cube"></i> <a href="#">YouTube</a></li>
                                <li><i class="fas fa-cube"></i> <a href="#">Instagram</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <section class="donations">
                <div class="container">
                    <div class="donations__left">
                        <h1>Help Keep Us Online</h1>

                        <span>
                            Donations are the only way to keep our server running.<br />
                            Donators receive perks such as flying, colored names and much more
                        </span>
                    </div>
                    <div class="donations__right">

                    </div>
                </div>
            </section>
        </footer>
    </main>
</div>

</body>
</html>

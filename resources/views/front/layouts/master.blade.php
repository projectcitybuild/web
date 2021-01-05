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

        <meta property="og:url"         content="https://www.projectcitybuild.com">
        <meta property="og:title"       content="@yield('title', 'Project City Build')">
        <meta property="og:description" content="@yield('description')">
        <meta property="og:site_name"   content="Project City Build">
        <meta property="og:type"        content="website">
        <meta property="og:locale"      content="en_US">

        <meta name="twitter:card"       content="@yield('description')">
        <meta name="twitter:site"       content="@PCB_Minecraft">


        <title>@yield('title', 'Project City Build')</title>

        <link rel="stylesheet" href="{{ mix('assets/css/app.css') }}">
        <link rel="icon" type="type/x-icon" href="{{ asset('assets/favicon.ico') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="https://i.imgur.com/g1OfIGT.png" />

        <script defer src="https://use.fontawesome.com/releases/v5.10.2/js/brands.js"></script>
        <script defer src="https://use.fontawesome.com/releases/v5.10.2/js/solid.js"></script>
        <script defer src="https://use.fontawesome.com/releases/v5.10.2/js/fontawesome.js"></script>

        <script defer src="{{ mix('assets/js/manifest.js') }}"></script>
        <script defer src="{{ mix('assets/js/vendor.js') }}"></script>
        <script defer src="{{ mix('assets/js/app.js') }}"></script>

        @stack('head')

        @if(env('APP_ENV') != 'local')
            <script async src="https://www.googletagmanager.com/gtag/js?id=UA-2747125-5"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());

                gtag('config', 'UA-2747125-5');
            </script>
        @endif
    </head>
    <body>

        <nav id="main-nav">
            <div class="container">
                <ul>
                    <li><a href="{{ route('front.home') }}">Home</a></li>
                    <li><a href="https://forums.projectcitybuild.com/">Forums</a></li>
                    <li>
                        <a href="#" class="nav-dropdown">Servers <i class="fas fa-caret-down"></i></a>
                        <ul class="menu-sideway">
                            <li>
                                <h5>Minecraft</h5>
                                <ul>
                                    <li><a href="https://forums.projectcitybuild.com/t/pcb-community-rules/22928">Rules & Guidelines</a></li>
                                    <li><a href="https://forums.projectcitybuild.com/t/pcb-ranks/32812">Ranks</a></li>
                                    <li><a href="https://wiki.projectcitybuild.com/wiki/List_of_Staff_Members">Staff</a></li>
                                    <li><a href="http://pcbmc.co:8123/" target="_blank">Real-Time Map</a></li>
                                </ul>
                            </li>
                            <li>
                                <h5>Feed the Beast</h5>
                                <ul>
                                    <li><a href="https://forums.projectcitybuild.com/t/modded-is-back-with-our-custom-false-hope-pack/34989/3">Rules & Guidelines</a></li>
                                    <li><a href="https://forums.projectcitybuild.com/t/modded-is-back-with-our-custom-false-hope-pack/34989/2">Installation Guide</a></li>
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
                            <li><a href="https://forums.projectcitybuild.com/c/support/grief-player-reports">Report a Player</a></li>
                            <li class="divider"><a href="https://goo.gl/forms/UodUsKQBZJdCzNWk1">Apply for Staff</a></li>
                            <li class="divider"><a href="{{ route('front.donate') }}">Donate</a></li>
                            <li><a href="https://wiki.projectcitybuild.com/" target="_blank" rel="noopener">Community Wiki</a></li>
                        </ul>
                    </li>
                    <li><a href="{{ route('front.banlist') }}">Ban List</a></li>
                </ul>

                <ul>
                    <li class="social-icon">
                        <a target="_blank" rel="noopener" href="https://www.facebook.com/ProjectCityBuild">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                    </li>
                    <li class="social-icon">
                        <a target="_blank" rel="noopener" href="https://www.instagram.com/projectcitybuild">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </li>
                    <li class="social-icon">
                        <a target="_blank" rel="noopener" href="https://www.youtube.com/user/PCBMinecraft">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </li>
                    <li class="social-icon">
                        <a target="_blank" rel="noopener" href="http://steamcommunity.com/groups/ProjectCityBuild">
                            <i class="fab fa-steam-symbol"></i>
                        </a>
                    </li>
                    @if(Auth::check())
                        <li>
                            <a href="#" class="nav-dropdown">Account <i class="fas fa-caret-down"></i></a>
                            <ul>
                                <li><a href="{{ route('front.account.settings') }}">Account Settings</a></li>
                                <li class="divider"><a href="https://forums.projectcitybuild.com/my/preferences/account">Forum Settings</a></li>
                                <li>
                                    <a href="{{ route('front.logout') }}">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li><a href="{{ route('front.login') }}">Login</a></li>
                    @endif
                </ul>
            </div>
        </nav>


        <div class="drawer-btn-container">
            <a href="#" id="drawer-btn"><i class="fas fa-bars"></i></a>
        </div>

        <main>

            @php
                $isHomepage = Route::current()->getName() === 'front.home';
            @endphp
            <header class="header {{ !$isHomepage ? 'header--thin' : '' }}">
                <div class="container header__container">
                    <div class="header__left">
                        <img class="header__logo {{ !$isHomepage ? 'header__logo--nopadding' : '' }}" src="{{ asset('assets/images/logo.png') }}" alt="Project City Build" />

                        @includeWhen($isHomepage, 'front.components.server-feed')
                    </div>

                    <div class="header__right">
                        @if($isHomepage)
                        <div class="hero">
                            <h1 class="hero__header">We Build Stuff.</h1>
                            <div class="hero__slogan">
                                PCB is a gaming community of creative players and city builders.<br>
                                Over <span class="accent strong">{{ number_format($playerCount) ?: 0 }}</span> registered players and always growing.
                            </div>

                            <div class="hero__actions">
                                @guest
                                    <a class="hero__button" href="{{ route('front.register') }}">
                                        <i class="fas fa-mouse-pointer"></i>
                                        Join Us
                                    </a>
                                    <a class="hero__button hero__button--bordered" href="{{ route('front.login') }}">
                                        Login
                                    </a>
                                @endguest

                                @auth
                                    <a class="hero__button" href="https://forums.projectcitybuild.com/my/summary">
                                        Profile
                                    </a>
                                    <a class="hero__button hero__button--bordered" href="https://forums.projectcitybuild.com/my/preferences/account">
                                        Settings
                                    </a>
                                @endauth
                            </div>
                        </div>
                        @endif

                        @includeWhen(!$isHomepage, 'front.components.server-feed')
                    </div>
                </div>
            </header>

            <section>
                <div class="container contents">
                    @yield('contents')
                </div>
            </section>

            <footer class="footer">
                <div class="container footer__container">
                    <div class="footer__left">
                        <ul class="footer__bullets">
                            <li><h5 class="footer__subheading">Legal</h5></li>
                            <li><i class="fas fa-check-circle"></i> <a href="{{ route('terms') }}">Terms of Service</a></li>
                            <li><i class="fas fa-check-circle"></i> <a href="{{ route('privacy') }}">Privacy Policy</a></li>
                        </ul>
                        <ul class="footer__bullets">
                            <li><h5 class="footer__subheading">Open Source</h5></li>
                            <li><i class="fas fa-code-branch"></i> <a target="_blank" rel="noopener" href="https://github.com/projectcitybuild/PCBridge">PCBridge</a></li>
                            <li><i class="fas fa-code-branch"></i> <a target="_blank" rel="noopener" href="https://github.com/projectcitybuild/web">projectcitybuild.com</a></li>
                        </ul>
                    </div>
                    <div class="footer__right">
                        <div class="footer__social-icons">
                            <a target="_blank" rel="noopener" href="https://www.facebook.com/ProjectCityBuild"><i class="fab fa-facebook-square"></i></a>
                            <a target="_blank" rel="noopener" href="https://twitter.com/PCB_Minecraft"><i class="fab fa-twitter-square"></i></a>
                            <a target="_blank" rel="noopener" href="https://www.instagram.com/projectcitybuild"><i class="fab fa-instagram"></i></a>
                            <a target="_blank" rel="noopener" href="https://www.youtube.com/user/PCBMinecraft"><i class="fab fa-youtube"></i></a>
                            <a target="_blank" rel="noopener" href="http://projectcitybuild.tumblr.com/"><i class="fab fa-tumblr-square"></i></a>
                            <a target="_blank" rel="noopener" href="http://steamcommunity.com/groups/ProjectCityBuild"><i class="fab fa-steam-square"></i></a>
                        </div>
                        <a href="#top">Return to Top</a>
                    </div>

                </div>
            </footer>
        </main>

        @stack('body-js')

    </body>
</html>

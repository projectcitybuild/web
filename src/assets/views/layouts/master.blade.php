<!DOCTYPE html>
<html lang="en">
    <head>    
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="google-site-verification" content="Sp9E55tVkNph_ttvggLD52MY-ACeGfeivQbmWp7CWfo">
        <meta name="description" content="@yield('description', 'Medium sized community of creative gamers. Join our Minecraft city building server, host to a variety of different themed cities and towns.')">

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
        
        <script defer src="https://use.fontawesome.com/releases/v5.0.6/js/brands.js"></script>
        <script defer src="https://use.fontawesome.com/releases/v5.0.6/js/solid.js"></script>
        <script defer src="https://use.fontawesome.com/releases/v5.0.6/js/fontawesome.js"></script>

        <script defer src="{{ mix('assets/js/app.js') }}"></script>

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
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="http://projectcitybuild.com/forums/">Forums</a></li>
                    <li>
                        <a href="#" class="nav-dropdown">Servers <i class="fas fa-caret-down"></i></a>
                        <ul class="menu-sideway">
                            <li>
                                <h5>Minecraft</h5>
                                <ul>
                                    <li><a href="http://projectcitybuild.com/forums/index.php?topic=11146">Rules & Guidelines</a></li>
                                    <li><a href="#">Ranks</a></li>
                                    <li><a href="#">Staff</a></li>
                                    <li><a href="http://pcbmc.co:8123/" target="_blank">Real-Time Map</a></li>
                                </ul>
                            </li>
                            <li>
                                <h5>Feed the Beast</h5>
                                <ul>
                                    <li><a href="#">Rules & Guidelines</a></li>
                                    <li><a href="#">Installation Guide</a></li>
                                    <li><a href="#">Real-Time Map</a></li>
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
                            <li><a href="#">Appeal a Ban</a></li>
                            <li><a href="#">Report a Player</a></li>
                            <li class="divider"><a href="#">Apply for Staff</a></li>
                            <li><a href="https://wiki.projectcitybuild.com/" target="_blank" rel="noopener">Community Wiki</a></li>
                        </ul>
                    </li>
                    <li><a href="{{ route('banlist') }}">Ban List</a></li>
                </ul>

                <ul>
                    <li class="social-icon">
                        <a href="https://www.facebook.com/ProjectCityBuild" target="_blank" rel="noopener"><i class="fab fa-facebook-f"></i></a>
                    </li>
                    <li class="social-icon">
                        <a href="https://www.instagram.com/projectcitybuild" target="_blank" rel="noopener"><i class="fab fa-instagram"></i></a>
                    </li>
                    <li class="social-icon">
                        <a href="https://www.youtube.com/user/PCBMinecraft" target="_blank" rel="noopener"><i class="fab fa-youtube"></i></a>
                    </li>
                    <li class="social-icon">
                        <a href="http://steamcommunity.com/groups/ProjectCityBuild" target="_blank" rel="noopener"><i class="fab fa-steam-symbol"></i></a>
                    </li>
                    <li><a href="#">Login</a></li>
                </ul>
            </div>
        </nav>

        
        <div class="drawer-btn-container">
            <a href="#" id="drawer-btn"><i class="fas fa-bars"></i></a>
        </div>

        <main>
            <header class="header">
                <div class="container header__container">
                    <div class="header__left">
                        <img class="header__logo" src="{{ asset('assets/images/logo.png') }}" alt="Project City Build" />
                    </div>
                    
                    <div class="header__right">
                        <section class="server-feed">
                            <div class="category">
                                <h5 class="category__heading">Minecraft</h5>
                                <div class="server server--online">
                                    <div class="server__title">Survival / Creative [24/7]</div>
                                    <div class="server__players badge badge--secondary">14/80</div>
                                    <div class="server__ip">pcbmc.co</div>
                                </div>
                                <div class="server server--offline">
                                    <div class="server__title">Feed the Beast</div>
                                    <div class="server__players badge badge--light">Offline</div>
                                    <div class="server__ip">23.94.186.178:25565</div>
                                </div>
                                <div class="server server--offline">
                                    <div class="server__title">Pixelmon</div>
                                    <div class="server__players badge badge--light">Offline</div>
                                    <div class="server__ip">23.94.186.178:25565</div>
                                </div>
                            </div>

                            <div class="category">
                                <h5 class="category__heading">Other Games</h5>
                                <div class="server server--online">
                                    <div class="server__title">Terraria</div>
                                    <div class="server__players badge badge--secondary">14/80</div>
                                    <div class="server__ip">pcbmc.co</div>
                                </div>
                                <div class="server server--offline">
                                    <div class="server__title">Starbound</div>
                                    <div class="server__players badge badge--light">Offline</div>
                                    <div class="server__ip">pcbmc.co</div>
                                </div>
                            </div>
                        </section>
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
                            <li><i class="fas fa-check-circle"></i> <a href="http://projectcitybuild.com/forums/index.php?topic=2718">Terms of Service</a></li>
                            <li><i class="fas fa-check-circle"></i> <a href="#">Privacy Policy</a></li>
                        </ul>
                        <ul class="footer__bullets">
                            <li><h5 class="footer__subheading">Open Source</h5></li>
                            <li><i class="fas fa-code-branch"></i> <a target="_blank" rel="noopener" href="https://github.com/itsmyfirstday/PCBridge">PCBridge</a></li>
                            <li><i class="fas fa-code-branch"></i> <a target="_blank" rel="noopener" href="https://github.com/itsmyfirstday/ProjectCityBuild">projectcitybuild.com</a></li>
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
        
    </body>
</html>

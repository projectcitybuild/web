<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="google-site-verification" content="Sp9E55tVkNph_ttvggLD52MY-ACeGfeivQbmWp7CWfo" />

        <title>Project City Build</title>

        <link rel="stylesheet" type="text/css" href="{{ mix('assets/css/app.css') }}" />

        <script src="https://use.fontawesome.com/releases/v5.0.1/js/brands.js"></script>
        <script src="https://use.fontawesome.com/releases/v5.0.1/js/solid.js"></script>
        <!-- <script src="https://use.fontawesome.com/releases/v5.0.1/js/regular.js"></script> -->
        <script src="https://use.fontawesome.com/releases/v5.0.1/js/fontawesome.js"></script>

        @if(env('APP_ENV') != 'local'))
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
                            <li><a href="https://wiki.projectcitybuild.com/" target="_blank">Community Wiki</a></li>
                        </ul>
                    </li>
                    <li><a href="{{ route('banlist') }}">Ban List</a></li>
                </ul>

                <ul>
                    <li class="social-icon">
                        <a href="https://www.facebook.com/ProjectCityBuild" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    </li>
                    <li class="social-icon">
                        <a href="https://www.instagram.com/projectcitybuild" target="_blank"><i class="fab fa-instagram"></i></a>
                    </li>
                    <li class="social-icon">
                        <a href="https://www.youtube.com/user/PCBMinecraft" target="_blank"><i class="fab fa-youtube"></i></a>
                    </li>
                    <li class="social-icon">
                        <a href="http://steamcommunity.com/groups/ProjectCityBuild" target="_blank"><i class="fab fa-steam-symbol"></i></a>
                    </li>
                    <li><a href="#">Login</a></li>
                </ul>
            </div>
        </nav>

        <main>
            <a href="#" id="drawer-btn"><i class="fas fa-bars"></i></a>
            test<br />
            <img src="{{ asset('assets/images/logo.png') }}" width="350" />
        </main>

        <!-- <div class="contents">
            <header>
                <div class="left">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('assets/images/logo.png') }}" />
                    </a>
                </div>
                <div class="right">
                    <div id="serverfeed"></div>
                </div>
            </header>
            
            <main class="container">
                @yield('contents')
            </main>

            <footer>
                <div class="container">
                    
                    <div class="left">
                        <ul>
                            <li><h5>Legal</h5></li>
                            <li><i class="fa fa-check-circle"></i> <a href="http://projectcitybuild.com/forums/index.php?topic=2718">Terms of Service</a></li>
                        </ul>
                        <ul>
                            <li><h5>Open Source</h5></li>
                            <li><i class="fa fa-code-fork"></i> <a href="https://github.com/itsmyfirstday/PCBridge" target="_blank">PCBridge</a></li>
                            <li><i class="fa fa-code-fork"></i> <a href="https://github.com/itsmyfirstday/ProjectCityBuild" target="_blank">projectcitybuild.com</a></li>
                        </ul>
                    </div>
                    <div class="right">
                        <div class="social">
                            <a href="https://www.facebook.com/ProjectCityBuild" target="_blank"><i class="fa fa-facebook-square"></i></a>
                            <a href="https://twitter.com/PCB_Minecraft" target="_blank"><i class="fa fa-twitter-square"></i></a>
                            <a href="https://www.instagram.com/projectcitybuild" target="_blank"><i class="fa fa-instagram"></i></a>
                            <a href="https://www.youtube.com/user/PCBMinecraft" target="_blank"><i class="fa fa-youtube-square"></i></a>
                            <a href="http://projectcitybuild.tumblr.com/" target="_blank"><i class="fa fa-tumblr-square"></i></a>
                            <a href="http://steamcommunity.com/groups/ProjectCityBuild" target="_blank"><i class="fa fa-steam"></i></a>
                        </div>
                        <a href="#top">Return to Top</a>
                    </div>

                </div>
            </footer>

        </div> -->

        <script src="{{ mix('assets/js/app.js') }}"></script>
        
    </body>
</html>

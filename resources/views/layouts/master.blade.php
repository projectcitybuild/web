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
    </head>
    <body>
        
        <input type="checkbox" id="drawer-toggle" />
        <label for="drawer-toggle"></label>
        <nav id="mobile-float-nav">
            
        </nav>
        <nav id="menu-nav">
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#">Forums</a></li>
                <li>
                    <a href="#">Servers <i class="fa fa-caret-down"></i></a>
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
                            </ul>
                        </li>
                        <li>
                            <h5>Terraria</h5>
                            <ul>
                                <li><a href="#">Rules & Guidelines</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#">Community <i class="fa fa-caret-down"></i></a>
                    <ul>
                        <li><a href="#">Appeal a Ban</a></li>
                        <li><a href="#">Report a Player</a></li>
                        <li class="divider"><a href="#">Apply for Staff</a></li>
                        <li><a href="https://wiki.projectcitybuild.com/" target="_blank">Community Wiki</a></li>
                    </ul>
                </li>
                <li><a href="#">Ban List</a></li>
            </ul>

            <ul>
                <li class="social-icon"><a href="https://www.facebook.com/ProjectCityBuild" target="_blank"><i class="fa fa-facebook"></i></a></li>
                <li class="social-icon"><a href="https://www.instagram.com/projectcitybuild" target="_blank"><i class="fa fa-instagram"></i></a></li>
                <li class="social-icon"><a href="https://www.youtube.com/user/PCBMinecraft" target="_blank"><i class="fa fa-youtube"></i></a></li>
                <li class="social-icon"><a href="http://steamcommunity.com/groups/ProjectCityBuild" target="_blank"><i class="fa fa-steam"></i></a></li>
                <li><a href="#">Login</a></li>
            </ul>
        </nav>

        <div class="contents">
            <header>
                <div class="left">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('assets/images/logo.png') }}" />
                    </a>
                </div>
                <div class="right">
                    <server-category>
                        <h5>Minecraft</h5>
                        <server online>
                            <div class="status">24/80</div>
                            <div class="title">Survival / Creative [24/7]</div>
                            <div class="ip">pcbmc.co / 198.144.156.53</div>
                        </server>
                        <server offline>
                            <div class="status">Offline</div>
                            <div class="title">Feed the Beast</div>
                            <div class="ip">pcbmc.co</div>
                        </server>
                    </server-category>
                    <server-category>
                        <h5>Other Games</h5>
                        <server offline>
                            <div class="status">Offline</div>
                            <div class="title">Terraria</div>
                            <div class="ip">pcbmc.co</div>
                        </server>
                        <server offline>
                            <div class="status">Offline</div>
                            <div class="title">Starbound</div>
                            <div class="ip">pcbmc.co</div>
                        </server>
                    </server-category>
                </div>
            </header>
            
            <main class="container">
                <div class="left">
                    @yield('contents')
                </div>
                <div class="right">
                    @yield('sidebar')
                </div>
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

        </div>

        <script src="{{ mix('assets/js/app.js') }}"></script>

    </body>
</html>

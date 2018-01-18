<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="google-site-verification" content="Sp9E55tVkNph_ttvggLD52MY-ACeGfeivQbmWp7CWfo" />

        <title>Project City Build</title>

        <link rel="stylesheet" href="{{ mix('assets/css/app.css') }}" />

        <script defer src="https://use.fontawesome.com/releases/v5.0.1/js/brands.js"></script>
        <script defer src="https://use.fontawesome.com/releases/v5.0.1/js/solid.js"></script>
        <script defer src="https://use.fontawesome.com/releases/v5.0.1/js/fontawesome.js"></script>

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

        
        <div class="drawer-btn-container">
            <a href="#" id="drawer-btn"><i class="fas fa-bars"></i></a>
        </div>

        <main>
            <header class="header">
                <div class="container header__container">
                    <div class="header__col-left">
                        <img class="header__logo" src="{{ asset('assets/images/logo.png') }}" />
                    </div>
                    
                    <div class="header__col-right">
                        <section class="server-feed">
                            <div class="category">
                                <h5 class="category__heading">Minecraft</h5>
                                <div class="server server--online">
                                    <div class="server__title">Survival / Creative [24/7]</div>
                                    <div class="server__players badge secondary">14/80</div>
                                    <div class="server__ip">pcbmc.co</div>
                                </div>
                                <div class="server server--offline">
                                    <div class="server__title">Feed the Beast</div>
                                    <div class="server__players badge light">Offline</div>
                                    <div class="server__ip">23.94.186.178:25565</div>
                                </div>
                                <div class="server server--offline">
                                    <div class="server__title">Pixelmon</div>
                                    <div class="server__players badge light">Offline</div>
                                    <div class="server__ip">23.94.186.178:25565</div>
                                </div>
                            </div>

                            <div class="category">
                                <h5 class="category__heading">Other Games</h5>
                                <div class="server server--online">
                                    <div class="server__title">Terraria</div>
                                    <div class="server__players badge secondary">14/80</div>
                                    <div class="server__ip">pcbmc.co</div>
                                </div>
                                <div class="server server--offline">
                                    <div class="server__title">Starbound</div>
                                    <div class="server__players badge light">Offline</div>
                                    <div class="server__ip">pcbmc.co</div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </header>

            <section>
                <div class="container contents">
                    <div class="contents__body">

                        <article class="article card">
                            <div class="article__container">
                                <h2 class="article__heading">Title</h2>
                                <div class="article__date">Tue, 5th of January, 2018</div>
                
                                <div class="article__body">
                                    Text
                                </div>
                
                                <div class="article__author">
                                    Posted by
                                    <img src="https://minotar.net/helm/_andy/16" width="16" />
                                    <a href="#">_andy</a>
                                </div>
                            </div>
                            <div class="article__footer">
                                <div class="stats">
                                    <div class="stats__item">
                                        <span class="stats__item__figure">14</span>
                                        <span class="stats__item__heading">Comments</span>
                                    </div>
                                    <div class="stats__item">
                                        <span class="stats__item__figure">831</span>
                                        <span class="stats__item__heading">Post Views</span>
                                    </div>
                                </div>
                                <div class="actions">
                                    <a class="button accent large" href="#">
                                        Read Post
                                        <i class="fa fa-chevron-right"></i>
                                    </a>
                                </div>
                            </div>
                        </article>
                            
                        <article class="article card">
                            <div class="article__container">
                                <h2 class="article__heading">Title</h2>
                                <div class="article__date">Tue, 5th of January, 2018</div>
                
                                <div class="article__body">
                                    Text
                                </div>
                
                                <div class="article__author">
                                    Posted by
                                    <img src="https://minotar.net/helm/_andy/16" width="16" />
                                    <a href="#">_andy</a>
                                </div>
                            </div>
                            <div class="article__footer">
                                <div class="stats">
                                    <div class="stats__item">
                                        <span class="stats__item__figure">14</span>
                                        <span class="stats__item__heading">Comments</span>
                                    </div>
                                    <div class="stats__item">
                                        <span class="stats__item__figure">831</span>
                                        <span class="stats__item__heading">Post Views</span>
                                    </div>
                                </div>
                                <div class="actions">
                                    <a class="button accent large" href="#">
                                        Read Post
                                        <i class="fa fa-chevron-right"></i>
                                    </a>
                                </div>
                            </div>
                        </article>

                        <article class="article card">
                            <div class="article__container">
                                <h2 class="article__heading">Title</h2>
                                <div class="article__date">Tue, 5th of January, 2018</div>
                
                                <div class="article__body">
                                    Text
                                </div>
                
                                <div class="article__author">
                                    Posted by
                                    <img src="https://minotar.net/helm/_andy/16" width="16" />
                                    <a href="#">_andy</a>
                                </div>
                            </div>
                            <div class="article__footer">
                                <div class="stats">
                                    <div class="stats__item">
                                        <span class="stats__item__figure">14</span>
                                        <span class="stats__item__heading">Comments</span>
                                    </div>
                                    <div class="stats__item">
                                        <span class="stats__item__figure">831</span>
                                        <span class="stats__item__heading">Post Views</span>
                                    </div>
                                </div>
                                <div class="actions">
                                    <a class="button accent large" href="#">
                                        Read Post
                                        <i class="fa fa-chevron-right"></i>
                                    </a>
                                </div>
                            </div>
                        </article>
                    </div>

                    <div class="contents__sidebar">
                    
                        <div class="donate-panel card primary">
                            <div class="donate-panel__padding">
                                <h3 class="donate-panel__heading">Help Keep Us Online</h3>
                                <div class="progress progress--accent">
                                    <div class="progress__bar">
                                        <div class="progress__bar__fill" style="width:25%"></div>
                                    </div>
                                    <div class="progress__markers">
                                        <span>0</span>
                                        <span>250</span>
                                        <span>500</span>
                                        <span>750</span>
                                        <span>1000</span>
                                    </div>
                                </div>
                            </div>

                            <div class="stats donate-panel__stats">
                                <div class="stats__item">
                                    <span class="stats__item__figure">${{ $donations['total'] }}</span>
                                    <span class="stats__item__heading">Funds Raised</span>
                                </div>
                                <div class="stats__item">
                                    <span class="stats__item__figure">{{ $donations['remainingDays'] }}</span>
                                    <span class="stats__item__heading">Remaining Days</span>
                                </div>
                            </div>

                            <div class="donate-panel__padding">
                                <a class="button large secondary" href="http://projectcitybuild.com/forums/index.php?topic=4124.0">
                                    Donate
                                </a>
                                <small>Donators receive a colored name, a reserved server slot and more!</small>
                            </div>
                        </div>

                        <a class="button secondary sidebar-btn" href="http://projectcitybuild.com/forums/index.php?topic=6790.0">
                            <div class="sidebar-btn__icon"><i class="fa fa-fw fa-gift"></i></div>
                            <div class="sidebar-btn__text">
                                <span class="sidebar-btn__heading">Vote For Us</span>
                                <small>Vote to receive daily in-game prizes</small>
                            </div>
                        </a>
                        <a class="button secondary sidebar-btn" href="https://wiki.projectcitybuild.com/">
                            <div class="sidebar-btn__icon">
                                <i class="fab fa-fw fa-wikipedia-w"></i>
                            </div>
                            <div class="sidebar-btn__text">
                                <span class="sidebar-btn__heading">Community Wiki</span>
                                <small>History, towns and more</small>
                            </div>
                        </a>

                        <div class="panel discord-panel">
                            <iframe src="https://discordapp.com/widget?id=161649330799902720&theme=light" 
                                width="100%" 
                                height="500" 
                                allowtransparency="true" 
                                frameborder="0">
                            </iframe>
                        </div>
                    </div>
                </div>
            </section>

            <footer class="footer">
                <div class="container footer__container">
                    <div class="footer__col-left">
                        <ul class="footer__bullets">
                            <li><h5 class="footer__subheading">Legal</h5></li>
                            <li><i class="fas fa-check-circle"></i> <a href="http://projectcitybuild.com/forums/index.php?topic=2718">Terms of Service</a></li>
                        </ul>
                        <ul class="footer__bullets">
                            <li><h5 class="footer__subheading">Open Source</h5></li>
                            <li><i class="fas fa-code-branch"></i> <a target="_blank" href="https://github.com/itsmyfirstday/PCBridge">PCBridge</a></li>
                            <li><i class="fas fa-code-branch"></i> <a target="_blank" href="https://github.com/itsmyfirstday/ProjectCityBuild">projectcitybuild.com</a></li>
                        </ul>
                    </div>
                    <div class="footer__col-right">
                        <div class="footer__social-icons">
                            <a target="_blank" href="https://www.facebook.com/ProjectCityBuild"><i class="fab fa-facebook-square"></i></a>
                            <a target="_blank" href="https://twitter.com/PCB_Minecraft"><i class="fab fa-twitter-square"></i></a>
                            <a target="_blank" href="https://www.instagram.com/projectcitybuild"><i class="fab fa-instagram"></i></a>
                            <a target="_blank" href="https://www.youtube.com/user/PCBMinecraft"><i class="fab fa-youtube"></i></a>
                            <a target="_blank" href="http://projectcitybuild.tumblr.com/"><i class="fab fa-tumblr-square"></i></a>
                            <a target="_blank" href="http://steamcommunity.com/groups/ProjectCityBuild"><i class="fab fa-steam-square"></i></a>
                        </div>
                        <a href="#top">Return to Top</a>
                    </div>

                </div>
            </footer>
        </main>

        <script src="{{ mix('assets/js/app.js') }}"></script>
        
    </body>
</html>

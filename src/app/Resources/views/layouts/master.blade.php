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

        
        <div class="drawer-btn-container">
            <a href="#" id="drawer-btn"><i class="fas fa-bars"></i></a>
        </div>

        <main>
            <header>
                <div class="container">
                    <img id="logo" src="{{ asset('assets/images/logo.png') }}" width="350" />
                    
                    <section id="server-feed">
                        <div class="category">
                            <h5>Minecraft</h5>
                            <div class="server online">
                                <div class="server-title">Survival / Creative [24/7]</div>
                                <div class="server-players badge secondary">14/80</div>
                                <div class="server-ip">pcbmc.co</div>
                            </div>
                            <div class="server offline">
                                <div class="server-title">Feed the Beast</div>
                                <div class="server-players badge light">Offline</div>
                                <div class="server-ip">23.94.186.178:25565</div>
                            </div>
                            <div class="server offline">
                                <div class="server-title">Pixelmon</div>
                                <div class="server-players badge light">Offline</div>
                                <div class="server-ip">23.94.186.178:25565</div>
                            </div>
                        </div>

                        <div class="category">
                            <h5>Other Games</h5>
                            <div class="server online">
                                <div class="server-title">Terraria</div>
                                <div class="server-players badge secondary">14/80</div>
                                <div class="server-ip">pcbmc.co</div>
                            </div>
                            <div class="server offline">
                                <div class="server-title">Starbound</div>
                                <div class="server-players badge light">Offline</div>
                                <div class="server-ip">pcbmc.co</div>
                            </div>
                        </div>
                    </section>
                    
                    <section id="slogan">
                        <h3>Creative, Community-Driven Gaming</h3>
                        Over 15,591 members and growing.

                        <a class="button large accent">Create an Account</a>
                        <a class="button large secondary">Login</a>
                    </section>
                </div>
            </header>

            <section>
                <div class="container">
                    <article class="card news-panel">
                        <div class="article-contents">
                            <h2>Title</h2>
                            <div class="date">Tue, 5th of January, 2018</div>
            
                            <div class="text">
                                Text
                            </div>
            
                            <div class="poster">
                                Posted by
                                <img src="https://minotar.net/helm/_andy/16" width="16" />
                                <a href="#">_andy</a>
                            </div>
                        </div>
                        <div class="article-footer">
                            <div class="stats">
                                <div class="stat">
                                    <h4>14</h4>
                                    <span>Comments</span>
                                </div>
                                <div class="stat">
                                    <h4>831</h4>
                                    <span>Post Views</span>
                                </div>
                            </div>
                            <div class="actions">
                                <a class="btn large orange" href="#">
                                    Read Post
                                    <i class="fa fa-chevron-right"></i>
                                </a>
                            </div>
                        </div>
                    </article>

                    <div class="card">
                        <div class="card-body">
                            <table class="table striped">
                                <thead>
                                    <tr>
                                        <td>Date</td>
                                        <td>Thread</td>
                                        <td>Latest Poster</td>
                                        <td>Replies</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Thu, 11th Jan</td>
                                        <td>Hey look, a new website!</td>
                                        <td>_andy</td>
                                        <td>13</td>
                                    </tr>
                                    <tr>
                                        <td>Thu, 11th Jan</td>
                                        <td>Hey look, a new website!</td>
                                        <td>_andy</td>
                                        <td>13</td>
                                    </tr>
                                    <tr>
                                        <td>Thu, 11th Jan</td>
                                        <td>Hey look, a new website!</td>
                                        <td>_andy</td>
                                        <td>13</td>
                                    </tr>
                                    <tr>
                                        <td>Thu, 11th Jan</td>
                                        <td>Hey look, a new website!</td>
                                        <td>_andy</td>
                                        <td>13</td>
                                    </tr>
                                    <tr>
                                        <td>Thu, 11th Jan</td>
                                        <td>Hey look, a new website!</td>
                                        <td>_andy</td>
                                        <td>13</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                        
                    <article class="card news-panel">
                        <div class="article-contents">
                            <h2>Title</h2>
                            <div class="date">Tue, 5th of January, 2018</div>
            
                            <div class="text">
                                Text
                            </div>
            
                            <div class="poster">
                                Posted by
                                <img src="https://minotar.net/helm/_andy/16" width="16" />
                                <a href="#">_andy</a>
                            </div>
                        </div>
                        <div class="article-footer">
                            <div class="stats">
                                <div class="stat">
                                    <h4>14</h4>
                                    <span>Comments</span>
                                </div>
                                <div class="stat">
                                    <h4>831</h4>
                                    <span>Post Views</span>
                                </div>
                            </div>
                            <div class="actions">
                                <a class="btn large orange" href="#">
                                    Read Post
                                    <i class="fa fa-chevron-right"></i>
                                </a>
                            </div>
                        </div>
                    </article>

                    <article class="card news-panel">
                        <div class="article-contents">
                            <h2>Title</h2>
                            <div class="date">Tue, 5th of January, 2018</div>
            
                            <div class="text">
                                Text
                            </div>
            
                            <div class="poster">
                                Posted by
                                <img src="https://minotar.net/helm/_andy/16" width="16" />
                                <a href="#">_andy</a>
                            </div>
                        </div>
                        <div class="article-footer">
                            <div class="stats">
                                <div class="stat">
                                    <h4>14</h4>
                                    <span>Comments</span>
                                </div>
                                <div class="stat">
                                    <h4>831</h4>
                                    <span>Post Views</span>
                                </div>
                            </div>
                            <div class="actions">
                                <a class="btn large orange" href="#">
                                    Read Post
                                    <i class="fa fa-chevron-right"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                </div>
            </section>

            <section>
                <div class="container">
                    <div class="card primary" id="donate-panel">
                        <div class="panel-container">
                            <h3>Help Keep Us Online</h3>
                            <div class="progressbar accent">
                                <div class="outer">
                                    <div class="inner" style="width:25%"></div>
                                </div>
                                <div class="markers">
                                    <span>0</span>
                                    <span>250</span>
                                    <span>500</span>
                                    <span>750</span>
                                    <span>1000</span>
                                </div>
                            </div>
                        </div>

                        <div class="stats">
                            <div>
                                <h4>${{ $donations['total'] }}</h4>
                                <span>Funds Raised</span>
                            </div>
                            <div>
                                <h4>{{ $donations['remainingDays'] }}</h4>
                                <span>Remaining Days</span>
                            </div>
                        </div>

                        <div class="panel-container">
                            <a class="button large secondary" href="http://projectcitybuild.com/forums/index.php?topic=4124.0">
                                Donate
                            </a>
                            <small>Donators receive a colored name, a reserved server slot and more!</small>
                        </div>
                    </div>

                    <a class="btn-divided white" href="http://projectcitybuild.com/forums/index.php?topic=6790.0">
                        <div class="icon"><i class="fa fa-fw fa-gift"></i></div>
                        <div class="text left">
                            Vote For Us
                            <small>Vote to receive daily in-game prizes</small>
                        </div>
                    </a>
                    <a class="btn-divided white" href="https://wiki.projectcitybuild.com/">
                        <div class="icon"><i class="fa fa-fw fa-wikipedia-w"></i></div>
                        <div class="text left">
                            Community Wiki
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
            </section>

            <footer>
                <div class="container">
                    <div class="left">
                        <ul>
                            <li><h5>Legal</h5></li>
                            <li><i class="fas fa-check-circle"></i> <a href="http://projectcitybuild.com/forums/index.php?topic=2718">Terms of Service</a></li>
                        </ul>
                        <ul>
                            <li><h5>Open Source</h5></li>
                            <li><i class="fas fa-code-branch"></i> <a href="https://github.com/itsmyfirstday/PCBridge" target="_blank">PCBridge</a></li>
                            <li><i class="fas fa-code-branch"></i> <a href="https://github.com/itsmyfirstday/ProjectCityBuild" target="_blank">projectcitybuild.com</a></li>
                        </ul>
                    </div>
                    <div class="right">
                        <div class="social">
                            <a href="https://www.facebook.com/ProjectCityBuild" target="_blank"><i class="fab fa-facebook-square"></i></a>
                            <a href="https://twitter.com/PCB_Minecraft" target="_blank"><i class="fab fa-twitter-square"></i></a>
                            <a href="https://www.instagram.com/projectcitybuild" target="_blank"><i class="fab fa-instagram"></i></a>
                            <a href="https://www.youtube.com/user/PCBMinecraft" target="_blank"><i class="fab fa-youtube"></i></a>
                            <a href="http://projectcitybuild.tumblr.com/" target="_blank"><i class="fab fa-tumblr-square"></i></a>
                            <a href="http://steamcommunity.com/groups/ProjectCityBuild" target="_blank"><i class="fab fa-steam-square"></i></a>
                        </div>
                        <a href="#top">Return to Top</a>
                    </div>

                </div>
            </footer>
        </main>

        <script src="{{ mix('assets/js/app.js') }}"></script>
        
    </body>
</html>

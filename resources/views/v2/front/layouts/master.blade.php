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
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

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
        <div class="logo">
            <img src="assets/images/logo.png" />
        </div>
        <ul class="nav-links">
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
                            <li><a href="http://pcbmc.co:8123/" target="_blank" rel="noopener noreferrer">Real-Time Map</a></li>
                        </ul>
                    </li>
                    <li>
                        <h5>Feed the Beast</h5>
                        <ul>
                            <li>
                                <a href="https://forums.projectcitybuild.com/t/modded-is-back-with-our-custom-false-hope-pack/34989/3">
                                    Rules & Guidelines
                                </a>
                            </li>
                            <li>
                                <a href="https://forums.projectcitybuild.com/t/modded-is-back-with-our-custom-false-hope-pack/34989/2">
                                    Installation Guide
                                </a>
                            </li>
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
                    <li class="divider"><a href="https://wiki.projectcitybuild.com/" target="_blank" rel="noopener">Community Wiki</a></li>
                    <li><a href="{{ route('front.banlist') }}">Ban List</a></li>
                </ul>
            </li>
            <li><a href="https://www.instagram.com/projectcitybuild" target="_blank" rel="noopener noreferrer">Media</a></li>
            <li><a href="{{ route('front.donate') }}">Donate</a></li>

            <li class="spacer"></li>

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

<div id="app">
    <header class="hero">
        <div class="container">
            <div class="hero__cta">
                <h1 data-aos="fade-up" data-aos-duration="750">We Build Stuff.<br />Come Join Us!</h1>
                <div class="subtitle" data-aos="fade-left" data-aos-delay="150" data-aos-duration="750">
                    One of the world's longest-running Minecraft servers; we're a <br />
                    community of creative players and city builders
                </div>

                <a href="{{ route('front.register') }}" class="button outlined" data-aos="fade-left" data-aos-delay="350" data-aos-duration="750">
                    <i class="fas fa-mouse-pointer"></i>
                    Join Now
                </a>
            </div>

            <div class="hero__server-feed">
                @foreach($servers as $server)
                    <div class="server-feed__server {{ $server->isOnline() ? 'online' : 'offline' }}">
                        <span class="server-feed__title">{{ $server->name }}</span>
                        @if($server->isOnline())
                        <span class="server-feed__player-count"><i class="fas fa-user"></i> {{ $server->status->num_of_players . '/' . $server->status->num_of_slots }}</span>
                        @endif
                        <span class="server-feed__address">
                            <a href="javascript:void(0)" data-server-address="{{ $server->address() }}">
                                <i class="fas fa-copy"></i> {{ $server->address() }}
                            </a>
                        </span>
                    </div>
                @endforeach

                <div class="server-feed__server online discord">
                    <span class="server-feed__title"><i class="fab fa-discord"></i> Discord</span>
                    <span class="server-feed__address">
                        <a href="https://discord.gg/3NYaUeScDX" target="_blank" rel="noopener noreferrer">Connect / Open</a>
                    </span>
                </div>
            </div>
        </div>
    </header>

    <main>
        <news-bar></news-bar>

        <section class="introduction">
            <div class="container" data-aos="fade-up">
                <h1>Minecraft 24/7</h1>

                <div class="introduction__content">
                    <div class="introduction__text">
                        We're a Minecraft community that has been around since 2010.<p />

                        With our free-build Creative and Survival multiplayer maps, we offer a fun platform & building experience like no other. You can visit and build in established towns & cities or start your own.
                    </div>

                    <div class="introduction__points">
                        <ul>
                            <li><i class="bullet fas fa-cube"></i> Free-build maps</li>
                            <li><i class="bullet fas fa-cube"></i> Grief protection</li>
                            <li><i class="bullet fas fa-cube"></i> Friendly community</li>
                        </ul>
                        <ul>
                            <li><i class="bullet fas fa-cube"></i> Earn trust & skill based perks</li>
                            <li><i class="bullet fas fa-cube"></i> Helpful staff</li>
                            <li><i class="bullet fas fa-cube"></i> Events and competitions</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="server-overview">
            <div class="server-overview__server">
                <div class="server-image creative" data-aos="fade-right"></div>
                <div class="server-text" data-aos="fade-left">
                    <h1>Creative</h1>

                    <div class="server-text__desc">
                        We don't believe in plots—our Creative map is the definition of freebuild.
                        Here you will find all sorts of interesting projects, from alien landscapes to cruise ships, from castles to entire cities.
                        Let your imagination run wild!
                    </div>
                    <div class="server-text__links">
                        <ul>
                            <li>
                                <a href="https://www.instagram.com/projectcitybuild" target="_blank" rel="noopener noreferrer">
                                    <i class="fas fa-chevron-right"></i> Gallery
                                </a>
                            </li>
                            <li>
                                <a href="http://pcbmc.co:8123" target="_blank"><i class="fas fa-chevron-right"></i> Real-Time Map</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="server-overview__server right">
                <div class="server-text" data-aos="fade-right">
                    <h1>Survival</h1>

                    <div class="server-text__desc">
                        We've stripped back our Survival world to be as close to the vanilla experience as possible.
                        There are no warps or teleports, and no dynamic map, so you'll have to figure out your own way.
                        It's Minecraft in its purest form.
                    </div>
                    <div class="server-text__links">
                        <ul>
                            <li>
                                <a href="https://www.instagram.com/projectcitybuild" target="_blank" rel="noopener noreferrer">
                                    <i class="fas fa-chevron-right"></i> Gallery
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="server-image survival" data-aos="fade-left"></div>
            </div>

            <div class="server-overview__server">
                <div class="server-image monarch" data-aos="fade-right"></div>
                <div class="server-text" data-aos="fade-left">
                    <h1>Monarch</h1>

                    <div class="server-text__desc">
                        Our premier city project, featuring builds at a realistic scale.
                        Over 5 years in development, Monarch showcases the talents of our most skilled builders.
                        Feel free to take a look around, and if you've got what it takes, you can make your own mark in the city.
                    </div>
                    <div class="server-text__links">
                        <ul>
                            <li>
                                <a href="https://www.instagram.com/projectcitybuild" target="_blank" rel="noopener noreferrer"><i class="fas fa-chevron-right"></i> Gallery</a>
                            </li>
                            <li>
                                <a href="http://pcbmc.co:8123"><i class="fas fa-chevron-right"></i> Real-Time Map</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="server-overview__server right">
                <div class="server-text" data-aos="fade-right">
                    <h1>Arcade</h1>

                    <div class="server-text__desc">
                        Who doesn't love minigames? In the Arcade you will find a whole host to try out.
                        Become a Spleef champion, or test out your crazy ideas in the Redstone Playground.
                        The fun never stops!
                    </div>
                    <div class="server-text__links">
                        <ul>
                            <li>
                                <i class="fas fa-chevron-right"></i> Coming Soon
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="server-image arcade" data-aos="fade-left"></div>
            </div>
        </section>

        <footer>
            <section class="footer-sitemap">
                <div class="container" data-aos="fade-up">
                    <h1>Explore More</h1>

                    <div class="footer-links">
                        <div class="footer-links__category">
                            <h2>Server</h2>
                            <ul>
                                <li><i class="bullet fas fa-cube"></i> <a href="https://forums.projectcitybuild.com/t/pcb-community-rules/22928">Rules & Guidelines</a></li>
                                <li><i class="bullet fas fa-cube"></i> <a href="https://forums.projectcitybuild.com/t/pcb-ranks/32812">Ranks</a></li>
                                <li><i class="bullet fas fa-cube"></i> <a href="https://wiki.projectcitybuild.com/wiki/List_of_Staff_Members">Staff</a></li>
                                <li><i class="bullet fas fa-cube"></i> <a href="http://pcbmc.co:8123/" target="_blank" rel="noopener noreferrer">Real-Time Map</a></li>
                            </ul>
                        </div>

                        <div class="footer-links__category">
                            <h2>Community</h2>
                            <ul>
                                <li><i class="bullet fas fa-cube"></i> <a href="{{ route('wiki') }}">Community Wiki</a></li>
                                <li><i class="bullet fas fa-cube"></i> <a href="https://forums.projectcitybuild.com/t/vote-for-our-server/18568">Vote For Our Server</a></li>
                            </ul>
                        </div>

                        <div class="footer-links__category">
                            <h2>Social Media</h2>
                            <ul>
                                <li><i class="bullet fab fa-youtube"></i> <a href="https://www.youtube.com/user/PCBMinecraft" target="_blank" rel="noopener noreferrer">YouTube</a></li>
                                <li><i class="bullet fab fa-instagram"></i> <a href="https://www.instagram.com/projectcitybuild" target="_blank" rel="noopener noreferrer">Instagram</a></li>
                                <li><i class="bullet fab fa-facebook-f"></i> <a href="https://www.facebook.com/ProjectCityBuild" target="_blank" rel="noopener noreferrer">Facebook</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <section class="footer-donations">
                <div class="container" data-aos="fade-up">
                    <div class="footer-donations__left">
                        <h1>Help Keep Us Online</h1>

                        <div class="description">
                            Donations are the only way to keep our server running.<br />
                            Donators receive perks such as flying, colored names and <a href="{{ route('front.donate') }}">much more</a>
                        </div>

                        <a class="donate-button" href="{{ route('front.donate') }}">
                            <i class="fas fa-dollar-sign"></i>
                            Donate
                        </a>
                    </div>

                    <div class="footer-donations__right">
                        <div class="donation-bar">
                            <div class="donation-bar__outer">
                                <div class="donation-bar__inner" style="width: {{ $donations['percentage'] }}%; min-width: 75px">
                                    ${{ number_format($donations['raised_this_year'], 2) }}
                                </div>
                            </div>
                            <ul class="donation-bar__indicators">
                                <li>$0</li>
                                <li>$250</li>
                                <li>$500</li>
                                <li>$750</li>
                                <li>$1000</li>
                            </ul>
                        </div>

                        <div class="spacer"></div>

                        <table class="donation-stats">
                            <tr>
                                <th>Days Remaining</th>
                                <td>{{ $donations['remaining_days'] }}</td>
                            </tr>
                            <tr>
                                <th>Funds Still Needed</th>
                                <td>${{ number_format($donations['still_required'], 2) }}</td>
                            </tr>
                            <tr>
                                <th>Raised Last Year</th>
                                <td>${{ number_format($donations['raised_last_year'], 2) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </section>

            <section class="footer-legal">
                <div class="container">
                    <ul>
                        <li><a href="{{ route('terms') }}">Terms of Service</a></li>
                        <li><a href="{{ route('privacy') }}">Privacy Policy</a></li>
                    </ul>
                </div>
            </section>
        </footer>
    </main>
</div>

<script src="https://unpkg.com/aos@next/dist/aos.js"></script>
<script>
    AOS.init({
        once: true,
    });
</script>

</body>
</html>

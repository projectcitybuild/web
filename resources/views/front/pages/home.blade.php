@extends('front.templates.master')

@section('title', 'Project City Build - Creative Minecraft Community')
@section('meta_title', 'Project City Build - Creative Minecraft Community')
@section('meta_description')
One of the world's longest-running Minecraft servers; we're a community of creative players and city builders @endsection

@section('body')
    <header class="hero" id="hero">
        <div class="container">
            <div class="hero__cta">
                <h1 data-aos="fade-up" data-aos-duration="750">We Build Stuff.<br />Come Join Us!</h1>
                <div class="subtitle" data-aos="fade-left" data-aos-delay="150" data-aos-duration="750">
                    One of the world's longest-running Minecraft servers; we're a <br />
                    community of creative players and city builders
                </div>

                <a href="{{ route('front.register') }}" class="hero__cta-button" data-aos="fade-left" data-aos-delay="350" data-aos-duration="750">
                    <i class="fas fa-mouse-pointer"></i>
                    Join Now
                </a>
            </div>

            <div class="hero__server-feed">
                @foreach($servers as $server)
                    <div class="server-feed__server {{ $server->is_online ? 'online' : 'offline' }}">
                        <span class="server-feed__title">{{ $server->name }}</span>
                        @if($server->is_online)
                            <span class="server-feed__player-count"><i class="fas fa-user"></i> {{ $server->num_of_players . '/' . $server->num_of_slots }}</span>
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
                                <a href="{{ route('front.maps') }}" target="_blank" rel="noopener noreferrer"><i class="fas fa-chevron-right"></i> Real-Time Map</a>
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
                                <a href="{{ route('front.maps') }}" target="_blank" rel="noopener noreferrer"><i class="fas fa-chevron-right"></i> Real-Time Map</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <footer>
            @include('front.components.sitemap', ['animated' => true])

            <section class="footer-donations">
                <div class="container" data-aos="fade-up">
                    <div class="footer-donations__left">
                        <h1>Help Keep Us Online</h1>

                        <div class="description">
                            Donations are the only way to keep our server running.<br />
                            Donors receive perks such as flying, colored names and <a href="{{ route('front.donate') }}">much more</a>
                        </div>

                        <a class="donate-button" href="{{ route('front.donate') }}">
                            <i class="fas fa-dollar-sign"></i>
                            Donate
                        </a>
                    </div>

                    <div class="footer-donations__right">
                        <x-donation-bar />

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
        </footer>
    </main>

    @include('front.components.footer')
@endsection

@push('end')
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init({
            once: true,
        });
    </script>
@endpush

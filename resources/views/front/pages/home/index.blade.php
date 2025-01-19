@extends('front.layouts.root-layout')

@section('title', 'Project City Build - Creative Minecraft Community')
@section('meta_title', 'Project City Build - Creative Minecraft Community')
@section('meta_description')
One of the world's longest-running Minecraft servers; we're a community of creative players and city builders @endsection

@section('body')
    <x-front::hero />

    <main>
        <section class="introduction">
            <div class="max-w-screen-2xl mx-auto px-6 md:px-12">
                <h1>Minecraft 24/7</h1>

                <div class="introduction__content">
                    <div class="introduction__text">
                        We're a Minecraft community that's been around since October 2010.<p />

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

        <section class="bg-[#F3F3F3]">
            <div class="flex flex-col md:flex-row">
                <div
                    class="
                        w-full md:w-1/2 h-[350px] md:h-[500px]
                        bg-gray-100
                        bg-cover bg-no-repeat bg-center
                        bg-[url('/resources/images/home_creative.png')]
                    "
                ></div>

                <div class="md:w-1/2 px-12 py-24 md:p-20 flex flex-col justify-center">
                    <h1 class="font-display text-6xl">Creative</h1>

                    <div class="mt-3 text-gray-700 leading-8 text-sm">
                        We don't believe in plots—our Creative map is the definition of freebuild.
                        Here you will find all sorts of interesting projects, from alien landscapes to cruise ships, from castles to entire cities.
                        Let your imagination run wild!
                    </div>

                    <ul class="mt-8 flex gap-4 text-sm font-bold">
                        <li>
                            <a href="https://www.instagram.com/projectcitybuild" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                    <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                </svg>
                                Gallery
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('front.maps') }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                    <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                </svg>
                                Real-Time Map
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="flex flex-col-reverse md:flex-row">
                <div class="md:w-1/2 px-12 py-24 md:p-20 flex flex-col justify-center md:text-right">
                    <h1 class="font-display text-6xl">Survival</h1>

                    <div class="mt-3 text-gray-700 leading-8 text-sm">
                        We've stripped back our Survival world to be as close to the vanilla experience as possible.
                        There are no warps or teleports, and no dynamic map, so you'll have to figure out your own way.
                        It's Minecraft in its purest form.
                    </div>

                    <ul class="mt-8 flex justify-end gap-4 text-sm font-bold">
                        <li>
                            <a href="https://www.instagram.com/projectcitybuild" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                    <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                </svg>
                                Gallery
                            </a>
                        </li>
                    </ul>
                </div>

                <div
                    class="
                        w-full md:w-1/2 h-[350px] md:h-[500px]
                        bg-gray-100
                        bg-cover bg-no-repeat bg-center
                        bg-[url('/resources/images/home_survival.png')]
                    "
                ></div>
            </div>

            <div class="flex flex-col md:flex-row">
                <div
                    class="
                        w-full md:w-1/2 h-[350px] md:h-[500px]
                        bg-gray-100
                        bg-cover bg-no-repeat bg-center
                        bg-[url('/resources/images/home_monarch.png')]
                    "
                ></div>

                <div class="md:w-1/2 px-12 py-24 md:p-20 flex flex-col justify-center">
                    <h1 class="font-display text-6xl">Monarch</h1>

                    <div class="mt-3 text-gray-700 leading-8 text-sm">
                        Our premier city project, featuring builds at a realistic scale.
                        Over 5 years in development, Monarch showcases the talents of our most skilled builders.
                        Feel free to take a look around, and if you've got what it takes, you can make your own mark in the city.
                    </div>

                    <ul class="mt-8 flex gap-4 text-sm font-bold">
                        <li>
                            <a href="https://www.instagram.com/projectcitybuild" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                    <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                </svg>
                                Gallery
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('front.maps') }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                    <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                </svg>
                                Real-Time Map
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <footer>
            @include('front.components.sitemap', ['animated' => true])

            <section class="footer-donations">
                <div class="max-w-screen-2xl mx-auto px-6 md:px-12 flex">
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

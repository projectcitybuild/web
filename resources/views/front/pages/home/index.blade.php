@extends('front.layouts.root-layout')

@section('title', 'Project City Build - Creative Minecraft Community')
@section('meta_title', 'Project City Build - Creative Minecraft Community')
@section('meta_description')
One of the world's longest-running Minecraft servers; we're a community of creative players and city builders @endsection

@section('body')
    <x-front::hero />

    <main>
        <section class="bg-white">
            <div class="max-w-screen-2xl mx-auto p-12">
                <div class="flex flex-col gap-4 md:flex-row md:gap-12 md:py-8">
                    <div class="mb-4 text-4xl font-light leading-relaxed">
                        We're a Minecraft server and community that's been around <strong class="font-bold">since October 2010</strong>.
                    </div>

                    <div class="text-gray-600 text-lg leading-loose pt-3">
                        With our free-build Creative and Survival multiplayer worlds, we offer a fun platform & building
                        experience like no other. You can visit and build in established towns & cities or start your own.
                    </div>
                </div>

                <ul class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <li class="rounded-lg bg-gray-50 p-6 text-lg">
                        <svg class="size-12 text-gray-600 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3M3.22302 14C4.13247 18.008 7.71683 21 12 21c4.9706 0 9-4.0294 9-9 0-4.97056-4.0294-9-9-9-3.72916 0-6.92858 2.26806-8.29409 5.5M7 9H3V5"/>
                        </svg>

                        <h2 class="mt-4 text-4xl font-display">Minecraft 24/7</h2>
                        <span class="text-sm text-gray-600">Online all day, every day since the beginning</span>
                    </li>
                    <li class="rounded-lg bg-gray-50 p-6 text-lg">
                        <svg class="size-12 text-gray-600 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 4H4m0 0v4m0-4 5 5m7-5h4m0 0v4m0-4-5 5M8 20H4m0 0v-4m0 4 5-5m7 5h4m0 0v-4m0 4-5-5"/>
                        </svg>
                        <h2 class="mt-4 text-4xl font-display">Free-Build Worlds</h2>
                        <span class="text-sm text-gray-600">No plots, multiple build worlds, improved world generation</span>
                    </li>
                    <li class="rounded-lg bg-gray-50 p-6 text-lg">
                        <svg class="size-12 text-gray-600 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.5 11.5 11 13l4-3.5M12 20a16.405 16.405 0 0 1-5.092-5.804A16.694 16.694 0 0 1 5 6.666L12 4l7 2.667a16.695 16.695 0 0 1-1.908 7.529A16.406 16.406 0 0 1 12 20Z"/>
                        </svg>
                        <h2 class="mt-4 text-4xl font-display">Grief Protection</h2>
                        <span class="text-sm text-gray-600">Block logging and various grief-detection methods in place</span>
                    </li>
                    <li class="rounded-lg bg-gray-50 p-6 text-lg">
                        <svg class="size-12 text-gray-600 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12.01 6.001C6.5 1 1 8 5.782 13.001L12.011 20l6.23-7C23 8 17.5 1 12.01 6.002Z"/>
                        </svg>
                        <h2 class="mt-4 text-4xl font-display">Community Focussed</h2>
                        <span class="text-sm text-gray-600">Build together, optional role-play options, community events, build showcases and more. Join our <a href="{{ config('discord.invite_url') }}" class="text-blue-500">Discord</a>!</span>
                    </li>
                    <li class="rounded-lg bg-gray-50 p-6 text-lg">
                        <svg class="size-12 text-gray-600 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 9h.01M8.99 9H9m12 3a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM6.6 13a5.5 5.5 0 0 0 10.81 0H6.6Z"/>
                        </svg>
                        <h2 class="mt-4 text-4xl font-display">Trust & Build Ranks</h2>
                        <span class="text-sm text-gray-600">Two ladders of ranks. Access to powerful build tools with build ranks; more QOL and cosmetics with trust ranks</span>
                    </li>
                    <li class="rounded-lg bg-gray-50 p-6 text-lg">
                        <svg class="size-12 text-gray-600 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m7.171 12.906-2.153 6.411 2.672-.89 1.568 2.34 1.825-5.183m5.73-2.678 2.154 6.411-2.673-.89-1.568 2.34-1.825-5.183M9.165 4.3c.58.068 1.153-.17 1.515-.628a1.681 1.681 0 0 1 2.64 0 1.68 1.68 0 0 0 1.515.628 1.681 1.681 0 0 1 1.866 1.866c-.068.58.17 1.154.628 1.516a1.681 1.681 0 0 1 0 2.639 1.682 1.682 0 0 0-.628 1.515 1.681 1.681 0 0 1-1.866 1.866 1.681 1.681 0 0 0-1.516.628 1.681 1.681 0 0 1-2.639 0 1.681 1.681 0 0 0-1.515-.628 1.681 1.681 0 0 1-1.867-1.866 1.681 1.681 0 0 0-.627-1.515 1.681 1.681 0 0 1 0-2.64c.458-.361.696-.935.627-1.515A1.681 1.681 0 0 1 9.165 4.3ZM14 9a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z"/>
                        </svg>
                        <h2 class="mt-4 text-4xl font-display">Events and Competitions</h2>
                        <span class="text-sm text-gray-600">Build competitions, server events and more. Rewards for both winners and participants</span>
                    </li>
                </ul>
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

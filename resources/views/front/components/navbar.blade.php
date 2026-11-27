@props([
    'variant' => 'opaque',
    'account' => false,
])

<nav
    data-sticky-navbar
    data-variant="{{ $variant }}"
    class="front-navbar border-white/65 relative z-50 w-full {{ $variant != 'opaque' ? 'border-b' : 'bg-gray-900/90 backdrop-blur-sm' }}"
>
    <div class="max-w-screen-2xl flex flex-wrap items-center justify-between mx-auto p-4">
        <x-logo />

        <div class="flex md:order-2 items-center gap-2 md:gap-3">
            @auth
                <div class="relative">
                    <button type="button" class="flex text-sm bg-gray-800 rounded-full md:me-0" id="user-menu-button" aria-expanded="false" aria-controls="user-dropdown" data-menu-toggle-target="user-dropdown" data-menu-dismiss="outside">
                        <span class="sr-only">Open user menu</span>
                        <x-avatar />
                    </button>
                    <!-- Dropdown menu -->
                    <div class="absolute right-0 top-full z-50 hidden my-4 text-base list-none bg-gray-800 divide-y divide-gray-700 rounded-lg shadow" id="user-dropdown">
                        <div class="px-4 py-3">
                            <span class="block text-sm text-gray-100">{{ Auth::user()->username }}</span>
                            <span class="block text-sm text-gray-400 truncate">{{ Auth::user()->email }}</span>
                        </div>
                        <ul class="py-2" aria-labelledby="user-menu-button">
                            @unless($account)
                            <li>
                                <a href="{{ route('front.account.profile') }}" class="block px-4 py-2 text-sm text-gray-200 hover:bg-gray-700">Dashboard</a>
                            </li>
                            @endunless
                            @can('access-manage')
                                <li>
                                    <a href="{{ route('manage.index') }}" class="block px-4 py-2 text-sm text-gray-200 hover:bg-gray-700">Management</a>
                                </li>
                            @endcan
                            @can('access-review')
                                <li>
                                    <a href="{{ route('review.index') }}" class="block px-4 py-2 text-sm text-gray-200 hover:bg-gray-700">Review</a>
                                </li>
                            @endcan
                            @unless($account)
                            <li>
                                <a href="{{ route('front.account.settings') }}" class="block px-4 py-2 text-sm text-gray-200 hover:bg-gray-700">Settings</a>
                            </li>
                            @endunless
                        </ul>
                        <ul class="py-2" aria-labelledby="user-menu-button">
                            <li>
                                <a href="{{ route('front.logout') }}" class="block px-4 py-2 text-sm text-gray-200 hover:bg-gray-700">Sign out</a>
                            </li>
                        </ul>
                    </div>
                </div>
            @else
                <a href="{{ route('front.login') }}" class="rounded-md text-sm px-6 py-3 text-center bg-white text-gray-900 hover:bg-gray-200">
                    Sign In
                </a>
            @endauth

            <button data-menu-toggle-target="mega-menu-full" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-300 rounded-lg md:hidden hover:bg-gray-700 focus:outline-hidden focus:ring-2 focus:ring-gray-600" aria-controls="mega-menu-full" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
                </svg>
            </button>
        </div>

        <div
            id="mega-menu-full"
            class="hidden max-h-[calc(100dvh-5rem)] w-full overflow-y-auto pb-2 md:flex md:max-h-none md:w-auto md:overflow-visible md:pb-0 md:order-1"
        >
            <ul
                class="mt-3 flex flex-col gap-1 rounded-2xl border border-gray-700/80 bg-gray-900/95 p-2 font-medium md:mt-0 md:flex-row md:items-center md:gap-0 md:space-x-8 md:rounded-none md:border-0 md:bg-transparent md:p-0"
            >
                @if($account)
                    <li>
                        <a
                            href="{{ route('front.account.profile') }}"
                            class="block py-2 px-3 text-gray-100 rounded md:bg-transparent md:p-0 {{ request()->is('account') ? 'bg-gray-700 md:bg-transparent md:text-blue-300' : '' }}"
                            @if(request()->is('account')) aria-current="page" @endif
                        >Dashboard</a>
                    </li>
                    <li>
                        <a
                            href="{{ route('front.account.games') }}"
                            class="block py-2 px-3 text-gray-100 rounded hover:bg-gray-700 md:hover:bg-transparent md:hover:text-blue-300 md:p-0 {{ request()->is('account/games') ? 'bg-gray-700 md:bg-transparent md:text-blue-300' : '' }}"
                            @if(request()->is('account/games')) aria-current="page" @endif
                        >Player</a>
                    </li>
                    <li>
                        <a
                            href="{{ route('front.account.donations') }}"
                            class="block py-2 px-3 text-gray-100 rounded hover:bg-gray-700 md:hover:bg-transparent md:hover:text-blue-300 md:p-0 {{ request()->is('account/donations') ? 'bg-gray-700 md:bg-transparent md:text-blue-300' : '' }}"
                            @if(request()->is('account/donations')) aria-current="page" @endif
                        >Donations</a>
                    </li>
                    <li>
                        <a
                            href="{{ route('front.account.records') }}"
                            class="block py-2 px-3 text-gray-100 rounded hover:bg-gray-700 md:hover:bg-transparent md:hover:text-blue-300 md:p-0 {{ request()->is('account/records') ? 'bg-gray-700 md:bg-transparent md:text-blue-300' : '' }}"
                            @if(request()->is('account/records')) aria-current="page" @endif
                        >Records</a>
                    </li>
                    <li>
                        <a
                            href="{{ route('front.account.settings') }}"
                            class="block py-2 px-3 text-gray-100 rounded hover:bg-gray-700 md:hover:bg-transparent md:hover:text-blue-300 md:p-0 {{ request()->is('account/settings/*') ? 'bg-gray-700 md:bg-transparent md:text-blue-300' : '' }}"
                            @if(request()->is('account/settings/*')) aria-current="page" @endif
                        >Settings</a>
                    </li>
                @else
                <li>
                    <a
                        href="{{ route('front.home') }}"
                        class="block rounded-xl px-4 py-3 text-gray-100 transition-colors hover:bg-white/10 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-300 md:rounded-md md:px-0 md:py-2 md:text-gray-50 md:hover:bg-transparent md:hover:text-gray-300"
                    >Home</a>
                </li>
                <li>
                    <a
                        href="{{ route('portal') }}"
                        class="block rounded-xl px-4 py-3 text-gray-100 transition-colors hover:bg-white/10 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-300 md:rounded-md md:px-0 md:py-2 md:text-gray-50 md:hover:bg-transparent md:hover:text-gray-300"
                    >Portal</a>
                </li>
                <li>
                    <a
                        href="{{ route('front.maps') }}"
                        class="block rounded-xl px-4 py-3 text-gray-100 transition-colors hover:bg-white/10 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-300 md:rounded-md md:px-0 md:py-2 md:text-gray-50 md:hover:bg-transparent md:hover:text-gray-300"
                    >Live Maps</a>
                </li>
                <li>
                    <a
                        href="{{ route('vote') }}"
                        class="block rounded-xl px-4 py-3 text-gray-100 transition-colors hover:bg-white/10 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-300 md:rounded-md md:px-0 md:py-2 md:text-gray-50 md:hover:bg-transparent md:hover:text-gray-300"
                    >Vote For Us</a>
                </li>
                <li>
                    <a
                        href="{{ route('front.donate') }}"
                        class="block rounded-xl px-4 py-3 text-gray-100 transition-colors hover:bg-white/10 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-300 md:rounded-md md:px-0 md:py-2 md:text-gray-50 md:hover:bg-transparent md:hover:text-gray-300"
                    >Donate</a>
                </li>
                <li>
                    <button
                        type="button"
                        id="mega-menu-full-dropdown-button"
                        data-menu-toggle-target="mega-menu-full-dropdown"
                        data-menu-dismiss="outside"
                        aria-controls="mega-menu-full-dropdown"
                        aria-expanded="false"
                        class="flex min-h-12 w-full items-center justify-between rounded-xl px-4 py-3 text-gray-100 transition-colors hover:bg-white/10 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-300 md:w-auto md:rounded-md md:px-0 md:py-2 md:text-gray-50 md:hover:bg-transparent md:hover:text-gray-300"
                    >
                        More
                        <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                        </svg>
                    </button>
                </li>
                @endif
            </ul>

            @unless($account)
            <div id="mega-menu-full-dropdown" aria-labelledby="mega-menu-full-dropdown-button" class="hidden mt-3 w-full rounded-2xl border border-gray-700/80 bg-gray-800/60 p-3 backdrop-blur-xl md:absolute md:left-0 md:top-full md:mt-4 md:rounded-3xl md:bg-gray-900/95 md:p-6 md:shadow-2xl">
                <div class="grid max-w-screen-xl grid-cols-1 gap-4 mx-auto text-gray-100 sm:grid-cols-2 lg:grid-cols-3">
            <ul>
                <li class="mb-2 px-3 text-xs font-semibold tracking-[0.18em] text-gray-400 uppercase">About</li>
                <li>
                    <a href="{{ route('rules') }}" class="block rounded-xl px-3 py-2.5 transition-colors hover:bg-white/5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-300">
                        <div class="font-semibold">Rules & Guidelines</div>
                        <span class="text-sm text-gray-400">Terms for using our services</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('ranks') }}" class="block rounded-xl px-3 py-2.5 transition-colors hover:bg-white/5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-300">
                        <div class="font-semibold">Ranks</div>
                        <span class="text-sm text-gray-400">List of all attainable player ranks</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('staff') }}" class="block rounded-xl px-3 py-2.5 transition-colors hover:bg-white/5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-300">
                        <div class="font-semibold">Staff</div>
                        <span class="text-sm text-gray-400">List of all current and past volunteers</span>
                    </a>
                </li>
            </ul>
            <ul class="border-t border-gray-700/70 pt-3 sm:border-0 sm:pt-0">
                <li class="mb-2 px-3 text-xs font-semibold tracking-[0.18em] text-gray-400 uppercase">Explore</li>
                <li>
                    <a href="{{ route('map-archive') }}" class="block rounded-xl px-3 py-2.5 transition-colors hover:bg-white/5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-300">
                        <div class="font-semibold flex gap-1 items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                <path fill-rule="evenodd" d="M2 3a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H2Zm0 4.5h16l-.811 7.71a2 2 0 0 1-1.99 1.79H4.802a2 2 0 0 1-1.99-1.79L2 7.5ZM10 9a.75.75 0 0 1 .75.75v2.546l.943-1.048a.75.75 0 1 1 1.114 1.004l-2.25 2.5a.75.75 0 0 1-1.114 0l-2.25-2.5a.75.75 0 1 1 1.114-1.004l.943 1.048V9.75A.75.75 0 0 1 10 9Z" clip-rule="evenodd" />
                            </svg>
                            Map Archive
                        </div>
                        <span class="text-sm text-gray-400">Download server maps no longer available</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('front.banlist') }}" class="block rounded-xl px-3 py-2.5 transition-colors hover:bg-white/5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-300">
                        <div class="font-semibold flex gap-1 items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                <path fill-rule="evenodd" d="m5.965 4.904 9.131 9.131a6.5 6.5 0 0 0-9.131-9.131Zm8.07 10.192L4.904 5.965a6.5 6.5 0 0 0 9.131 9.131ZM4.343 4.343a8 8 0 1 1 11.314 11.314A8 8 0 0 1 4.343 4.343Z" clip-rule="evenodd" />
                            </svg>
                            Banned Players
                        </div>
                        <span class="text-sm text-gray-400">List of players banned from our services</span>
                    </a>
                </li>
                <li class="mt-4 px-3 pt-3 text-xs font-semibold tracking-[0.18em] text-gray-400 uppercase">
                    Help
                </li>
                <li>
                    <a href="{{ route('front.contact') }}" class="block rounded-xl px-3 py-2.5 transition-colors hover:bg-white/5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-300">
                        <div class="font-semibold flex gap-1 items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                            Contact Us
                        </div>
                        <span class="text-sm text-gray-400">For general inquiries</span>
                    </a>
                </li>
            </ul>
            <ul class="border-t border-gray-700/70 pt-3 sm:border-0 sm:pt-0">
                <li class="mb-2 px-3 text-xs font-semibold tracking-[0.18em] text-gray-400 uppercase">Get involved</li>
                <li>
                    <a href="{{ route('report') }}" class="block rounded-xl px-3 py-2.5 transition-colors hover:bg-white/5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-300">
                        <div class="font-semibold">Report a Player</div>
                        <span class="text-sm text-gray-400">Notify staff of player misconduct</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('front.appeal') }}" class="block rounded-xl px-3 py-2.5 transition-colors hover:bg-white/5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-300">
                        <div class="font-semibold">Appeal a Ban</div>
                        <span class="text-sm text-gray-400">Request the removal of a player ban</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('front.rank-up') }}" class="block rounded-xl px-3 py-2.5 transition-colors hover:bg-white/5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-300">
                        <div class="font-semibold">Apply for Build Rank</div>
                        <span class="text-sm text-gray-400">Submit an application for a higher build rank</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('staff-apply') }}" class="block rounded-xl px-3 py-2.5 transition-colors hover:bg-white/5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-300">
                        <div class="font-semibold">Apply for Staff</div>
                        <span class="text-sm text-gray-400">Volunteer for the staff team</span>
                    </a>
                </li>
            </ul>
                </div>
            </div>
            @endunless
        </div>
    </div>
</nav>

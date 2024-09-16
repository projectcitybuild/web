@props([
    'variant' => 'opaque',
])

<nav class="border-gray-200 border-opacity-65 absolute md:relative w-full {{ $variant != 'opaque' ? 'border-b' : 'bg-[#362f2b]' }}">
    <div class="max-w-screen-2xl flex flex-wrap items-center justify-between mx-auto p-4">
        <x-logo />

        <div class="flex md:order-2 space-x-3 md:space-x-0 items-center">
            @auth
                <button type="button" class="flex text-sm bg-gray-50 rounded-full md:me-0" id="user-menu-button" aria-expanded="false" data-dropdown-toggle="user-dropdown" data-dropdown-placement="bottom">
                    <span class="sr-only">Open user menu</span>
                    <x-avatar />
                </button>
                <!-- Dropdown menu -->
                <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow" id="user-dropdown">
                    <div class="px-4 py-3">
                        <span class="block text-sm text-gray-900">{{ Auth::user()->username }}</span>
                        <span class="block text-sm  text-gray-500 truncate">{{ Auth::user()->email }}</span>
                    </div>
                    <ul class="py-2" aria-labelledby="user-menu-button">
                        <li>
                            <a href="{{ route('front.account.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                        </li>
                        @if (Auth::user()->canAccessPanel())
                            <li>
                                <a href="{{ route('front.panel.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Staff Panel</a>
                            </li>
                        @endif
                    </ul>
                    <ul class="py-2" aria-labelledby="user-menu-button">
                        <li>
                            <a href="{{ route('front.logout') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sign out</a>
                        </li>
                    </ul>
                </div>
            @else
                <a href="{{ route('front.login') }}" class="text-gray-900 bg-gray-50 hover:bg-gray-200 rounded-md text-sm px-6 py-3 text-center">
                    Sign In
                </a>
            @endauth

            <button data-collapse-toggle="mega-menu-full" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200" aria-controls="navbar-cta" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
                </svg>
            </button>
        </div>

        <div
            id="mega-menu-full"
            class="
                hidden items-center justify-between w-full
                md:flex md:w-auto md:order-1
            "
        >
            <ul
                class="
                    flex flex-col font-medium p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50
                    md:space-x-8 md:flex-row md:mt-0 md:border-0 md:bg-transparent
                "
            >
                <li>
                    <a
                        href="{{ route('front.home') }}"
                        class="
                            block py-2 px-3 md:p-0 text-gray-900 rounded hover:bg-gray-100
                            md:text-gray-50 md:hover:bg-transparent md:hover:text-gray-300
                        "
                    >Home</a>
                </li>
                <li>
                    <a
                        href="https://portal.projectcitybuild.com/"
                        class="
                            block py-2 px-3 md:p-0 text-gray-900 rounded hover:bg-gray-100
                            md:text-gray-50 md:hover:bg-transparent md:hover:text-gray-300
                        "
                    >Portal</a>
                </li>
                <li>
                    <a
                        href="{{ route('front.maps') }}"
                        class="
                            block py-2 px-3 md:p-0 text-gray-900 rounded hover:bg-gray-100
                            md:text-gray-50 md:hover:bg-transparent md:hover:text-gray-300
                        "
                    >Live Maps</a>
                </li>
                <li>
                    <a
                        href="{{ route('vote') }}"
                        class="
                            block py-2 px-3 md:p-0 text-gray-900 rounded hover:bg-gray-100
                            md:text-gray-50 md:hover:bg-transparent md:hover:text-gray-300
                        "
                    >Vote For Us</a>
                </li>
                <li>
                    <a
                        href="{{ route('front.donate') }}"
                        class="
                            block py-2 px-3 md:p-0 text-gray-900 rounded hover:bg-gray-100
                            md:text-gray-50 md:hover:bg-transparent md:hover:text-gray-300
                        "
                    >Donate</a>
                </li>
                <li>
                    <button
                        id="mega-menu-full-dropdown-button"
                        data-collapse-toggle="mega-menu-full-dropdown"
                        class="
                            flex items-center justify-between w-full py-2 px-3 text-gray-900 rounded
                            md:text-gray-50 md:w-auto hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-gray-300 md:p-0
                        "
                    >
                        More
                        <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                        </svg>
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <div id="mega-menu-full-dropdown" class="hidden shadow-xl md:shadow-sm bg-gray-50 w-full absolute bg-white/90 backdrop-blur-md">
        <div class="grid max-w-screen-xl px-4 py-5 mx-auto text-gray-900 sm:grid-cols-3 md:px-6 md:py-8">
            <ul>
                <li>
                    <a href="{{ route('rules') }}" class="block p-3 rounded-lg hover:bg-gray-100">
                        <div class="font-semibold">Rules & Guidelines</div>
                        <span class="text-sm text-gray-500">Terms for using our services</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('ranks') }}" class="block p-3 rounded-lg hover:bg-gray-100">
                        <div class="font-semibold">Ranks</div>
                        <span class="text-sm text-gray-500">List of all attainable player ranks</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('staff') }}" class="block p-3 rounded-lg hover:bg-gray-100">
                        <div class="font-semibold flex gap-2 items-center">Staff</div>
                        <span class="text-sm text-gray-500">List of all current and past volunteers</span>
                    </a>
                </li>
            </ul>
            <ul>
                <li>
                    <a href="{{ route('front.3d-map') }}" class="block p-3 rounded-lg hover:bg-gray-100">
                        <div class="font-semibold">3D Map</div>
                        <span class="text-sm text-gray-500">View the interactive map of Monarch</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('map-archive') }}" class="block p-3 rounded-lg hover:bg-gray-100">
                        <div class="font-semibold">Map Archive</div>
                        <span class="text-sm text-gray-500">Download server maps no longer available</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('front.banlist') }}" class="block p-3 rounded-lg hover:bg-gray-100">
                        <div class="font-semibold">Ban List</div>
                        <span class="text-sm text-gray-500">List of players banned from our services</span>
                    </a>
                </li>
            </ul>
            <ul>
                <li>
                    <a href="{{ route('report') }}" class="block p-3 rounded-lg hover:bg-gray-100">
                        <div class="font-semibold">Report a Player</div>
                        <span class="text-sm text-gray-500">Notify staff of player misconduct</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('front.appeal') }}" class="block p-3 rounded-lg hover:bg-gray-100">
                        <div class="font-semibold">Appeal a Ban</div>
                        <span class="text-sm text-gray-500">Request the removal of a player ban</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('front.rank-up') }}" class="block p-3 rounded-lg hover:bg-gray-100">
                        <div class="font-semibold">Apply for Build Rank</div>
                        <span class="text-sm text-gray-500">Submit an application for a higher build rank</span>
                    </a>
                </li>
                <li>
                    <a href="https://goo.gl/forms/UodUsKQBZJdCzNWk1" class="block p-3 rounded-lg hover:bg-gray-100">
                        <div class="font-semibold">Apply for Staff</div>
                        <span class="text-sm text-gray-500">Volunteer for the staff team</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

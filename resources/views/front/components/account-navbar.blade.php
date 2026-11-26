@php
    $uuid = Auth::user()->minecraftAccount->first()?->uuid;
@endphp

<nav class="bg-gray-900 border-gray-700">
    <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
        <x-logo />

        <div class="flex items-center md:order-2 space-x-3 md:space-x-0">
            <button type="button" class="flex text-sm bg-gray-800 rounded-full md:me-0 focus:ring-4 focus:ring-gray-300" id="user-menu-button" aria-expanded="false" data-dropdown-toggle="user-dropdown" data-dropdown-placement="bottom">
                <span class="sr-only">Open user menu</span>
                <x-avatar />
            </button>
            <!-- Dropdown menu -->
            <div class="z-50 hidden my-4 text-base list-none bg-gray-800 divide-y divide-gray-700 rounded-lg shadow" id="user-dropdown">
                <div class="px-4 py-3">
                    <span class="block text-sm text-gray-100">{{ Auth::user()->username }}</span>
                    <span class="block text-sm text-gray-400 truncate">{{ Auth::user()->email }}</span>
                </div>
                <ul class="py-2" aria-labelledby="user-menu-button">
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
                </ul>
                <ul class="py-2" aria-labelledby="user-menu-button">
                    <li>
                        <a href="{{ route('front.logout') }}" class="block px-4 py-2 text-sm text-gray-200 hover:bg-gray-700">Sign out</a>
                    </li>
                </ul>
            </div>
            <button data-collapse-toggle="navbar-user" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-300 rounded-lg md:hidden hover:bg-gray-700 focus:outline-hidden focus:ring-2 focus:ring-gray-600" aria-controls="navbar-user" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
                </svg>
            </button>
        </div>
        <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-user">
            <ul class="flex flex-col font-medium p-4 md:p-0 mt-4 border border-gray-700 rounded-lg bg-gray-800 md:space-x-8 md:flex-row md:mt-0 md:border-0 md:bg-gray-900">
                <li>
                    <a
                        href="{{ route('front.account.profile') }}"
                        class="block py-2 px-3 text-gray-100 rounded md:bg-transparent md:p-0 {{ request()->is('account') ? 'bg-gray-700 md:bg-transparent md:text-blue-300' : '' }}"
                        aria-current="page"
                    >Dashboard</a>
                </li>
                <li>
                    <a
                        href="{{ route('front.account.games') }}"
                        class="block py-2 px-3 text-gray-100 rounded hover:bg-gray-700 md:hover:bg-transparent md:hover:text-blue-300 md:p-0 {{ request()->is('account/games') ? 'bg-gray-700 md:bg-transparent md:text-blue-300' : '' }}"
                    >Player</a>
                </li>
                <li>
                    <a
                        href="{{ route('front.account.donations') }}"
                        class="block py-2 px-3 text-gray-100 rounded hover:bg-gray-700 md:hover:bg-transparent md:hover:text-blue-300 md:p-0 {{ request()->is('account/donations') ? 'bg-gray-700 md:bg-transparent md:text-blue-300' : '' }}"
                    >Donations</a>
                </li>
                <li>
                    <a
                        href="{{ route('front.account.records') }}"
                        class="block py-2 px-3 text-gray-100 rounded hover:bg-gray-700 md:hover:bg-transparent md:hover:text-blue-300 md:p-0 {{ request()->is('account/records') ? 'bg-gray-700 md:bg-transparent md:text-blue-300' : '' }}"
                    >Records</a>
                </li>
                <li>
                    <a
                        href="{{ route('front.account.settings') }}"
                        class="block py-2 px-3 text-gray-100 rounded hover:bg-gray-700 md:hover:bg-transparent md:hover:text-blue-300 md:p-0 {{ request()->is('account/settings/*') ? 'bg-gray-700 md:bg-transparent md:text-blue-300' : '' }}"
                    >Settings</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

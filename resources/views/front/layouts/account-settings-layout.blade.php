@extends('front.layouts.root-layout')

@section('body')
    <x-account-navbar />

    <main
        class="
            flex flex-col max-w-screen-xl
            md:flex-row md:mx-auto md:mt-8
        "
    >
        <div class="p-6 min-w-80">
            <h2 class="text-sm text-gray-500 mb-3">Profile</h2>

            <a
                href="{{ route('front.account.settings.username') }}"
                class="
                    flex flex-row gap-2 items-center p-2 rounded-lg
                    {{ request()->is('account/settings/username') ? 'bg-gray-200 font-bold' : '' }}
                "
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                    <path d="m5.433 13.917 1.262-3.155A4 4 0 0 1 7.58 9.42l6.92-6.918a2.121 2.121 0 0 1 3 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 0 1-.65-.65Z" />
                    <path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0 0 10 3H4.75A2.75 2.75 0 0 0 2 5.75v9.5A2.75 2.75 0 0 0 4.75 18h9.5A2.75 2.75 0 0 0 17 15.25V10a.75.75 0 0 0-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5Z" />
                </svg>
                <span class="text-gray-800">Username</span>
            </a>

            <h2 class="text-sm text-gray-500 mb-3 mt-6">Security</h2>

            <a
                href="{{ route('front.account.settings.email') }}"
                class="
                    flex flex-row gap-2 items-center p-2 rounded-lg
                    {{ request()->is('account/settings/email') ? 'bg-gray-200 font-bold' : '' }}
                "
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                    <path d="M3 4a2 2 0 0 0-2 2v1.161l8.441 4.221a1.25 1.25 0 0 0 1.118 0L19 7.162V6a2 2 0 0 0-2-2H3Z" />
                    <path d="m19 8.839-7.77 3.885a2.75 2.75 0 0 1-2.46 0L1 8.839V14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8.839Z" />
                </svg>
                <span class="text-gray-800">Email Address</span>
            </a>

            <a
                href="{{ route('front.account.settings.password') }}"
                class="
                    flex flex-row gap-2 items-center p-2 rounded-lg
                    {{ request()->is('account/settings/password') ? 'bg-gray-200 font-bold' : '' }}
                "
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                    <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z" clip-rule="evenodd" />
                </svg>
                <span class="text-gray-800">Password</span>
            </a>

            <a
                href="{{ route('front.account.settings.mfa') }}"
                class="
                    flex flex-row gap-2 items-center p-2 rounded-lg
                    {{ request()->is('account/settings/mfa') ? 'bg-gray-200 font-bold' : '' }}
                "
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                    <path d="M8 16.25a.75.75 0 0 1 .75-.75h2.5a.75.75 0 0 1 0 1.5h-2.5a.75.75 0 0 1-.75-.75Z" />
                    <path fill-rule="evenodd" d="M4 4a3 3 0 0 1 3-3h6a3 3 0 0 1 3 3v12a3 3 0 0 1-3 3H7a3 3 0 0 1-3-3V4Zm4-1.5v.75c0 .414.336.75.75.75h2.5a.75.75 0 0 0 .75-.75V2.5h1A1.5 1.5 0 0 1 14.5 4v12a1.5 1.5 0 0 1-1.5 1.5H7A1.5 1.5 0 0 1 5.5 16V4A1.5 1.5 0 0 1 7 2.5h1Z" clip-rule="evenodd" />
                </svg>
                <span class="text-gray-800 flex-grow">2-Factor Authentication</span>

                @if (Auth::user()->is_totp_enabled)
                    <div class="bg-green-500 rounded-md text-xs text-white py-1 px-2 font-normal">ON</div>
                @else
                    <div class="bg-gray-300 rounded-md text-xs text-white py-1 px-2 font-normal">OFF</div>
                @endif
            </a>

            <h2 class="text-sm text-gray-500 mb-3 mt-6">Billing</h2>

            <a
                href="{{ route('front.account.settings.billing') }}"
                class="flex flex-row gap-2 items-center p-2"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                    <path fill-rule="evenodd" d="M2.5 4A1.5 1.5 0 0 0 1 5.5V6h18v-.5A1.5 1.5 0 0 0 17.5 4h-15ZM19 8.5H1v6A1.5 1.5 0 0 0 2.5 16h15a1.5 1.5 0 0 0 1.5-1.5v-6ZM3 13.25a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 0 1.5h-1.5a.75.75 0 0 1-.75-.75Zm4.75-.75a.75.75 0 0 0 0 1.5h3.5a.75.75 0 0 0 0-1.5h-3.5Z" clip-rule="evenodd" />
                </svg>
                <span class="text-gray-800">Payment Methods</span>
            </a>
        </div>

        <div class="rounded-lg bg-white flex-grow p-6 m-2">
            @yield('content')
        </div>
    </main>
@endsection

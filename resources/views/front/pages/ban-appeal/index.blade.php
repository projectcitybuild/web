@extends('front.layouts.root-layout')

@section('title', 'Appeal Ban')
@section('heading', 'Appeal Ban')
@section('description', 'Use the below form to submit a ban appeal')

@section('body')
    <x-front::navbar />

    <main class="bg-white py-20">
        <header class="max-w-screen-2xl mx-auto px-6">
            @if($errors->any())
                <div class="alert alert--error">
                    <h2><i class="fas fa-exclamation-circle"></i> Error</h2>
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="-mx-4 flex flex-wrap">
                <div class="w-full px-4">
                    <div class="mx-auto mb-[60px] max-w-[650px] text-center">
                        <h2 class="font-bold text-5xl">
                            Appeal a Ban
                        </h2>
                        <p class="text-base text-gray-500 mt-4 leading-relaxed">
                            If you have been <strong>wrongfully banned</strong>, please let us know so we can investigate the matter.
                            Alternatively, if you are <strong>seeking a second chance</strong>, you may plead your case for consideration.
                        </p>
                    </div>
                </div>
            </div>
        </header>

        <section class="max-w-screen-lg mx-auto px-6">
            <div class="text-gray-500 text-sm text-center">Step 1 of 2</div>

            <div class="flex flex-col md:flex-row justify-between gap-6 mt-6">
                <div class="flex-grow md:w-0 rounded-xl border border-gray-200 bg-gray-50 p-8 flex flex-col">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" />
                    </svg>

                    <h2 class="text-3xl font-bold mt-2">Select from list</h2>

                    <div class="text-gray-500 text-sm mt-3">
                        Auto-fill some of your appeal details by selecting the ban from a list.
                        If logged-in, you'll be shown your active bans in addition to the main list.
                    </div>

                    <div class="flex-grow"></div>

                    <x-button class="mt-9">
                        Proceed
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                            <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                        </svg>
                    </x-button>
                </div>

                <div class="px-1 text-3xl font-bold text-gray-500 flex justify-center items-center">
                    or
                </div>

                <div class="flex-grow md:w-0 rounded-xl border border-gray-200 bg-gray-50 p-8 flex flex-col">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                    </svg>

                    <h2 class="text-3xl font-bold mt-2">Fill out form</h2>

                    <div class="text-gray-500 text-sm mt-3">
                        No login required, but all details will need to be filled-in manually
                    </div>

                    <div class="flex-grow"></div>

                    <x-button class="mt-9">
                        Proceed
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                            <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                        </svg>
                    </x-button>
                </div>
            </div>
        </section>
    </main>

    <p>
        Please fill out all fields honestly.
    </p>
    <blockquote>
        <h3><i class="fas fa-warning"></i> WARNING</h3>
        Abuse of the ban appeal system will result in your access being revoked.
    </blockquote>

    @auth
        <div class="contents__section">
            <h2>Current Bans</h2>
            <div class="game-ban-column">
                @each('front.pages.ban-appeal._game-ban-listing', $bans, 'ban', 'front.pages.ban-appeal._game-ban-empty')
            </div>
        </div>
        <div class="contents__section">
            <h2>Look up Account</h2>
            <p>If the ban doesn't appear above, try searching manually. Enter your <strong>current</strong> Minecraft
                username.</p>

            @include('front.pages.ban-appeal._username-lookup')
        </div>
    @else
        <div class="contents__section">
            <h2>Sign in to Appeal</h2>
            <p>To appeal a ban, sign in to your PCB account. Alternatively, you may appeal as a guest.</p>
            <a href="{{ route('front.appeal.auth') }}" class="button button--filled">Sign In </a>
        </div>
        <div class="contents__section">
            <h2>Appeal as a Guest</h2>
            <p>To start your appeal, enter the <strong>current</strong> username of your Minecraft account</p>

            @include('front.pages.ban-appeal._username-lookup')
        </div>
    @endauth
@endsection

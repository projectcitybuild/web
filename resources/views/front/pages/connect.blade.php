@extends('front.layouts.root-layout')

@section('title', 'Connect to Project City Build')
@section('meta_title', 'Connect to Project City Build')
@section('meta_description', 'Join Project City Build on Minecraft: Java Edition using the server address pcbmc.co.')

@section('body')
    <x-navbar />

    <main class="bg-gray-950 text-gray-100">
        <section class="bg-gradient-to-b from-gray-900 to-gray-950 py-16 sm:py-20 lg:py-24">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto mt-12 max-w-3xl text-center sm:mt-16">
                    <p class="inline-flex items-center gap-2 rounded-full border border-amber-800 bg-gray-900/80 px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-amber-300 shadow-sm">
                        <span class="relative flex size-2.5" aria-hidden="true">
                            <span class="absolute inline-flex size-full animate-ping rounded-full bg-amber-400 opacity-75 motion-reduce:animate-none"></span>
                            <span class="relative inline-flex size-2.5 rounded-full bg-amber-500"></span>
                        </span>
                        Minecraft Java Edition
                    </p>

                    <h1 class="mt-6 font-display text-6xl leading-[0.92] sm:text-7xl md:text-8xl">
                        Your next build<br class="hidden sm:block" /> starts here.
                    </h1>
                    <p class="mx-auto mt-6 max-w-2xl text-base leading-7 text-gray-300 sm:text-lg sm:leading-8">
                        Project City Build is a <strong class="text-amber-300">Minecraft: Java Edition</strong> server. To join, launch Minecraft and enter our server address.
                    </p>

                    <div class="relative mx-auto mt-10 max-w-2xl overflow-hidden rounded-3xl border border-amber-800 bg-gray-900 p-6 text-white shadow-2xl shadow-amber-950/15 sm:p-10">
                        <div aria-hidden="true" class="absolute inset-x-12 top-0 h-px bg-gradient-to-r from-transparent via-amber-400 to-transparent"></div>
                        <p class="text-xs font-bold uppercase tracking-[0.22em] text-amber-300">Server address</p>
                        <code class="mt-4 block break-all font-mono text-4xl font-bold tracking-wider text-white sm:text-6xl sm:tracking-[0.16em]">pcbmc.co</code>

                        <button
                            type="button"
                            data-server-address="pcbmc.co"
                            class="mt-7 inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-amber-400 px-6 py-3 text-sm font-bold text-gray-950 shadow-lg shadow-black/20 transition duration-200 hover:-translate-y-0.5 hover:bg-amber-300 active:translate-y-0 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-300"
                        >
                            <svg class="size-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 8V5.75A1.75 1.75 0 0 1 9.75 4h8.5A1.75 1.75 0 0 1 20 5.75v8.5A1.75 1.75 0 0 1 18.25 16H16M5.75 8h8.5A1.75 1.75 0 0 1 16 9.75v8.5A1.75 1.75 0 0 1 14.25 20h-8.5A1.75 1.75 0 0 1 4 18.25v-8.5A1.75 1.75 0 0 1 5.75 8Z"/>
                            </svg>
                            <span data-copy-address-label>Copy address</span>
                        </button>
                        <p class="mt-4 min-h-6 text-sm text-gray-300" data-copy-address-status role="status" aria-live="polite">
                            Copy the address, then follow the steps below.
                        </p>
                    </div>
                </div>

                <section aria-labelledby="connection-steps-heading" class="mx-auto mt-20 max-w-5xl sm:mt-24">
                    <div class="mx-auto max-w-2xl text-center">
                        <h2 id="connection-steps-heading" class="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">Where to enter the address</h2>
                        <p class="mt-4 leading-7 text-gray-300">Use these steps in Minecraft: Java Edition. The same address works with either Add Server or Direct Connection.</p>
                    </div>

                    <ol class="mt-8 grid gap-4 md:grid-cols-3">
                        <li class="rounded-2xl border border-gray-800 bg-gray-900 p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-md">
                            <span class="flex size-10 items-center justify-center rounded-full bg-amber-400/10 font-display text-2xl text-amber-300">01</span>
                            <h3 class="mt-5 text-lg font-semibold">Open Multiplayer</h3>
                            <p class="mt-2 leading-7 text-gray-300">Launch Minecraft: Java Edition and choose <strong class="font-semibold text-white">Multiplayer</strong> from the title screen.</p>
                        </li>
                        <li class="rounded-2xl border border-gray-800 bg-gray-900 p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-md">
                            <span class="flex size-10 items-center justify-center rounded-full bg-amber-400/10 font-display text-2xl text-amber-300">02</span>
                            <h3 class="mt-5 text-lg font-semibold">Add a server</h3>
                            <p class="mt-2 leading-7 text-gray-300">Select <strong class="font-semibold text-white">Add Server</strong> to save it for later, or choose <strong class="font-semibold text-white">Direct Connection</strong> to join right away.</p>
                        </li>
                        <li class="rounded-2xl border border-gray-800 bg-gray-900 p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-md">
                            <span class="flex size-10 items-center justify-center rounded-full bg-amber-400/10 font-display text-2xl text-amber-300">03</span>
                            <h3 class="mt-5 text-lg font-semibold">Enter the address</h3>
                            <p class="mt-2 leading-7 text-gray-300">Put <strong class="font-mono font-semibold text-white">pcbmc.co</strong> in the <strong class="font-semibold text-white">Server Address</strong> field, then select Done or Join Server.</p>
                        </li>
                    </ol>

                    <p class="mt-8 text-center text-sm text-gray-400">
                        Need a hand getting connected?
                        <a href="{{ config('discord.invite_url') }}" class="font-semibold text-blue-300 underline decoration-blue-700 underline-offset-4 hover:text-blue-200">Ask us on Discord</a>.
                    </p>
                </section>
            </div>
        </section>
    </main>

    @include('front.components.footer')
@endsection

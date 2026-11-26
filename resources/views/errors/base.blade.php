@extends('front.layouts.root-layout')

@section('body')
    <main class="relative isolate flex min-h-screen items-center overflow-hidden bg-gray-950 px-4 py-10 text-gray-100 sm:px-6 lg:px-8">
        <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 overflow-hidden">
            <div class="absolute -left-40 top-1/4 size-96 rounded-full bg-amber-500/10 blur-3xl"></div>
            <div class="absolute -right-32 bottom-0 size-96 rounded-full bg-blue-500/10 blur-3xl"></div>
            <div class="absolute inset-0 bg-gradient-to-b from-gray-900/20 via-transparent to-gray-950/80"></div>
        </div>

        <div class="mx-auto w-full max-w-5xl">
            <header class="mb-6 flex items-center justify-between gap-4 px-1">
                <a href="{{ route('front.home') }}" class="inline-flex items-center gap-3 text-white transition-opacity hover:opacity-80">
                    <span aria-hidden="true" class="flex size-10 items-center justify-center rounded-xl border border-amber-400/30 bg-amber-400/10 text-amber-300">
                        <svg class="size-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/>
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="m4.5 7.75 7.5 4.5 7.5-4.5M12 12.5V21"/>
                        </svg>
                    </span>
                    <span class="font-display text-2xl tracking-wide sm:text-3xl">Project City Build</span>
                </a>

                <span class="hidden items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.16em] text-gray-400 sm:inline-flex">
                    <span aria-hidden="true" class="size-1.5 rounded-full bg-amber-400"></span>
                    System notice
                </span>
            </header>

            <section class="relative overflow-hidden rounded-3xl border border-white/10 bg-gray-900/80 shadow-2xl shadow-black/30 backdrop-blur-xl">
                <div aria-hidden="true" class="absolute inset-x-10 top-0 h-px bg-gradient-to-r from-transparent via-amber-400/70 to-transparent"></div>

                <div class="grid md:grid-cols-[0.9fr_1.1fr]">
                    <div class="relative flex min-h-64 items-center justify-center overflow-hidden border-b border-white/10 bg-gray-950/30 p-8 md:min-h-[27rem] md:border-b-0 md:border-r">
                        <div aria-hidden="true" class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(251,191,36,0.08),transparent_65%)]"></div>
                        <div aria-hidden="true" class="absolute inset-0 opacity-[0.12] [background-image:linear-gradient(rgba(255,255,255,0.12)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.12)_1px,transparent_1px)] [background-size:36px_36px]"></div>
                        <div class="relative text-center">
                            <p class="mb-3 text-xs font-bold uppercase tracking-[0.24em] text-amber-300/80">Error code</p>
                            <p aria-label="Error @yield('code')" class="font-display text-[clamp(8rem,22vw,13rem)] leading-[0.78] tracking-tight text-white drop-shadow-[0_8px_35px_rgba(251,191,36,0.12)]">
                                @yield('code')
                            </p>
                            <div aria-hidden="true" class="mx-auto mt-8 h-1 w-16 rounded-full bg-amber-400"></div>
                        </div>
                    </div>

                    <div class="flex flex-col justify-center p-7 sm:p-10 lg:p-14">
                        <p class="inline-flex w-fit items-center gap-2 rounded-full border border-amber-400/20 bg-amber-400/5 px-3 py-1.5 text-xs font-bold uppercase tracking-[0.18em] text-amber-300">
                            <svg aria-hidden="true" class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v3m0 4h.01M10.3 4.8 2.9 17.2a1.8 1.8 0 0 0 1.55 2.7h15.1a1.8 1.8 0 0 0 1.55-2.7L13.7 4.8a1.96 1.96 0 0 0-3.4 0Z"/>
                            </svg>
                            Something went wrong
                        </p>

                        <h1 class="mt-6 font-display text-5xl leading-[0.95] text-white sm:text-6xl">
                            @yield('title')
                        </h1>
                        <p class="mt-5 max-w-lg text-base leading-7 text-gray-300 sm:text-lg sm:leading-8">
                            @yield('description')
                        </p>

                        <div class="mt-8">
                            <x-button
                                href="{{ route('front.home') }}"
                                scheme="primary"
                                class="transition duration-200 hover:-translate-y-0.5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-300"
                            >
                                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955a1.125 1.125 0 0 1 1.592 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125h4.125v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75"/>
                                </svg>
                                Back to home
                            </x-button>
                        </div>
                    </div>
                </div>
            </section>

            <p class="mt-6 text-center text-xs tracking-wide text-gray-500">
                © Project City Build {{ date('Y') }}
            </p>
        </div>
    </main>
@endsection

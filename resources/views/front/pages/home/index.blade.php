@extends('front.layouts.root-layout')

@section('title', 'Project City Build - Creative Minecraft Community')
@section('meta_title', 'Project City Build - Creative Minecraft Community')
@section('meta_description', 'One of the world\'s longest-running Minecraft servers; we\'re a community of creative players and city builders')

@section('body')
    <x-front::hero />

    <main class="bg-white text-gray-900 dark:bg-gray-950 dark:text-gray-100">
        <section aria-labelledby="community-heading" class="py-16 sm:py-20 lg:py-24">
            <div class="mx-auto max-w-screen-2xl px-4 sm:px-8">
                <div class="grid gap-8 md:grid-cols-[0.9fr_1.1fr] md:items-end md:gap-16">
                    <div data-home-reveal class="space-y-4">
                        <p class="text-sm font-bold uppercase tracking-[0.18em] text-amber-700 dark:text-amber-400">Since October 2010</p>
                        <h2 id="community-heading" class="max-w-xl font-display text-5xl leading-[0.95] sm:text-6xl">
                            Crafting worlds and connections
                        </h2>
                    </div>

                    <p data-home-reveal data-home-reveal-delay="120" class="max-w-2xl text-base leading-8 text-gray-600 dark:text-gray-300 sm:text-lg">
                        For more than <strong>{{ (int) \Illuminate\Support\Carbon::create(2010, 10, 1)->diffInYears(now()) }} years</strong>, our Minecraft community has been a place to dream big, build together, and share creations.
                        From sprawling cities to unique personal builds, we welcome all — come join the fun and make lasting friendships.
                    </p>
                </div>

                <ul class="mt-10 grid gap-4 sm:grid-cols-2 lg:mt-14 lg:grid-cols-3">
                    <li data-home-reveal class="rounded-2xl border border-gray-200 bg-gray-50 p-6 transition-[color,background-color,border-color,box-shadow,translate] duration-300 ease-out hover:-translate-y-1 hover:border-amber-300 hover:shadow-lg dark:border-gray-800 dark:bg-gray-900 dark:hover:border-amber-700 motion-reduce:transition-none motion-reduce:hover:translate-y-0">
                        <span class="flex size-12 items-center justify-center rounded-xl bg-amber-100 text-amber-700 dark:bg-amber-400/10 dark:text-amber-300">
                            <svg class="size-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3M3.22302 14C4.13247 18.008 7.71683 21 12 21c4.9706 0 9-4.0294 9-9-0-4.97056-4.0294-9-9-9-3.72916 0-6.92858 2.26806-8.29409 5.5M7 9H3V5"/>
                            </svg>
                        </span>
                        <h3 class="mt-5 text-lg font-semibold">Minecraft 24/7</h3>
                        <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">Online all day, every day since the beginning.</p>
                    </li>
                    <li data-home-reveal data-home-reveal-delay="60" class="rounded-2xl border border-gray-200 bg-gray-50 p-6 transition-[color,background-color,border-color,box-shadow,translate] duration-300 ease-out hover:-translate-y-1 hover:border-amber-300 hover:shadow-lg dark:border-gray-800 dark:bg-gray-900 dark:hover:border-amber-700 motion-reduce:transition-none motion-reduce:hover:translate-y-0">
                        <span class="flex size-12 items-center justify-center rounded-xl bg-amber-100 text-amber-700 dark:bg-amber-400/10 dark:text-amber-300">
                            <svg class="size-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 4H4m0 0v4m0-4 5 5m7-5h4m0 0v4m0-4-5 5M8 20H4m0 0v-4m0 4 5-5m7 5h4m0 0v-4m0 4-5-5"/>
                            </svg>
                        </span>
                        <h3 class="mt-5 text-lg font-semibold">Free-Build Worlds</h3>
                        <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">No plots, multiple build worlds, and improved world generation.</p>
                    </li>
                    <li data-home-reveal data-home-reveal-delay="120" class="rounded-2xl border border-gray-200 bg-gray-50 p-6 transition-[color,background-color,border-color,box-shadow,translate] duration-300 ease-out hover:-translate-y-1 hover:border-amber-300 hover:shadow-lg dark:border-gray-800 dark:bg-gray-900 dark:hover:border-amber-700 motion-reduce:transition-none motion-reduce:hover:translate-y-0">
                        <span class="flex size-12 items-center justify-center rounded-xl bg-amber-100 text-amber-700 dark:bg-amber-400/10 dark:text-amber-300">
                            <svg class="size-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.5 11.5 11 13l4-3.5M12 20a16.405 16.405 0 0 1-5.092-5.804A16.694 16.694 0 0 1 5 6.666L12 4l7 2.667a16.695 16.695 0 0 1-1.908 7.529A16.406 16.406 0 0 1 12 20Z"/>
                            </svg>
                        </span>
                        <h3 class="mt-5 text-lg font-semibold">Grief Protection</h3>
                        <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">Block logging and multiple grief-detection methods help protect your builds.</p>
                    </li>
                    <li data-home-reveal data-home-reveal-delay="180" class="rounded-2xl border border-gray-200 bg-gray-50 p-6 transition-[color,background-color,border-color,box-shadow,translate] duration-300 ease-out hover:-translate-y-1 hover:border-amber-300 hover:shadow-lg dark:border-gray-800 dark:bg-gray-900 dark:hover:border-amber-700 motion-reduce:transition-none motion-reduce:hover:translate-y-0">
                        <span class="flex size-12 items-center justify-center rounded-xl bg-amber-100 text-amber-700 dark:bg-amber-400/10 dark:text-amber-300">
                            <svg class="size-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12.01 6.001C6.5 1 1 8 5.782 13.001L12.011 20l6.23-7C23 8 17.5 1 12.01 6.002Z"/>
                            </svg>
                        </span>
                        <h3 class="mt-5 text-lg font-semibold">Community Focused</h3>
                        <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">
                            Build together, join events, and share your work. Come meet us on
                            <a href="{{ config('discord.invite_url') }}" class="font-semibold text-blue-700 underline decoration-blue-300 underline-offset-4 hover:text-blue-800 dark:text-blue-300 dark:decoration-blue-700 dark:hover:text-blue-200">Discord</a>.
                        </p>
                    </li>
                    <li data-home-reveal data-home-reveal-delay="240" class="rounded-2xl border border-gray-200 bg-gray-50 p-6 transition-[color,background-color,border-color,box-shadow,translate] duration-300 ease-out hover:-translate-y-1 hover:border-amber-300 hover:shadow-lg dark:border-gray-800 dark:bg-gray-900 dark:hover:border-amber-700 motion-reduce:transition-none motion-reduce:hover:translate-y-0">
                        <span class="flex size-12 items-center justify-center rounded-xl bg-amber-100 text-amber-700 dark:bg-amber-400/10 dark:text-amber-300">
                            <svg class="size-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 9h.01M8.99 9H9m12 3a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM6.6 13a5.5 5.5 0 0 0 10.81 0H6.6Z"/>
                            </svg>
                        </span>
                        <h3 class="mt-5 text-lg font-semibold">Trust &amp; Build Ranks</h3>
                        <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">Build ranks unlock powerful tools; trust ranks bring quality-of-life perks and cosmetics.</p>
                    </li>
                    <li data-home-reveal data-home-reveal-delay="300" class="rounded-2xl border border-gray-200 bg-gray-50 p-6 transition-[color,background-color,border-color,box-shadow,translate] duration-300 ease-out hover:-translate-y-1 hover:border-amber-300 hover:shadow-lg dark:border-gray-800 dark:bg-gray-900 dark:hover:border-amber-700 motion-reduce:transition-none motion-reduce:hover:translate-y-0">
                        <span class="flex size-12 items-center justify-center rounded-xl bg-amber-100 text-amber-700 dark:bg-amber-400/10 dark:text-amber-300">
                            <svg class="size-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m7.171 12.906-2.153 6.411 2.672-.89 1.568 2.34 1.825-5.183m5.73-2.678 2.154 6.411-2.673-.89-1.568 2.34-1.825-5.183M9.165 4.3c.58.068 1.153-.17 1.515-.628a1.681 1.681 0 0 1 2.64 0 1.68 1.68 0 0 0 1.515.628 1.681 1.681 0 0 1 1.866 1.866c-.068.58.17 1.154.628 1.516a1.681 1.681 0 0 1 0 2.639 1.682 1.682 0 0 0-.628 1.515 1.681 1.681 0 0 1-1.866 1.866 1.681 1.681 0 0 0-1.516.628 1.681 1.681 0 0 1-2.639 0 1.681 1.681 0 0 0-1.515-.628 1.681 1.681 0 0 1-1.867-1.866 1.681 1.681 0 0 0-.627-1.515 1.681 1.681 0 0 1 0-2.64c.458-.361.696-.935.627-1.515A1.681 1.681 0 0 1 9.165 4.3ZM14 9a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z"/>
                            </svg>
                        </span>
                        <h3 class="mt-5 text-lg font-semibold">Events &amp; Competitions</h3>
                        <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">Take part in server events and build competitions, with rewards for winners and participants.</p>
                    </li>
                </ul>
            </div>
        </section>

        <section id="worlds" aria-labelledby="worlds-heading" class="bg-gray-50 py-16 dark:bg-gray-900/60 sm:py-20 lg:py-24">
            <div class="mx-auto max-w-screen-2xl px-4 sm:px-8">
                <div data-home-reveal class="mx-auto max-w-2xl text-center">
                    <p class="text-sm font-bold uppercase tracking-[0.18em] text-amber-700 dark:text-amber-400">Your next build starts here</p>
                    <h2 id="worlds-heading" class="mt-3 font-display text-5xl leading-none sm:text-6xl">Our worlds</h2>
                    <p class="mt-4 leading-7 text-gray-600 dark:text-gray-300">From boundless creativity to a city built at a realistic scale, there's always somewhere new to explore.</p>
                </div>

                <div class="mt-10 grid gap-6 lg:mt-14 lg:grid-cols-3">
                    <article data-home-reveal class="group flex flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition-[box-shadow,translate] duration-300 ease-out hover:-translate-y-1 hover:shadow-xl dark:border-gray-800 dark:bg-gray-950 motion-reduce:transition-none motion-reduce:hover:translate-y-0">
                        <div aria-hidden="true" class="aspect-[4/3] bg-gray-200 bg-[url('/resources/images/home_creative.png')] bg-cover bg-center transition-transform duration-700 ease-out group-hover:scale-105 dark:bg-gray-800 motion-reduce:transition-none"></div>
                        <div class="flex grow flex-col p-6 sm:p-7">
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-amber-700 dark:text-amber-400">Create without limits</p>
                            <h3 class="mt-2 font-display text-4xl">Creative</h3>
                            <p class="mt-3 leading-7 text-gray-600 dark:text-gray-300">
                                No plots—just a free-build world full of everything from alien landscapes and cruise ships to castles and entire cities. Let your imagination run wild.
                            </p>
                            <ul class="mt-auto flex flex-wrap gap-x-6 gap-y-3 pt-6 text-sm font-semibold">
                                <li>
                                    <a href="https://www.instagram.com/projectcitybuild" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-blue-700 hover:text-blue-900 dark:text-blue-300 dark:hover:text-blue-200">
                                        Gallery <span aria-hidden="true">→</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('front.maps') }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-blue-700 hover:text-blue-900 dark:text-blue-300 dark:hover:text-blue-200">
                                        Real-time map <span aria-hidden="true">→</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </article>

                    <article data-home-reveal data-home-reveal-delay="100" class="group flex flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition-[box-shadow,translate] duration-300 ease-out hover:-translate-y-1 hover:shadow-xl dark:border-gray-800 dark:bg-gray-950 motion-reduce:transition-none motion-reduce:hover:translate-y-0">
                        <div aria-hidden="true" class="aspect-[4/3] bg-gray-200 bg-[url('/resources/images/home_monarch.png')] bg-cover bg-center transition-transform duration-700 ease-out group-hover:scale-105 dark:bg-gray-800 motion-reduce:transition-none"></div>
                        <div class="flex grow flex-col p-6 sm:p-7">
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-amber-700 dark:text-amber-400">A city made together</p>
                            <h3 class="mt-2 font-display text-4xl">Monarch</h3>
                            <p class="mt-3 leading-7 text-gray-600 dark:text-gray-300">
                                Our premier city project features realistic-scale builds. More than five years in the making, Monarch showcases our builders' talents—and there's room to make your mark.
                            </p>
                            <ul class="mt-auto flex flex-wrap gap-x-6 gap-y-3 pt-6 text-sm font-semibold">
                                <li>
                                    <a href="https://www.instagram.com/projectcitybuild" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-blue-700 hover:text-blue-900 dark:text-blue-300 dark:hover:text-blue-200">
                                        Gallery <span aria-hidden="true">→</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('front.maps') }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-blue-700 hover:text-blue-900 dark:text-blue-300 dark:hover:text-blue-200">
                                        Real-time map <span aria-hidden="true">→</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </article>

                    <article data-home-reveal data-home-reveal-delay="200" class="group flex flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition-[box-shadow,translate] duration-300 ease-out hover:-translate-y-1 hover:shadow-xl dark:border-gray-800 dark:bg-gray-950 motion-reduce:transition-none motion-reduce:hover:translate-y-0">
                        <div aria-hidden="true" class="aspect-[4/3] bg-gray-200 bg-[url('/resources/images/home_survival.png')] bg-cover bg-center transition-transform duration-700 ease-out group-hover:scale-105 dark:bg-gray-800 motion-reduce:transition-none"></div>
                        <div class="flex grow flex-col p-6 sm:p-7">
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-amber-700 dark:text-amber-400">Need a break?</p>
                            <h3 class="mt-2 font-display text-4xl">Survival</h3>
                            <p class="mt-3 leading-7 text-gray-600 dark:text-gray-300">
                                Our Survival world is as close to vanilla as possible. No warps, teleports, or dynamic map—just Minecraft in its purest form.
                            </p>
                            <ul class="mt-auto flex flex-wrap gap-x-6 gap-y-3 pt-6 text-sm font-semibold">
                                <li>
                                    <a href="https://www.instagram.com/projectcitybuild" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-blue-700 hover:text-blue-900 dark:text-blue-300 dark:hover:text-blue-200">
                                        Gallery <span aria-hidden="true">→</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section aria-labelledby="recent-players-heading" class="py-16 sm:py-20 lg:py-24">
            <div class="mx-auto max-w-screen-2xl px-4 sm:px-8">
                <div data-home-reveal class="mx-auto max-w-2xl text-center">
                    <p class="text-sm font-bold uppercase tracking-[0.18em] text-amber-700 dark:text-amber-400">Community in motion</p>
                    <h2 id="recent-players-heading" class="mt-3 font-display text-5xl leading-none sm:text-6xl">Recent Players</h2>
                    <p class="mt-4 leading-7 text-gray-600 dark:text-gray-300">See who's been visiting our worlds lately.</p>
                </div>

                <ul class="mt-10 grid gap-4 grid-cols-2 md:mt-14 md:grid-cols-4">
                    @foreach ($recentPlayers as $player)
                        <li data-home-reveal data-home-reveal-delay="{{ $loop->index % 4 * 60 }}" class="group relative isolate flex min-h-56 flex-col items-center gap-4 overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 text-center shadow-sm transition-[border-color,box-shadow,translate] duration-300 ease-out hover:-translate-y-1 hover:border-amber-300 hover:shadow-xl dark:border-gray-800 dark:bg-gray-950 dark:hover:border-amber-700 motion-reduce:transition-none motion-reduce:hover:translate-y-0">
                            <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-0 h-24 bg-gradient-to-b from-amber-100/80 to-transparent dark:from-amber-400/10"></div>
                            <div aria-hidden="true" class="pointer-events-none absolute -right-8 -top-8 size-32 rounded-full bg-amber-300/30 blur-2xl dark:bg-amber-400/10"></div>

                            <div class="relative mt-2 rounded-2xl bg-gradient-to-br from-amber-300 via-orange-200 to-yellow-100 p-1 shadow-lg shadow-amber-900/10 transition-transform duration-300 ease-out group-hover:rotate-3 group-hover:scale-105 dark:from-amber-500 dark:via-orange-600 dark:to-yellow-500 motion-reduce:transition-none motion-reduce:group-hover:rotate-0 motion-reduce:group-hover:scale-100">
                                <div class="rounded-xl border border-white/80 bg-gray-950 p-2 dark:border-white/10">
                                    <img
                                        src="https://minotar.net/helm/{{ $player->uuid }}/64"
                                        alt=""
                                        width="64"
                                        height="64"
                                        class="size-16 rounded-lg object-cover [image-rendering:pixelated]"
                                    >
                                </div>
                            </div>

                            <div class="relative min-w-0">
                                <h3 class="truncate font-semibold">{{ $player->alias ?? 'Unknown player' }}</h3>
                                <p class="mt-2 inline-flex whitespace-nowrap items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-1 text-xs text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                    <span aria-hidden="true" class="size-1.5 rounded-full bg-amber-500"></span>
                                    <span class="sr-only">Last seen </span>
                                    <time datetime="{{ $player->latestSession->starts_at->toIso8601String() }}">
                                        {{ $player->latestSession->starts_at->diffForHumans() }}
                                    </time>
                                </p>
                            </div>

                            @if ($player->account !== null && $player->account->roles->isNotEmpty())
                                <ul aria-label="{{ $player->alias ?? 'Player' }} roles" class="relative flex flex-wrap justify-center gap-1.5">
                                    @foreach ($player->account->roles as $role)
                                        <li class="rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-[11px] font-medium text-amber-900 dark:border-amber-800 dark:bg-amber-950/60 dark:text-amber-200">
                                            {{ $role->alias ?? Str::title($role->name) }}
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>

        <x-front::instagram-feed :feed-configured="$instagramFeedConfigured" :images="$instagramImages" />

        <section aria-labelledby="connect-heading" class="bg-gray-100 py-14 dark:bg-gray-900 sm:py-16">
            <div class="mx-auto max-w-screen-2xl px-4 sm:px-8">
                <div data-home-reveal class="flex flex-col gap-6 rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-950 sm:p-10 lg:flex-row lg:items-center lg:justify-between">
                    <div class="max-w-2xl">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-amber-700 dark:text-amber-400">Minecraft: Java Edition</p>
                        <h2 id="connect-heading" class="mt-3 text-3xl font-semibold tracking-tight sm:text-4xl">Ready to connect?</h2>
                        <p class="mt-3 leading-7 text-gray-600 dark:text-gray-300">Find the server address and step-by-step connection instructions.</p>
                    </div>
                    <a href="{{ route('front.connect') }}" class="inline-flex min-h-12 shrink-0 items-center justify-center gap-2 rounded-xl bg-amber-400 px-5 py-3 text-sm font-bold text-gray-950 transition duration-200 hover:-translate-y-0.5 hover:bg-amber-300 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-500 active:translate-y-0 motion-reduce:hover:translate-y-0 motion-reduce:transition-none">
                        How to connect
                        <span aria-hidden="true">→</span>
                    </a>
                </div>
            </div>
        </section>
    </main>

    @include('front.components.footer')
@endsection

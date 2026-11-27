@extends('front.layouts.root-layout')

@section('title', 'Donate - Project City Build')
@section('meta_description', 'Support Project City Build and help cover the costs of running our Minecraft servers and community website.')

@section('body')
    <div class="relative isolate">
        <div
            aria-hidden="true"
            data-donation-background
            class="pointer-events-none fixed inset-0 -z-10 opacity-100"
        >
            <div data-donation-parallax class="absolute -inset-y-[8vh] inset-x-0 bg-[url('/resources/images/bg_donate.webp')] bg-cover bg-center"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-gray-950/90 via-gray-950/75 to-gray-950/50 lg:bg-gradient-to-l lg:from-gray-950/90 lg:via-gray-950/70 lg:to-gray-950/40"></div>
            <div class="absolute inset-0 bg-gradient-to-b from-gray-950/15 via-transparent to-gray-950/70"></div>
        </div>

        <header data-donation-hero class="relative overflow-hidden border-b border-amber-900/50 bg-transparent">
        <x-front::navbar variant="clear" />

        <div class="relative mx-auto grid w-full max-w-6xl gap-12 px-4 py-16 sm:px-6 sm:py-24 lg:min-h-[38rem] lg:grid-cols-2 lg:items-center lg:gap-16 lg:px-8">
            <div class="lg:col-start-2 lg:translate-y-6">
                <div class="max-w-2xl lg:ml-auto">
                    <h1 class="mt-6 font-display text-6xl leading-[0.9] tracking-tight text-white drop-shadow-lg sm:text-7xl lg:text-8xl">
                        Help Keep Us<br class="hidden sm:block" /> Online
                    </h1>

                    <p class="mt-6 max-w-xl text-base leading-7 text-white drop-shadow sm:text-lg sm:leading-8">
                        We can only operate thanks to the continuous support of our volunteers and community.
                    </p>
                    <p class="mt-4 max-w-xl leading-7 text-gray-100 drop-shadow">
                        Please consider donating to help us operate for another year. All proceeds go towards the high running expense of our servers, infrastructure and websites —
                        <strong class="text-amber-200">nothing goes into our pockets</strong>.
                    </p>
                </div>

                <div class="mt-10">
                    <div class="rounded-2xl border border-white/25 bg-gray-950/60 p-5 shadow-xl shadow-black/20 backdrop-blur-md sm:p-6">
                        <div class="border-b border-white/20 pb-5">
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-amber-200">Annual community goal</p>
                            <p class="mt-2 font-display text-5xl leading-none text-white sm:text-6xl">${{ $target_funding }}</p>
                        </div>

                        <div class="pt-5">
                            <div class="mb-4 flex items-center justify-between gap-4">
                                <p class="text-sm font-semibold text-white">Community contributions this year</p>
                            </div>
                            <x-donation-bar />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </header>

    <main class="bg-transparent text-gray-100">
        <section class="border-t border-gray-800 bg-gray-950/80 py-16 sm:py-20">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                @if($errors->any())
                    <div class="alert alert--error">
                        <h2>Error</h2>
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="mx-auto mb-10 max-w-2xl text-center sm:mb-14">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-amber-300">Choose your support</p>
                    <h2 class="mt-3 font-display text-5xl leading-none sm:text-6xl">
                        Donation Tiers
                    </h2>
                    <p class="mt-4 leading-7 text-gray-300">
                        Choose the amount that suits you. As a thank you for your support, you'll receive perks based on the amount you donate.
                    </p>
                </div>

                <div class="-mx-4 flex flex-wrap justify-center">
                    <div class="w-full px-4 md:w-1/2 lg:w-1/3">
                        <div class="mb-6 flex h-full flex-col rounded-2xl border border-gray-800 bg-gray-900 p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:border-amber-800 hover:shadow-lg hover:shadow-black/20 sm:p-8">
                            <div class="mb-4 flex items-center text-sm font-bold uppercase tracking-[0.16em] text-amber-300">
                                Copper Tier
                           </div>
                            <h2 class="mb-5 text-5xl font-bold text-white">
                                <span>$4</span>
                                <span class="text-base font-medium text-gray-400">
                                  for a month
                                </span>
                            </h2>
                            <p class="mb-6 border-b border-gray-800 pb-6 text-sm leading-6 text-gray-300">
                                Perfect for those who want to stand-out and gain some basic quality-of-life improvements
                            </p>
                            <div class="mb-8 flex flex-1 flex-col gap-3 text-sm text-gray-200">
                                <p class="flex flex-row items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                        <path fill-rule="evenodd" d="M9.293 2.293a1 1 0 0 1 1.414 0l7 7A1 1 0 0 1 17 11h-1v6a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-3a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-6H3a1 1 0 0 1-.707-1.707l7-7Z" clip-rule="evenodd" />
                                    </svg>
                                    <span>
                                        <strong>3</strong> additional homes
                                    </span>
                                </p>
                                <p class="flex flex-row items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Donor rank and in-game title</span>
                                </p>
                                <p class="flex flex-row items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Donor badge</span>
                                </p>
                                <p class="flex flex-row items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Custom nickname</span>
                                </p>
                                <p class="flex flex-row items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Fly and walk speed commands</span>
                                </p>
                            </div>
                            <x-button
                                scheme="primary"
                                class="w-full transition duration-200 hover:-translate-y-0.5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-300"
                                data-donation-dialog-open
                                data-tier-name="Copper Tier"
                                data-subscription-price-id="{{ config('donations.price_ids.copper.subscription') }}"
                                data-one-time-price-id="{{ config('donations.price_ids.copper.one_off') }}"
                            >
                                Choose
                            </x-button>
                        </div>
                    </div>

                    <div class="w-full px-4 md:w-1/2 lg:w-1/3">
                        <div class="mb-6 flex h-full flex-col rounded-2xl border border-gray-800 bg-gray-900 p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:border-amber-800 hover:shadow-lg hover:shadow-black/20 sm:p-8">
                            <div class="mb-4 flex items-center text-sm font-bold uppercase tracking-[0.16em] text-amber-300">
                                Iron Tier
                            </div>
                            <h2 class="mb-5 text-5xl font-bold text-white">
                                <span>$8</span>
                                <span class="text-base font-medium text-gray-400">
                                  for a month
                                </span>
                            </h2>
                            <p class="mb-6 border-b border-gray-800 pb-6 text-sm leading-6 text-gray-300">
                                Perfect for those who want even more cosmetic options and convenience
                            </p>
                            <div class="mb-8 flex flex-1 flex-col gap-3 text-sm text-gray-200">
                                <p class="flex flex-row gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                        <path fill-rule="evenodd" d="M9.293 2.293a1 1 0 0 1 1.414 0l7 7A1 1 0 0 1 17 11h-1v6a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-3a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-6H3a1 1 0 0 1-.707-1.707l7-7Z" clip-rule="evenodd" />
                                    </svg>
                                    <span>
                                        <strong>8</strong> additional homes
                                    </span>
                                </p>
                                <p class="flex flex-row items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Donor rank and in-game title</span>
                                </p>
                                <p class="flex flex-row items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Donor badge</span>
                                </p>
                                <p class="flex flex-row items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Custom nickname</span>
                                </p>
                                <p class="flex flex-row items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Fly and walk speed commands</span>
                                </p>
                                <p class="flex flex-row items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                    </svg>
                                    <span>
                                        <x-front::command>/tp</x-front::command> and <x-front::command>/tppos</x-front::command> commands
                                    </span>
                                </p>
                                <p class="flex flex-row items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                    </svg>
                                    <span>
                                        <x-front::command>/ascend</x-front::command> and <x-front::command>/thru</x-front::command> commands
                                    </span>
                                </p>
                                <p class="flex flex-row items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Nickname colors</span>
                                </p>
                            </div>
                            <x-button
                                scheme="primary"
                                class="w-full transition duration-200 hover:-translate-y-0.5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-300"
                                data-donation-dialog-open
                                data-tier-name="Iron Tier"
                                data-subscription-price-id="{{ config('donations.price_ids.iron.subscription') }}"
                                data-one-time-price-id="{{ config('donations.price_ids.iron.one_off') }}"
                            >
                                Choose
                            </x-button>
                        </div>
                    </div>

                    <div class="w-full px-4 md:w-1/2 lg:w-1/3">
                        <div class="mb-6 flex h-full flex-col rounded-2xl border border-gray-800 bg-gray-900 p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:border-amber-800 hover:shadow-lg hover:shadow-black/20 sm:p-8">
                            <div class="mb-4 flex items-center text-sm font-bold uppercase tracking-[0.16em] text-amber-300">
                                Diamond Tier
                            </div>
                            <h2 class="mb-5 text-5xl font-bold text-white">
                                <span>$15</span>
                                <span class="text-base font-medium text-gray-400">
                                  for a month
                                </span>
                            </h2>
                            <p class="mb-6 border-b border-gray-800 pb-6 text-sm leading-6 text-gray-300">
                                For those who appreciate what we do and want to help support us financially
                            </p>
                            <div class="mb-8 flex flex-1 flex-col gap-3 text-sm text-gray-200">
                                <p class="flex flex-row gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                        <path fill-rule="evenodd" d="M9.293 2.293a1 1 0 0 1 1.414 0l7 7A1 1 0 0 1 17 11h-1v6a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-3a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-6H3a1 1 0 0 1-.707-1.707l7-7Z" clip-rule="evenodd" />
                                    </svg>
                                    <span>
                                        <strong>15</strong> additional homes
                                    </span>
                                </p>
                                <p class="flex flex-row items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Donor rank and in-game title</span>
                                </p>
                                <p class="flex flex-row items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Donor badge</span>
                                </p>
                                <p class="flex flex-row items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Custom nickname</span>
                                </p>
                                <p class="flex flex-row items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Fly and walk speed commands</span>
                                </p>
                                <p class="flex flex-row items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                    </svg>
                                    <span>
                                        <x-front::command>/tp</x-front::command> and <x-front::command>/tppos</x-front::command> commands
                                    </span>
                                </p>
                                <p class="flex flex-row items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                    </svg>
                                    <span>
                                        <x-front::command>/ascend</x-front::command> and <x-front::command>/thru</x-front::command> commands
                                    </span>
                                </p>
                                <p class="flex flex-row items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Nickname colors</span>
                                </p>
                            </div>
                            <x-button
                                scheme="primary"
                                class="w-full transition duration-200 hover:-translate-y-0.5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-300"
                                data-donation-dialog-open
                                data-tier-name="Diamond Tier"
                                data-subscription-price-id="{{ config('donations.price_ids.diamond.subscription') }}"
                                data-one-time-price-id="{{ config('donations.price_ids.diamond.one_off') }}"
                            >
                                Choose
                            </x-button>
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-right text-xs leading-6 text-gray-400">
                    Your payment details will be processed by <a href="https://stripe.com" target="_blank" rel="noopener noreferrer" class="text-amber-300 underline decoration-amber-700 underline-offset-2 hover:text-amber-200">Stripe</a>, and only a record of your donation will be stored by PCB.<br />
                    In accordance with data retention laws, <strong>we do not store any credit card or bank account information</strong>.
                </div>
            </div>
        </section>

        <section class="border-t border-gray-800 bg-gray-900">
            <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6 sm:py-20 lg:px-8">
                <div class="max-w-2xl">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-amber-300">A few helpful details</p>
                    <h2 class="mt-3 font-display text-5xl leading-none sm:text-6xl">Frequently Asked Questions</h2>
                </div>
                <div class="mt-8 grid border-t border-gray-800 pt-8 text-left md:grid-cols-2 md:gap-12">
                    <div>
                        <div class="mb-10">
                            <h3 class="flex items-center mb-4 text-lg font-medium text-white">
                                <svg class="mr-2 size-5 shrink-0 text-amber-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
                                Are payment processing fees included?
                            </h3>
                            <p class="text-gray-300">
                                Yes. Unfortunately, Stripe charges us a payment processing fee, and we cover that cost ourselves.
                            </p>
                            <p class="mt-4 text-gray-300">
                                For example, if you donate $4 for one month, you will receive one full month of perks, regardless of any processing fees deducted on our end.
                            </p>
                        </div>
                        <div class="mb-10">
                            <h3 class="flex items-center mb-4 text-lg font-medium text-white">
                                <svg class="mr-2 size-5 shrink-0 text-amber-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
                                What happens after my donor perks expire?
                            </h3>
                            <div class="text-gray-300">
                                <p>Once your perks expires:</p>

                                <ul class="mt-2 list-disc pl-6">
                                    <li>Additional commands (such as navigation commands) will no longer be available if you don’t normally have access to them.</li>
                                    <li>Your home limit will be reduced to the default amount.</li>
                                    <li>Your nickname will remain as is</li>
                                </ul>
                            </div>
                        </div>
                        <div class="mb-10">
                            <h3 class="flex items-center mb-4 text-lg font-medium text-white">
                                <svg class="mr-2 size-5 shrink-0 text-amber-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
                                What happens to my homes after my perks expire?
                            </h3>
                            <p class="text-gray-300">
                                You will not lose any existing homes, even if you are over the new limit.
                                You simply won’t be able to create a new home until you delete enough homes to fall within the allowed limit.
                            </p>
                        </div>
                        <div class="mb-10">
                            <h3 class="flex items-center mb-4 text-lg font-medium text-white">
                                <svg class="mr-2 size-5 shrink-0 text-amber-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
                                Can I gift my donor perks to someone else?
                            </h3>
                            <p class="text-gray-300">
                                Yes, but please let a staff member know once you have donated so that we can transfer them to the intended recipient.
                            </p>
                        </div>
                    </div>
                    <div>
                        <div class="mb-10">
                            <h3 class="flex items-center mb-4 text-lg font-medium text-white">
                                <svg class="mr-2 size-5 shrink-0 text-amber-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
                                If I cancel my subscription, do I lose my perks immediately?
                            </h3>
                            <div class="text-gray-300">
                                <p>No. Your perks will remain active until the end of your current billing period (i.e., one month from your last successful payment).</p>
                                <p class="mt-4">For example: if you start your subscription on August 1st and cancel the same day, you will still keep your perks until September 1st.</p>
                            </div>
                        </div>
                        <div class="mb-10">
                            <h3 class="flex items-center mb-4 text-lg font-medium text-white">
                                <svg class="mr-2 size-5 shrink-0 text-amber-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
                                What happens if my subscription payment is declined?
                            </h3>
                            <p class="text-gray-300">
                                The subscription will be cancelled, but your donor perks will expire at the end of the billing cycle (i.e., one month from your last successful payment).
                            </p>
                            <p class="text-gray-300">
                                You can subscribe again anytime.
                            </p>
                        </div>
                        <div class="mb-10">
                            <h3 class="flex items-center mb-4 text-lg font-medium text-white">
                                <svg class="mr-2 size-5 shrink-0 text-amber-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
                                What happens if I donate while I already have active donor perks?
                            </h3>
                            <p class="text-gray-300">Your donor perk durations stack automatically. If you donate again while your perks are still active, your current duration will be extended by the appropriate amount.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="border-t border-gray-800 bg-gray-950">
            <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6 sm:py-20 lg:px-8">
                <div class="max-w-2xl">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-amber-300">Read before donating</p>
                    <h2 class="mt-3 font-display text-5xl leading-none sm:text-6xl">Legal</h2>
                </div>
                <div class="mt-8 border-t border-gray-800 pt-8 text-left">
                    <div class="mb-6">
                        <div class="text-gray-300">
                            Please be aware that payment processors may charge processing fees. These fees are set by the provider and can change at any time, and we unfortunately have no control over them.
                        </div>
                    </div>
                    <div class="mb-6">
                        <div class="text-gray-300">
                            We are <strong>not a for-profit organization</strong>, and donations are completely voluntary. They are made to support the community and help us keep things running — they are <strong>not payments for a specific product or guaranteed service</strong>.
                        </div>
                    </div>
                    <div class="mb-6">
                        <p class="text-gray-300">
                            We may update or adjust donation perks from time to time. While we always aim to honor the benefits described, there may be situations where features need to be changed, limited, or removed due to technical, financial, or operational reasons. If that happens, we’ll always do our best to handle it fairly and communicate clearly with the community.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>

        @include('front.components.footer')
    </div>
@endsection

@push('end')
    <div
        id="donation-dialog-app"
        data-authenticated="{{ auth()->check() ? 'true' : 'false' }}"
        data-login-url="{{ route('front.login') }}"
        data-checkout-url="{{ route('front.donations.checkout') }}"
        data-csrf-token="{{ csrf_token() }}"
    ></div>
@endpush

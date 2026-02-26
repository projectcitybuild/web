@extends('front.layouts.root-layout')

@section('title', 'Donate - Project City Build')

@push('head')
    <script src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>
@endpush

@section('body')
    <header class="
            bg-[url('/resources/images/footer-bg.png')]
            bg-cover bg-no-repeat bg-center
            flex flex-col md:items-center
        ">
        <x-front::navbar variant="clear" />

        <div class="max-w-screen-2xl flex flex-col items-center py-12 px-6 md:py-24 md:px-12">
            <h1 class="text-7xl text-gray-50 font-display tracking-tight text-center">
                Help Keep Us Online
            </h1>

            <p class="text-gray-300 mt-6 text-center leading-loose">
                We can only operate thanks to the continuous support of our volunteers and community.
            </p>
            <p class="text-gray-300 mt-3 text-center leading-loose">
                Please consider donating to help us operate for another year.<br />
                All proceeds go towards the high running expense of our servers and websites - <strong class="text-orange-400">nothing goes into our pockets</strong>.
            </p>

            <div class="min-w-96 mt-12">
                <x-donation-bar />
            </div>

            <div class="text-gray-300 mt-3">
                Annual Goal = <strong>${{ $target_funding }}</strong>
            </div>
        </div>
    </header>

    <main>
        <section class="bg-white py-20">
            <div class="max-w-screen-2xl mx-auto px-6">
                @if($errors->any())
                    <div class="alert alert--error">
                        <h2>Error</h2>
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="-mx-4 flex flex-wrap">
                    <div class="w-full px-4">
                        <div class="mx-auto mb-[60px] max-w-[510px] text-center">
                            <h2 class="mb-3 text-3xl font-bold sm:text-4xl md:text-4xl">
                                Donation Tiers
                            </h2>
                            <p class="text-base text-gray-500">
                                Choose the amount that suits you.
                                <br /><br />
                                As a thank you for your support, you'll receive perks based on the amount you donate.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="-mx-4 flex flex-wrap justify-center">
                    <div class="w-full px-4 md:w-1/2 lg:w-1/3">
                        <div class="mb-10 rounded-xl border-2 py-10 px-8 sm:p-12 lg:py-10 lg:px-6 xl:p-12">
                            <div class="mb-5 block text-lg font-semibold flex flex-row align-content-between">
                                Copper Tier
                           </div>
                            <h2 class="mb-5 text-5xl font-bold">
                                <span>$4</span>
                                <span class="text-base font-medium text-body-color">
                                  for a month
                                </span>
                            </h2>
                            <p class="mb-8 border-b pb-8 text-base text-gray-500">
                                Perfect for those who want to stand-out and gain some basic quality-of-life improvements
                            </p>
                            <div class="mb-9 flex flex-col gap-[14px]">
                                <p class="flex flex-row gap-2">
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
                                class="w-full"
                                onclick="openPaymentModal(
                                        'Copper Tier',
                                        '{{ config('donations.price_ids.copper.one_off') }}',
                                        '{{ config('donations.price_ids.copper.subscription') }}'
                                    )"
                            >
                                Choose
                            </x-button>
                        </div>
                    </div>

                    <div class="w-full px-4 md:w-1/2 lg:w-1/3">
                        <div class="mb-10 rounded-xl border-2 py-10 px-8 sm:p-12 lg:py-10 lg:px-6 xl:p-12">
                            <div class="mb-5 block text-lg font-semibold flex flex-row align-content-between">
                                Iron Tier
                            </div>
                            <h2 class="mb-5 text-5xl font-bold">
                                <span>$8</span>
                                <span class="text-base font-medium text-body-color">
                                  for a month
                                </span>
                            </h2>
                            <p class="mb-8 border-b pb-8 text-base text-gray-500">
                                Perfect for those who want even more cosmetic options and convenience
                            </p>
                            <div class="mb-9 flex flex-col gap-[14px]">
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
                                        <span class="command">/tp</span> and <span class="command">/tppos</span> commands
                                    </span>
                                </p>
                                <p class="flex flex-row items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                    </svg>
                                    <span>
                                        <span class="command">/ascend</span> and <span class="command">/thru</span> commands
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
                                class="w-full"
                                onclick="openPaymentModal(
                                        'Iron Tier',
                                        '{{ config('donations.price_ids.iron.one_off') }}',
                                        '{{ config('donations.price_ids.iron.subscription') }}'
                                    )"
                            >
                                Choose
                            </x-button>
                        </div>
                    </div>

                    <div class="w-full px-4 md:w-1/2 lg:w-1/3">
                        <div class="mb-10 rounded-xl border-2 py-10 px-8 sm:p-12 lg:py-10 lg:px-6 xl:p-12">
                            <div class="mb-5 block text-lg font-semibold flex flex-row align-content-between">
                                Diamond Tier
                            </div>
                            <h2 class="mb-5 text-5xl font-bold">
                                <span>$15</span>
                                <span class="text-base font-medium text-body-color">
                                  for a month
                                </span>
                            </h2>
                            <p class="mb-8 border-b pb-8 text-base text-gray-500">
                                For those who appreciate what we do and want to help support us financially
                            </p>
                            <div class="mb-9 flex flex-col gap-[14px]">
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
                                        <span class="command">/tp</span> and <span class="command">/tppos</span> commands
                                    </span>
                                </p>
                                <p class="flex flex-row items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                    </svg>
                                    <span>
                                        <span class="command">/ascend</span> and <span class="command">/thru</span> commands
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
                                class="w-full"
                                onclick="openPaymentModal(
                                        'Diamond Tier',
                                        '{{ config('donations.price_ids.diamond.one_off') }}',
                                        '{{ config('donations.price_ids.diamond.subscription') }}'
                                    )"
                            >
                                Choose
                            </x-button>
                        </div>
                    </div>
                </div>

                <div class="max-w-screen-2xl mx-auto px-6 text-gray-500 text-xs text-right mt-3 leading-loose">
                    Your payment details will be processed by <a href="https://stripe.com" target="_blank" rel="noopener noreferrer" class="underline text-orange-500">Stripe</a>, and only a record of your donation will be stored by PCB.<br />
                    In accordance with data retention laws, <strong>we do not store any credit card or bank account information</strong>.
                </div>
            </div>
        </section>

        <section class="bg-gray-50 dark:bg-gray-900">
            <div class="py-8 px-4 mx-auto max-w-screen-xl sm:py-16 lg:px-6">
                <h2 class="mb-8 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white">Frequently Asked Questions</h2>
                <div class="grid pt-8 text-left border-t border-gray-200 md:gap-16 dark:border-gray-700 md:grid-cols-2">
                    <div>
                        <div class="mb-10">
                            <h3 class="flex items-center mb-4 text-lg font-medium text-gray-900 dark:text-white">
                                <svg class="flex-shrink-0 mr-2 w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
                                Are payment processing fees included?
                            </h3>
                            <p class="text-gray-500 dark:text-gray-400">
                                Yes. Unfortunately, Stripe charges us a payment processing fee, and we cover that cost ourselves.
                            </p>
                            <p class="mt-4 text-gray-500 dark:text-gray-400">
                                For example, if you donate $4 for one month, you will receive one full month of perks, regardless of any processing fees deducted on our end.
                            </p>
                        </div>
                        <div class="mb-10">
                            <h3 class="flex items-center mb-4 text-lg font-medium text-gray-900 dark:text-white">
                                <svg class="flex-shrink-0 mr-2 w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
                                What happens after my donor perks expire?
                            </h3>
                            <div class="text-gray-500 dark:text-gray-400">
                                <p>Once your perks expires:</p>

                                <ul class="mt-2 list-disc pl-6">
                                    <li>Additional commands (such as navigation commands) will no longer be available if you don’t normally have access to them.</li>
                                    <li>Your home limit will be reduced to the default amount.</li>
                                    <li>Your nickname will remain as is</li>
                                </ul>
                            </div>
                        </div>
                        <div class="mb-10">
                            <h3 class="flex items-center mb-4 text-lg font-medium text-gray-900 dark:text-white">
                                <svg class="flex-shrink-0 mr-2 w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
                                What happens to my homes after my perks expire?
                            </h3>
                            <p class="text-gray-500 dark:text-gray-400">
                                You will not lose any existing homes, even if you are over the new limit.
                                You simply won’t be able to create a new home until you delete enough homes to fall within the allowed limit.
                            </p>
                        </div>
                        <div class="mb-10">
                            <h3 class="flex items-center mb-4 text-lg font-medium text-gray-900 dark:text-white">
                                <svg class="flex-shrink-0 mr-2 w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
                                Can I gift my donor perks to someone else?
                            </h3>
                            <p class="text-gray-500 dark:text-gray-400">
                                Yes, but please let a staff member know once you have donated so that we can transfer them to the intended recipient.
                            </p>
                        </div>
                    </div>
                    <div>
                        <div class="mb-10">
                            <h3 class="flex items-center mb-4 text-lg font-medium text-gray-900 dark:text-white">
                                <svg class="flex-shrink-0 mr-2 w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
                                If I cancel my subscription, do I lose my perks immediately?
                            </h3>
                            <div class="text-gray-500 dark:text-gray-400">
                                <p>No. Your perks will remain active until the end of your current billing period (i.e., one month from your last successful payment).</p>
                                <p class="mt-4">For example: if you start your subscription on August 1st and cancel the same day, you will still keep your perks until September 1st.</p>
                            </div>
                        </div>
                        <div class="mb-10">
                            <h3 class="flex items-center mb-4 text-lg font-medium text-gray-900 dark:text-white">
                                <svg class="flex-shrink-0 mr-2 w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
                                What happens if my subscription payment is declined?
                            </h3>
                            <p class="text-gray-500 dark:text-gray-400">
                                The subscription will be cancelled, but your donor perks will expire at the end of the billing cycle (i.e., one month from your last successful payment).
                            </p>
                            <p class="text-gray-500 dark:text-gray-400">
                                You can subscribe again anytime.
                            </p>
                        </div>
                        <div class="mb-10">
                            <h3 class="flex items-center mb-4 text-lg font-medium text-gray-900 dark:text-white">
                                <svg class="flex-shrink-0 mr-2 w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
                                What happens if I donate while I already have active donor perks?
                            </h3>
                            <p class="text-gray-500 dark:text-gray-400">Your donor perk durations stack automatically. If you donate again while your perks are still active, your current duration will be extended by the appropriate amount.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-gray-50 dark:bg-gray-900">
            <div class="py-8 px-4 mx-auto max-w-screen-xl sm:py-16 lg:px-6">
                <h2 class="mb-8 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white">Legal</h2>
                <div class="pt-8 text-left border-t border-gray-200 md:gap-16 dark:border-gray-700">
                    <div class="mb-6">
                        <div class="text-gray-500 dark:text-gray-400">
                            Before donating, please be aware that payment processors may charge processing fees. These fees are set by the provider and can change at any time, and we unfortunately have no control over them.
                        </div>
                    </div>
                    <div class="mb-6">
                        <div class="text-gray-500 dark:text-gray-400">
                            We are <strong>not a for-profit organization</strong>, and donations are completely voluntary. They are made to support the community and help us keep things running — they are <strong>not payments for a specific product or guaranteed service</strong>.
                        </div>
                    </div>
                    <div class="mb-6">
                        <p class="text-gray-500 dark:text-gray-400">
                            We may update or adjust donation perks from time to time. While we always aim to honor the benefits described, there may be situations where features need to be changed, limited, or removed due to technical, financial, or operational reasons. If that happens, we’ll always do our best to handle it fairly and communicate clearly with the community.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    @include('front.components.footer')
@endsection

@push('end')
    @guest
    <div class="modal micromodal-slide" id="modal-payment" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true">
                <header class="modal__header">
                    <h2>Please Log-in</h2>
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                <main>
                    <section class="modal__section">
                        You must be <a href="{{ route('front.login') }}">logged in</a> to make a donation.
                    </section>
                </main>
            </div>
        </div>
    </div>
    @endguest
    @auth
    <div class="modal micromodal-slide" id="modal-payment" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true">
                <header class="modal__header">
                    <h2>How do you wish to pay?</h2>
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                <main>
                    <section class="modal__section">
                        <h1 class="text-2xl font-bold">Subscription</h1>
                        <div class="leading-loose text-gray-500 mt-3 mb-6">
                            A subscription allows you to automatically pay once a month (cancellable at anytime).
                            We recommend this method of payment if you wish to continuously support us without your perks expiring
                        </div>
                        <form action="{{ route('front.donations.checkout') }}" method="POST">
                            @csrf
                            <input type="hidden" id="subscription-price-id" name="price_id" />
                            <x-button type="submit">
                                Purchase
                            </x-button>

                            <div class="mt-3 text-xs text-gray-500">You will be redirected to Stripe to complete payment</div>
                        </form>
                    </section>

                    <section class="modal__section">
                        <h1 class="text-2xl font-bold">One-Time</h1>
                        <div class="leading-loose text-gray-500 mt-3 mb-6">
                            If you wish to pay just once, up-front, please enter the number of months of perks you would like.
                            Your perks will expire after the given number of months have elapsed (can be extended after it expires)
                        </div>
                        <form action="{{ route('front.donations.checkout') }}" method="POST">
                            @csrf

                            <label for="quantity">I would like to purchase</label>
                            <div class="modal__donate_form">
                                <input
                                    class="textfield"
                                    id="quantity"
                                    name="quantity"
                                    type="text"
                                    value="1"
                                    placeholder="1"
                                    maxlength="3"
                                />
                                <span>
                                    months of <strong id="tier-name">???</strong>
                                </span>
                            </div>

                            <input type="hidden" id="one-time-price-id" name="price_id" />

                            <x-button type="submit">
                                Purchase
                            </x-button>

                            <div class="mt-3 text-xs text-gray-500">You will be redirected to Stripe to complete payment</div>
                        </form>
                    </section>
                </main>
            </div>
        </div>
    </div>
    @endauth

    <script>
        MicroModal.init();

        function openPaymentModal(tierName, subscriptionPriceId, oneTimePriceId) {
            @auth
            document.getElementById('subscription-price-id').value = subscriptionPriceId
            document.getElementById('one-time-price-id').value = oneTimePriceId
            document.getElementById('tier-name').innerHTML = tierName
            @endauth

            MicroModal.show('modal-payment')
        }
    </script>
@endpush

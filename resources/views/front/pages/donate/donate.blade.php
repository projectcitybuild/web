@extends('front.layouts.root-layout')

@section('title', 'Donate - Project City Build')

@push('head')
    <script src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>
@endpush

@section('body')
    <header class="
            bg-[url('/resources/images/header-bg1.jpg')]
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
                    Your payment details will be processed by <a href="https://stripe.com" target="_blank" rel="noopener noreferrer" class="text-blue-500">Stripe</a>, and only a record of your donation will be stored by PCB.<br />
                    In accordance with data retention laws, <strong>we do not store any credit card or bank account information</strong>.
                </div>
            </div>
        </section>

        <section class="bg-gray-50 py-12 md:py-16">
            <div class="max-w-screen-2xl mx-auto px-6">
                <h2 class="mb-3 text-3xl font-bold sm:text-4xl md:text-4xl text-center">
                    FAQ
                </h2>

                <ul class="mt-6">
                    <li>
                        <h2 class="text-lg font-bold">
                            If I cancel a subscription, do I immediately lose my perks?
                        </h2>
                        <p class="text-md text-gray-600 mt-3 leading-relaxed">
                            No, your perks will remain active until the original expiry date (i.e. a month from the last payment).
                            <br />
                            For example, if you started your subscription on August 1st and immediately cancel it, your perks will remain until September 1st.
                        </p>
                    </li>
                    <li class="mt-12">
                        <h2 class="text-lg font-bold">
                            What perks will I lose after my duration ends?
                        </h2>
                        <p class="text-md text-gray-600 mt-3 leading-relaxed">
                            Donor commands (navigation, etc) will not be usable and home limits will be decreased.<br />
                            However, <strong>you will not lose access to your homes</strong> even if you are over the limit - instead you will be unable to use <span class="command">/sethome</span> until you delete the required amount.
                            <br /><br />
                            Subscribers will not lose their perks, provided that their payment isn't declined.
                        </p>
                    </li>
                </ul>
            </div>
        </section>


        <section class="bg-white py-12 md:py-16">
            <div class="max-w-screen-2xl mx-auto px-6">
                <h2 class="mb-3 text-3xl font-bold sm:text-4xl md:text-4xl text-center">
                    Legal
                </h2>

                <ol class="mt-6">
                    <li class="text-md text-gray-600 mt-3 leading-relaxed">
                        Please check for any applicable fees - Stripe can change these beyond our control at any time for any reason.
                    </li>
                    <li class="text-md text-gray-600 mt-3 leading-relaxed">
                        We are not a for-profit organization - donations are not payments for a service.
                    </li>
                    <li class="text-md text-gray-600 mt-3 leading-relaxed">
                        We reserve the right to change donation benefits at any time. <br />
                        While we try our best to honor the written benefits, there are times we are forced to change these due to technical or financial reasons.
                    </li>
                </ol>
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

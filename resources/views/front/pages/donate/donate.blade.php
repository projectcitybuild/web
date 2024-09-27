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

    <div class="page donate">
        <main>
            <section class="donate-tiers">
                <div class="container">
                    <h1>Donation Tiers</h1>

                    @if($errors->any())
                        <div class="alert alert--error">
                            <h2><i class="fas fa-exclamation-circle"></i> Error</h2>
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <div class="donate-tiers__overview">
                        As a thank you for your support, you'll receive perks based on the amount you donate.<br />
                        Choose the amount that suits you.
                    </div>

                    <div class="donation-tier-container">
                        <div class="donation-tier">
                            <div class="donation-tier__bar donation-tier__bar--copper"></div>

                            <div class="donation-tier__header">
                                <img src="{{ Vite::asset('resources/images/icon_copper_tier.png') }}" />
                                <h2>Copper Tier</h2>
                            </div>

                            <div class="donation-tier__price">
                                <div class="donation-tier__price-amount">$4</div>
                                <div class="donation-tier__price-unit">for a month of perks</div>
                            </div>

                            <div class="donation-tier__perks">
                                <div class="donation-tier__perks-title">You will receive</div>

                                <ul>
                                    <li><i class="fas fa-home"></i> <strong>3</strong> additional homes</li>
                                    <li><i class="fas fa-check-circle"></i> Donor rank and in-game title</li>
                                    <li><i class="fas fa-check-circle"></i> Custom nickname (<span class="command">/nick</span> command)</li>
                                    <li><i class="fas fa-check-circle"></i> Flyspeed and Walkspeed commands</li>
                                </ul>
                            </div>

                            <div class="donation-tier__footer">
                                <x-button
                                    onclick="openPaymentModal(
                                        'Copper Tier',
                                        '{{ config('donations.price_ids.copper.one_off') }}',
                                        '{{ config('donations.price_ids.copper.subscription') }}'
                                    )"
                                >
                                    Purchase
                                </x-button>
                            </div>
                        </div>

                        <div class="donation-tier__spacer"></div>

                        <div class="donation-tier">
                            <div class="donation-tier__bar donation-tier__bar--iron"></div>

                            <div class="donation-tier__header">
                                <img src="{{ Vite::asset('resources/images/icon_iron_tier.png') }}" />
                                <h2>Iron Tier</h2>
                            </div>

                            <div class="donation-tier__price">
                                <div class="donation-tier__price-amount">$8</div>
                                <div class="donation-tier__price-unit">for a month of perks</div>
                            </div>

                            <div class="donation-tier__perks">
                                <div class="donation-tier__perks-title">You will receive</div>

                                <ul>
                                    <li><i class="fas fa-home"></i> <strong>8</strong> additional homes</li>
                                    <li><i class="fas fa-check-circle"></i> Donor rank and in-game title</li>
                                    <li><i class="fas fa-check-circle"></i> Custom nickname (<span class="command">/nick</span> command)</li>
                                    <li><i class="fas fa-check-circle"></i> Flyspeed and Walkspeed commands</li>
                                    <li><i class="fas fa-check-circle"></i> <span class="command">/tp</span> and <span class="command">/tppos</span> commands</li>
                                    <li><i class="fas fa-check-circle"></i> <span class="command">/ascend</span> and <span class="command">/thru</span> commands</li>
                                    <li><i class="fas fa-check-circle"></i> Nickname colors</li>
                                </ul>
                            </div>

                            <div class="donation-tier__footer">
                                <x-button
                                    onclick="openPaymentModal(
                                        'Iron Tier',
                                        '{{ config('donations.price_ids.iron.one_off') }}',
                                        '{{ config('donations.price_ids.iron.subscription') }}'
                                    )"
                                >
                                    Purchase
                                </x-button>
                            </div>
                        </div>

                        <div class="donation-tier__spacer"></div>

                        <div class="donation-tier">
                            <div class="donation-tier__bar donation-tier__bar--diamond"></div>

                            <div class="donation-tier__header">
                                <img src="{{ Vite::asset('resources/images/icon_diamond_tier.png') }}" />
                                <h2>Diamond Tier</h2>
                            </div>

                            <div class="donation-tier__price">
                                <div class="donation-tier__price-amount">$15</div>
                                <div class="donation-tier__price-unit">for a month of perks</div>
                            </div>

                            <div class="donation-tier__perks">
                                <div class="donation-tier__perks-title">You will receive</div>

                                <ul>
                                    <li><i class="fas fa-home"></i> <strong>15</strong> additional homes</li>
                                    <li><i class="fas fa-check-circle"></i> Donor rank and in-game title</li>
                                    <li><i class="fas fa-check-circle"></i> Custom nickname (<span class="command">/nick</span> command)</li>
                                    <li><i class="fas fa-check-circle"></i> Flyspeed and Walkspeed commands</li>
                                    <li><i class="fas fa-check-circle"></i> <span class="command">/tp</span> and <span class="command">/tppos</span> commands</li>
                                    <li><i class="fas fa-check-circle"></i> <span class="command">/ascend</span> and <span class="command">/thru</span> commands</li>
                                    <li><i class="fas fa-check-circle"></i> Nickname colors</li>
                                </ul>
                            </div>

                            <div class="donation-tier__footer">
                                <x-button
                                    onclick="openPaymentModal(
                                        'Diamond Tier',
                                        '{{ config('donations.price_ids.diamond.one_off') }}',
                                        '{{ config('donations.price_ids.diamond.subscription') }}'
                                    )"
                                >
                                    Purchase
                                </x-button>
                            </div>
                        </div>
                    </div>

                    <div class="donation-tier-fineprint">
                        Your payment details will be processed by <a href="https://stripe.com" target="_blank" rel="noopener noreferrer">Stripe</a>, and only a record of your donation will be stored by PCB.<br />
                        In accordance with data retention laws, <strong>we do not store any credit card or bank account information</strong>.
                    </div>
                </div>
            </section>

            <section class="donate-faq">
                <div class="container">
                    <h1>FAQ</h1>
                    <div class="donate-faq__contents">
                        <ul>
                            <li>
                                <h2>If I cancel a subscription, do I immediately lose my perks?</h2>

                                No, your perks will remain active until the original expiry date (i.e. a month from the last payment).
                                <br />
                                For example, if you started your subscription on August 1st and immediately cancel it, your perks will remain until September 1st.
                            </li>
                            <li>
                                <h2>What perks will I lose after my duration ends?</h2>

                                Donor commands (navigation, etc) will not be usable and home limits will be decreased.<br />
                                However, <strong>you will not lose access to your homes</strong> even if you are over the limit - instead you will be unable to use <span class="command">/sethome</span> until you delete the required amount.
                                <br /><br />
                                Subscribers will not lose their perks, provided that their payment isn't declined.
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <section class="donate-legal">
                <div class="container">
                    <h1>Legal</h1>
                    <div class="donate-legal__contents">
                        <ul>
                            <li>
                                Please check for any applicable fees as Stripe can change these at any time for any reason, beyond our control.
                            </li>
                        </ul>
                    </div>
                </div>
            </section>
        </main>

        <footer>
            @include('front.components.sitemap')
        </footer>
    </div>

    @include('front.components.footer')
@endsection

@push('end')
    @guest
    <div class="modal micromodal-slide" id="modal-payment" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true">
                <header class="modal__header">
                    <h2><i class="fas fa-lock"></i> Please Log-in</h2>
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
                        <h1><i class="fas fa-history"></i> Subscription</h1>
                        <div class="modal__section_description">
                            A subscription allows you to automatically pay once a month (cancellable at anytime).
                            We recommend this method of payment if you wish to continuously support us without your perks expiring
                        </div>
                        <form action="{{ route('front.donations.checkout') }}" method="POST">
                            @csrf
                            <input type="hidden" id="subscription-price-id" name="price_id" />
                            <button type="submit" class="button button--filled">
                                <i class="fas fa-external-link-alt"></i> Purchase
                            </button>
                        </form>
                    </section>
                    <section class="modal__section">
                        <h1><i class="fas fa-dollar-sign"></i> One-Time</h1>
                        <div class="modal__section_description">
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
                            <button type="submit" class="button button--filled">
                                <i class="fas fa-external-link-alt"></i> Purchase
                            </button>
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

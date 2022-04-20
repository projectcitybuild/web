@extends('v2.front.templates.master')

@section('title', 'Donate - Project City Build')

@push('head')
    <script src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>
@endpush

@section('body')
    <div class="page donate">
        <header class="hero-header">
            <div class="container">
                <div class="hero-header__left">
                    <h1>Help Keep Us Online</h1>

                    <div class="header-desc">
                        PCB is only able to operate thanks to the continuous support of our community and volunteers.
                        All proceeds go towards the high running expense of our servers and websites - <strong>nothing goes into our pockets</strong>.
                        <br /><br />
                        Please consider donating to help keep us online for another year
                    </div>
                </div>

                <div class="hero-header__right">
                    <x-donation-bar />

                    Annual Goal: <strong>${{ $target_funding }}</strong>
                </div>
            </div>
        </header>

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
                                <img src="{{ asset('assets/images/icon_copper_tier.png') }}" />
                                <h2>Copper Tier</h2>
                            </div>

                            <div class="donation-tier__price">
                                <div class="donation-tier__price-amount">$4</div>
                                <div class="donation-tier__price-unit">for a month of perks</div>
                            </div>

                            <div class="donation-tier__boxes">
                                <div class="donation-tier__boxes-title">
                                    <strong>Each week</strong> you'll receive
                                </div>
                                <div class="donation-tier__boxes-reward">
                                    <i class="fas fa-ticket"></i>
                                    <strong>10</strong>
                                    <span>Tickets (<a href="#tickets">what's this?</a>)</span>
                                </div>
                            </div>

                            <div class="donation-tier__perks">
                                <div class="donation-tier__perks-title">Plus, you'll receive</div>

                                <ul>
                                    <li><i class="fas fa-home"></i> <strong>3</strong> additional homes</li>
                                    <li><i class="fas fa-check-circle"></i> Donor rank and in-game title</li>
                                    <li><i class="fas fa-check-circle"></i> Custom nickname (<span class="command">/nick</span> command)</li>
                                    <li><i class="fas fa-check-circle"></i> Flyspeed and Walkspeed commands</li>
                                </ul>
                            </div>

                            <div class="donation-tier__footer">
                                <button
                                    class="button button--filled"
                                    onclick="openPaymentModal(
                                        'Copper Tier',
                                        '{{ config('donations.price_ids.copper.one_off') }}',
                                        '{{ config('donations.price_ids.copper.subscription') }}'
                                    )"
                                >
                                    Purchase
                                </button>
                            </div>
                        </div>

                        <div class="donation-tier__spacer"></div>

                        <div class="donation-tier">
                            <div class="donation-tier__bar donation-tier__bar--iron"></div>

                            <div class="donation-tier__header">
                                <img src="{{ asset('assets/images/icon_iron_tier.png') }}" />
                                <h2>Iron Tier</h2>
                            </div>

                            <div class="donation-tier__price">
                                <div class="donation-tier__price-amount">$8</div>
                                <div class="donation-tier__price-unit">for a month of perks</div>
                            </div>

                            <div class="donation-tier__boxes">
                                <div class="donation-tier__boxes-title">
                                    <strong>Each week</strong> you'll receive
                                </div>
                                <div class="donation-tier__boxes-reward">
                                    <i class="fas fa-ticket"></i>
                                    <strong>25</strong>
                                    <span>Tickets (<a href="#tickets">what's this?</a>)</span>
                                </div>
                            </div>

                            <div class="donation-tier__perks">
                                <div class="donation-tier__perks-title">Plus, you'll receive</div>

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
                                <button
                                    class="button button--filled"
                                    onclick="openPaymentModal(
                                        'Iron Tier',
                                        '{{ config('donations.price_ids.iron.one_off') }}',
                                        '{{ config('donations.price_ids.iron.subscription') }}'
                                    )"
                                >
                                    Purchase
                                </button>
                            </div>
                        </div>

                        <div class="donation-tier__spacer"></div>

                        <div class="donation-tier">
                            <div class="donation-tier__bar donation-tier__bar--diamond"></div>

                            <div class="donation-tier__header">
                                <img src="{{ asset('assets/images/icon_diamond_tier.png') }}" />
                                <h2>Diamond Tier</h2>
                            </div>

                            <div class="donation-tier__price">
                                <div class="donation-tier__price-amount">$15</div>
                                <div class="donation-tier__price-unit">for a month of perks</div>
                            </div>

                            <div class="donation-tier__boxes">
                                <div class="donation-tier__boxes-title">
                                    <strong>Each week</strong> you'll receive
                                </div>
                                <div class="donation-tier__boxes-reward">
                                    <i class="fas fa-ticket"></i>
                                    <strong>50</strong>
                                    <span>Tickets (<a href="#ticketes">what's this?</a>)</span>
                                </div>
                            </div>

                            <div class="donation-tier__perks">
                                <div class="donation-tier__perks-title">Plus, you'll receive</div>

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
                                <button
                                    class="button button--filled"
                                    onclick="openPaymentModal(
                                        'Diamond Tier',
                                        '{{ config('donations.price_ids.diamond.one_off') }}',
                                        '{{ config('donations.price_ids.diamond.subscription') }}'
                                    )"
                                >
                                    Purchase
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="donation-tier-fineprint">
                        Your payment details will be processed by <a href="https://stripe.com" target="_blank" rel="noopener noreferrer">Stripe</a>, and only a record of your donation will be stored by PCB.<br />
                        In accordance with data retention laws, <strong>we do not store any credit card or bank account information</strong>.
                    </div>
                </div>
            </section>

            <a id="tickets"></a>
            <section class="donate-tickets">
                <div class="container">
                    <div class="donate-tickets__title">
                        <h2>Tickets</h2>
                    </div>

                    <div class="donate-tickets__description">
                        Tickets can be exchanged in-game at any time for some cool cosmetics and loot, including
                        <strong>animated hats</strong>, <strong>particle effects</strong>, <strong>pets</strong>,
                        <strong>fun gadgets</strong>, <strong>cloaks</strong>, <strong>emotes</strong>, <strong>mob morphs</strong> and more
                        from a rotation of items strictly limited to donors.

                        Every week, tickets are automatically credited to your account based on your donation tier.
                        <br /><br />
                        Tickets are separate from the regular Survival in-game currency, do not expire and are exclusively for cosmetics.
                        They cannot be purchased or exchanged for with regular currency.
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

                                All cosmetics purchased with tickets are yours to keep forever.
                                <br /><br />
                                Donor commands (navigation, etc) will not be usable and home limits will be decreased.<br />
                                However, <strong>you will not lose access to your homes</strong> even if you are over the limit - instead you will be unable to use <span class="command">/sethome</span> until you delete the required amount.
                                <br /><br />
                                Subscribers will not lose their perks, provided that their payment isn't declined.
                            </li>
                            <li>
                                <h2>What if I Boost the Discord server?</h2>

                                We really appreciate <a href="https://support.discord.com/hc/en-us/articles/360028038352-Server-Boosting-FAQ-" target="_blank" rel="noopener noreferrer">Server Boosts</a> too,
                                as it benefits everyone using our Discord server.
                                Please reach out to staff on Discord or the forums and we can assign you the equivalent Donation Tier from above, for the duration that you continue to boost.
                                <br /><br />
                                However, should you choose to stop boosting, we ask that you please notify us as soon as possible.
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
                            <li>Please check for any applicable fees as Stripe/PayPal can change these at any time for any reason, beyond our control.</li>
                            <li>
                                Please remember that donating does not exempt you from obeying the server rules.<br />
                                Regardless of how much you donate, you will not receive special pardons or privileges for failure to comply.<br />
                                You are not paying for a service.
                            </li>
                        </ul>
                    </div>
                </div>
            </section>
        </main>

        <footer>
            @include('v2.front.components.sitemap')
        </footer>
    </div>
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

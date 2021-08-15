@extends('v2.front.templates.master')

@section('title', 'Donate')
@section('description', "Help keep us online by donating")

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
                        All proceeds go towards the high running expense of our servers and website - <strong>nothing goes into our pockets</strong>.
                        <br /><br />
                        Please consider donating to help keep us online for another year
                    </div>
                </div>

                <div class="hero-header__right">
                    @include('v2.front.components.donation-bar', [
                        'current' => $donations['raised_this_year'],
                        'percentage'=> $donations['percentage'],
                    ])

                    Annual Goal: $1000
                </div>
            </div>
        </header>

        <main>
            <section class="donate-tiers">
                <div class="container">
                    <h1>Donation Tiers</h1>

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
                                    <strong>Each day</strong> you'll receive
                                </div>
                                <div class="donation-tier__boxes-reward">
                                    <i class="fas fa-box"></i>
                                    <strong>1</strong>
                                    <span>Mystery Box (<a href="#mystery-box">what's this?</a>)</span>
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
                                <button class="button button--filled" onclick="openModal('modal-1')">
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
                                    <strong>Each day</strong> you'll receive
                                </div>
                                <div class="donation-tier__boxes-reward">
                                    <i class="fas fa-box"></i>
                                    <strong>4</strong>
                                    <span>Mystery Boxes (<a href="#mystery-box">what's this?</a>)</span>
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
                                <button class="button button--filled">Purchase</button>
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
                                    <strong>Each day</strong> you'll receive
                                </div>
                                <div class="donation-tier__boxes-reward">
                                    <i class="fas fa-box"></i>
                                    <strong>10</strong>
                                    <span>Mystery Boxes (<a href="#mystery-box">what's this?</a>)</span>
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
                                <button class="button button--filled">Purchase</button>
                            </div>
                        </div>
                    </div>

                    <div class="donation-tier-fineprint">
                        Your payment details will be processed by <a href="https://stripe.com" target="_blank" rel="noopener noreferrer">Stripe</a>, and only a record of your donation will be stored by PCB.<br />
                        In accordance with data retention laws, <strong>we do not store any credit card or bank account information</strong>.
                    </div>
                </div>
            </section>

            <a name="mystery-box"></a>
            <section class="donate-mystery-box">
                <div class="container">
                    <img src="{{ asset('assets/images/mystery-box-title.png') }}" />

                    <div class="donate-mystery-box__title">
                        Each box contains either cool loot or currency. Some of the possible loot includes
                        <strong>animated hats</strong>, <strong>particle effects</strong>, <strong>pets</strong>, <strong>fun gadgets</strong>, <strong>cloaks</strong>, <strong>emotes</strong> and <strong>mob morphs</strong>
                        from a rotation of items strictly limited to donors.
                        <br /><br />
                        Didn’t get the loot you wanted? Currency can be redeemed to directly buy what you want.
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
                                For example, if you started your subscription on August 1st and immediately cancel it, your perks will remain until the midnight of September 1st.
                            </li>
                            <li>
                                <h2>What if I Boost the Discord server?</h2>

                                We really appreciate any server <a href="https://support.discord.com/hc/en-us/articles/360028038352-Server-Boosting-FAQ-" target="_blank" rel="noopener noreferrer">Boosts</a> too,
                                as it benefits everyone using our Discord server.
                                Please reach out to staff on Discord or the forums and we can assign you the equivalent Donation Tier from above, for the duration that you continue to boost.
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <section class="donate-legal">
                <div class="container">
                    <h1>Legal Stuff</h1>
                    <div class="donate-legal__contents">
                        <ul>
                            <li>Please check for any applicable fees as Stripe/PayPal can change these at any time for any reason, beyond PCB’s control.</li>
                            <li>Please remember that donating does not exempt you from obeying the server rules. You will get no pardons or privileges regardless of how much you have donated as a donation is just that, a donation. You are not paying for a service.</li>
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
    <div class="modal micromodal-slide" id="modal-1" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
                <header class="modal__header">
                    <h2>
                        How do you wish to pay?
                    </h2>
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
                            <input type="hidden" name="price_id" value="price_1JJL5mAtUyfM4v5ISwJrrVur" />
                            <input type="hidden" name="product_id" value="prod_JxFaAltmFPewxs" />
                            <input type="hidden" name="quantity" value="1" />
                            <input type="hidden" name="is_subscription" value="1" />
                            <button type="submit" class="button button--filled">
                                <i class="fas fa-external-link-alt"></i> Proceed
                            </button>
                        </form>
                    </section>
                    <section class="modal__section">
                        <h1><i class="fas fa-dollar-sign"></i> One-Time</h1>
                        <div class="modal__section_description">
                            If you wish to pay just once, up-front, please select the number of months of perks you would like.
                            Your perks will expire after the given number of months have elapsed (can be extended at any time)
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
                                />
                                <span>
                                    months of <strong>TODO</strong>
                                </span>
                            </div>

                            <input type="hidden" name="price_id" value="price_1JJL5mAtUyfM4v5IJNHp1Tk2" />
                            <input type="hidden" name="product_id" value="prod_JxFaAltmFPewxs" />
                            <input type="hidden" name="is_subscription" value="0" />
                            <button type="submit" class="button button--filled">
                                <i class="fas fa-external-link-alt"></i> Proceed
                            </button>
                        </form>
                    </section>
                </main>
            </div>
        </div>
    </div>

    <script>
        MicroModal.init();

        function openModal(target) {
            MicroModal.show(target)
        }
    </script>
@endpush

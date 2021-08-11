@extends('v2.front.templates.master')

@section('title', 'Donate')
@section('description', "Help keep us online by donating")

@section('body')
    <div class="page donate">
        <header class="hero-header">
            <div class="container">
                <div class="hero-header__left">
                    <h1>Help Keep Us Online</h1>

                    <div class="header-desc">
                        PCB is only able to operate thanks to the continuous support of our community and volunteers.
                        All proceeds go towards the high running expense of our servers and website - <strong>nothing goes into our pockets</strong>.

                        Please consider donating to help keep us online for another year
                    </div>
                </div>

                <div class="hero-header__right">
                    <div class="donation-bar">
                        <div class="donation-bar__outer">
                            <div class="donation-bar__inner" style="width: 25%; min-width: 75px">
                                $100.00
                            </div>
                        </div>
                        <ul class="donation-bar__indicators">
                            <li>$0</li>
                            <li>$250</li>
                            <li>$500</li>
                            <li>$750</li>
                            <li>$1000</li>
                        </ul>
                    </div>
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
                                    <span>Mystery Box (<a href="#">what's this?</a>)</span>
                                </div>
                            </div>

                            <div class="donation-tier__perks">
                                <div class="donation-tier__perks-title">Plus, you'll receive</div>

                                <ul>
                                    <li><i class="fas fa-home"></i> <strong>+3</strong> additional homes</li>
                                    <li><i class="fas fa-check-circle"></i> Donor rank and in-game title</li>
                                    <li><i class="fas fa-check-circle"></i> Custom nickname (/nick command)</li>
                                    <li><i class="fas fa-check-circle"></i> Flyspeed and Walkspeed commands</li>
                                </ul>
                            </div>

                            <div class="donation-tier__footer">
                                <button class="button button--filled">Purchase</button>
                            </div>
                        </div>

                        <div class="donation-tier__spacer"></div>

                        <div class="donation-tier">
                            <div class="donation-tier__bar donation-tier__bar--iron"></div>

                            <div class="donation-tier__header">
                                <img src="{{ asset('assets/images/icon_copper_tier.png') }}" />
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
                                    <span>Mystery Box (<a href="#">what's this?</a>)</span>
                                </div>
                            </div>

                            <div class="donation-tier__perks">
                                <div class="donation-tier__perks-title">Plus, you'll receive</div>

                                <ul>
                                    <li><i class="fas fa-home"></i> <strong>+8</strong> additional homes</li>
                                    <li><i class="fas fa-check-circle"></i> Donor rank and in-game title</li>
                                    <li><i class="fas fa-check-circle"></i> Custom nickname (/nick command)</li>
                                    <li><i class="fas fa-check-circle"></i> Flyspeed and Walkspeed commands</li>
                                    <li><i class="fas fa-check-circle"></i> /tp and /tppos commands</li>
                                    <li><i class="fas fa-check-circle"></i> /ascend and/thru commands</li>
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
                                <img src="{{ asset('assets/images/icon_copper_tier.png') }}" />
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
                                    <span>Mystery Box (<a href="#">what's this?</a>)</span>
                                </div>
                            </div>

                            <div class="donation-tier__perks">
                                <div class="donation-tier__perks-title">Plus, you'll receive</div>

                                <ul>
                                    <li><i class="fas fa-home"></i> <strong>+15</strong> additional homes</li>
                                    <li><i class="fas fa-check-circle"></i> Donor rank and in-game title</li>
                                    <li><i class="fas fa-check-circle"></i> Custom nickname (/nick command)</li>
                                    <li><i class="fas fa-check-circle"></i> Flyspeed and Walkspeed commands</li>
                                    <li><i class="fas fa-check-circle"></i> /tp and /tppos commands</li>
                                    <li><i class="fas fa-check-circle"></i> /ascend and/thru commands</li>
                                    <li><i class="fas fa-check-circle"></i> Nickname colors</li>
                                </ul>
                            </div>

                            <div class="donation-tier__footer">
                                <button class="button button--filled">Purchase</button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer>
            @include('v2.front.components.sitemap')
        </footer>
    </div>

{{--    <form action="{{ route('front.donations.checkout') }}" method="POST">--}}
{{--        @csrf--}}
{{--        <input type="hidden" name="price_id" value="price_1JJL5mAtUyfM4v5IJNHp1Tk2" />--}}
{{--        <input type="hidden" name="product_id" value="prod_JxFaAltmFPewxs" />--}}
{{--        <input type="hidden" name="quantity" value="1" />--}}
{{--        <input type="hidden" name="is_subscription" value="0" />--}}
{{--        <button type="submit">One Time</button>--}}
{{--    </form>--}}
{{--    <form action="{{ route('front.donations.checkout') }}" method="POST">--}}
{{--        @csrf--}}
{{--        <input type="hidden" name="price_id" value="price_1JJL5mAtUyfM4v5ISwJrrVur" />--}}
{{--        <input type="hidden" name="product_id" value="prod_JxFaAltmFPewxs" />--}}
{{--        <input type="hidden" name="quantity" value="1" />--}}
{{--        <input type="hidden" name="is_subscription" value="1" />--}}
{{--        <button type="submit">Subscription</button>--}}
{{--    </form>--}}
@endsection

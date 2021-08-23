@extends('v2.front.templates.master')

@section('title', 'Thank you for your donation')

@push('head')
    <script src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>
@endpush

@section('body')
    <div class="page donate">
        <main>
            <section class="donate-tiers">
                <div class="container">
                    <h1>Thank you for your donation!</h1>

                    <div class="donate-tiers__overview">
                        Your perks have been assigned to your account.<br />
                        If you are currently in-game, please reconnect to receive your perks.

                        <div class="donate__confirmation--info">
                            In the case where you don't immediately receive your perks, please wait a few minutes as your payment may still
                            be processing.
                            <br /><br />
                            If you have any problems, please do not hesitate to reach out to staff on the forums, Discord or in-game.
                        </div>

                        <br /><br />

                        <a class="button button--filled button--secondary" href="{{ route('front.home') }}">
                            Back to Home
                        </a>

                        <a class="button button--filled button--secondary" href="{{ route('front.account.billing') }}">
                            Billing Portal
                        </a>

                        <a class="button button--filled button--primary" href="{{ route('front.account.donations') }}">
                            See your donations <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </section>

            <section class="donate-legal">
                <div class="container">
                    <h1>Your New Perks</h1>
                    <div class="donate-legal__contents">
                        <ul>
                            <li>
                                <h2><i class="fas fa-box-open"></i> Loot Boxes</h2>
                                Each day you can redeem Mystery Boxes in-game by using the <span class="command">/box redeem</span> command.<br />
                                Mystery Boxes can be opened via the Vault (<span class="command">/warp vault</span>).<br />
                                Or craft more Mystery Boxes from currency at the Vault.
                            </li>
                            <li>
                                <h2><i class="fas fa-user-astronaut"></i> Cosmetics</h2>
                                You can access your cosmetics at any time via the <span class="command">/menu</span> command.
                            </li>
                            <li>
                                <h2><i class="fas fa-home"></i> More Homes</h2>
                                Your maximum number of Homes has been increased. Use <span class="command">/home</span> and <span class="command">/sethome</span>
                            </li>
                            <li>
                                <h2><i class="fas fa-running"></i> Easier Navigation</h2>
                                Change your fly and walkspeed with <span class="command">/flyspeed</span> and <span class="command">/walkspeed</span> respectively.
                                <br /><br />
                                (For Iron Tier and above)<br />
                                Teleport to a coordinate with <span class="command">/tppos</span> or directly to a player with <span class="command">/tp</span>.<br />
                                Teleport through blocks with <span class="command">/ascend</span> and <span class="command">/thru</span>
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

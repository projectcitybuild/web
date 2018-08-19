@extends('front.layouts.master')

@section('title', 'Donate')
@section('description', "Help keep us online by donating")

@push('head')
    <meta name="stripe-key" content="{{ config('services.stripe.key') }}" />
    <meta name="stripe-submit" content="{{ route('front.donate.charge') }}" />
@endpush

@section('contents')

    <div class="donate">
        <div class="donate__left">

            <div class="donate__section">
                <h1>Help Keep Us Online</h1>
                <h4 class="primary"><i class="fas fa-exclamation-circle"></i> Donations are the only way to keep our community running!</h4>

                <span class="text">
                    Thank you for considering a donation to PCB. All proceeds go towards the running expenses of our servers and website.
                    <p />
                    Whilst you can donate any amount you like, due to processing fees the minimum we would request you donate is $3 (USD).
                
                    <ul class="no-list">
                        <li><i class="fas fa-long-arrow-alt-right"></i> Donating at least <strong>$3 (USD)</strong> will provide you with 1 month worth of Donator perks</li>
                        <li><i class="fas fa-long-arrow-alt-right"></i> Donating at least <strong>$30 (USD)</strong> will provide you with Donator perks for life</li>
                    </ul>

                    Every $3 donated grants 1 month of Donator perks.
                </span>
            </div>
            <div class="donate__section">
                <h3>Donator perks</h3>
                <ul>
                    <li>In-game [$] (Donator) tag</li>
                    <li>Coloured nickname</li>
                </ul>

                You will also receive Trusted rank permissions:
                <ul>
                    <li>Flying</li>
                    <li>Custom nicknames</li>
                    <li>Teleporting</li>
                    <li>Death chests</li>
                    <li>Increased home limit</li>
                </ul>

                If you would like to donate via a different method, please let us know. We also support PayPal and bank transfers.
            </div>
            <div class="donate__section">
            
                <h3>Terms and Conditions</h3>
                <ul>
                    <li>Please check for any applicable fees as Stripe/PayPal can change these at any time for any reason, beyond PCB’s control.</li>
                    <li>We’ve been made aware that including the word “donation” in your message may cause the payment to be held by PayPal, as we’re not a charity. Please refrain from using this word in your message.</li>
                    <li>Please remember that donating does not exempt you from obeying the server rules. You will get no pardons or privileges regardless of how much you have donated as a donation is just that, a donation. <strong>You are not paying for a service</strong>.</li>
                </ul>

                <blockquote>
                    Community Rules
                    6. Nicknames - nicknames must be similar to your in-game name (IGN)
                    Nicknames cannot be excessive in visible length (staff discretion applies).

                    For Donators, or other players with coloured nickname permission, your nickname colour may not resemble that of a staff rank (colours that are associated with a staff rank) . If you are unsure then request approval from a staff member. You may use a mixture of staff and other colours

                    Exceptions may be made if a player is more commonly known by another name
                </blockquote>
            </div>
        </div>
        
        <div class="donate__right">
            <h1>Donate now</h1>

            @guest
                <div class="alert alert--warning">
                    <strong>You are not currently logged in</strong>. If you do not login, you will not receive your donator perks!
                </div>
            @endguest

            <div id="donation-amount"></div>

            <small>
                Your payment details will be processed by <a href="https://stripe.com/" rel="noopener" target="_blank">Stripe</a>, and only a record of your donation will be stored by PCB.
                In accordance with data laws, we <strong>do not retain any credit card or bank account information</strong>.
            </small>
        </div>

    </div>

@endsection
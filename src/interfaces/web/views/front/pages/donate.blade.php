@extends('front.layouts.master')

@section('title', 'Donate')
@section('description', "Help keep us online by donating")

@section('contents')

    <div class="donate">
        <div class="donate__left">
            <h1>Help Keep Us Online</h1>
            <h3>Donations are the only way to keep our community running!</h3>

            Thank you for considering a donation to PCB. All proceeds go towards the running expenses of the server and wesbite.
            <p />
            Whilst you can donate any amount you like, due to processing fees, the minimum we would request you donate is $3 (USD).
        
            <ul>
                <li>$3 (USD) - Donating at least $3 (USD) will provide you with 1 month worth of Donator perks</li>
                <li>$30 (USD) - Donating at least $30 (USD) will provide you with Donator perks for life</li>
            </ul>

            <hr />

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

            <hr />

            If you would like to donate via a different method, please let us know. We also support PayPal and bank transfers.
        
            <hr />

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
        <div class="donate__right">
            <h2>Donate now</h2>

            <button class="button button--secondary">$5</button>
            <button class="button button--secondary">$10</button>
            <button class="button button--secondary">$20</button>
            <button class="button button--secondary">$30</button>
            <button class="button button--secondary">Custom</button>

            <input class="input-text" type="text" placeholder="3.00" />

            <button class="button button--secondary">Once</button>
            <button class="button button--secondary">Monthly</button>

            You must be logged-in to receive your perks

            <form action="{{ route('front.donate.charge') }}" method="POST">
                @csrf
                <script
                    src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                    data-key="{{ config('services.stripe.key') }}"
                    data-amount="300"
                    data-name="Project City Build"
                    data-description="One-Time Donation"
                    data-image="https://forums.projectcitybuild.com/uploads/default/original/1X/847344a324d7dc0d5d908e5cad5f53a61372aded.png"
                    data-locale="auto"
                    data-currency="aud">
                </script>
            </form>

            <hr />

            <small>
                Your payment details will be processed by <a href="https://stripe.com/" rel="noopener" target="_blank">Stripe</a>, and only a record of your donation will be stored by PCB.
                In accordance with the law, we <strong>do not retain any credit card or banking details</strong>.
            </small>
        </div>

    </div>

@endsection
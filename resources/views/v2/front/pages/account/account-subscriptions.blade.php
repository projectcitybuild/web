@extends('v2.front.templates.master')

@section('title', 'Subscriptions - Your Account - Project City Build')
@section('description', '')

@section('body')
    <header class="image-header">
        <div class="container">
            <h1>Your Account</h1>
        </div>
    </header>

    <main class="page settings">
        @include('v2.front.pages.account.components.account-sidebar')
        <div class="settings__content">
            <div class="settings__section">
                <h2 class="settings__section-heading">Your Subscriptions</h2>
                <p class="settings__description">A record of all your monthly subscriptions.<br>If
                    you've subscribed to a Donation Tier, but it hasn't appeared here, please contact PCB staff via <a
                        href="https://forums.projectcitybuild.com/c/support/">our discussion forums</a> or Discord.</p>
            </div>

            @if($subscriptions->count() == 0)
                <div class="settings__empty-placeholder">
                    <div><i class="fas fa-credit-card fa-2x"></i></div>
                    <p>You do not have any subscription history.</p>
                </div>
            @else
                <table class="table table--first-col-padded table--striped">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($donationPerks as $perk)
                        <tr>
                            @if($perk->donation != null)
                                <td>{{ $perk->donation->created_at->toFormattedDateString() }}</td>
                                <td>${{ number_format($perk->donation->amount, 2) }}</td>
                            @else
                                <td>Unable to find transaction</td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </main>
@endsection

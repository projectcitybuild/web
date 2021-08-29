@extends('v2.front.templates.master')

@section('title', 'Donations - Your Account - Project City Build')
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
                <h2 class="settings__section-heading">Your Donations</h2>
                <p class="form__description">A record of all your contributions to help keep PCB running.<br>If
                    you've donated, but it hasn't appeared here, please contact PCB staff via <a
                        href="https://forums.projectcitybuild.com/c/support/">our discussion forums</a> or Discord.</p>
            </div>

            @if($donations->count() == 0)
                <div class="settings__empty-placeholder">
                    <div><i class="fas fa-credit-card fa-2x"></i></div>
                    <p>You have not made any donations.</p>
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
                    @foreach($donations as $donation)
                        <tr>
                            <td>{{ $donation->created_at->toFormattedDateString() }}</td>
                            <td>${{ number_format($donation->amount, 2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif

            <div class="settings__section">
                <h2 class="settings__section-heading">Your Perks</h2>
                <p class="settings__description">Note: Subscriptions will auto-renew their associated perk prior to its expiry date</p>
            </div>

            @if($donationPerks->count() == 0)
                <div class="settings__empty-placeholder">
                    <div><i class="fas fa-credit-card fa-2x"></i></div>
                    <p>You do not have any perks.</p>
                </div>
            @else
                <table class="table table--first-col-padded table--striped">
                    <thead>
                    <tr>
                        <th>Perk</th>
                        <th>Status</th>
                        <th>Start Date</th>
                        <th>Expiry Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($donationPerks as $perk)
                        <tr>
                            <td>
                                @if($perk->donationTier)
                                    {{ $perk->donationTier->name }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $perk->isActive() ? 'Active' : 'Expired' }}</td>
                            <td>{{ $perk->donation->created_at->toFormattedDateString() }}</td>
                            <td>
                                {{ $perk->expires_at->toFormattedDateString() }}
                                @if ($perk->isActive() && $perk->expires_at !== null)
                                    ({{ now()->diff($perk->expires_at)->days }} days remaining)
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </main>
@endsection

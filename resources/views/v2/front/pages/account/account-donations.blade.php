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

            @if($donationPerks->count() == 0)
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
                        <th>Expires</th>
                        <th>Status</th>
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
                            <td>
                                @if($perk->is_lifetime_perks)
                                    Lifetime
                                @else
                                    {{ $perk->expires_at->toFormattedDateString() }}
                                @endif
                            </td>
                            <td>
                                {{ $perk->is_active ? 'Active' : 'Expired' }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </main>
@endsection

@extends('front.layouts.master')

@section('title', 'Your Donations')
@section('description', '')

@section('contents')
    <div class="contents__body">
        <div class="card card--divided">
            <div class="card__body card__body--padded">
                <h1>Donations</h1>
                <span class="header-description">A record of all your contributions to help keep PCB running</span>
                <p class="header-description">If you've donated, but it hasn't appeared here, please contact PCB staff via <a
                        href="https://forums.projectcitybuild.com/c/support/">our discussion forums</a> or Discord.</p>
            </div>
            @if($donationPerks->count() == 0)
                <div class="card__body card__body--padded">
                    <p>You haven't donated yet! Donations are the only way to keep PCB running.</p>
                </div>
            @else
                <div class="card__body card--no-padding">
                    <table class="table table--striped table--first-col-padded">
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
                </div>
            @endif
        </div>
    </div>

    <div class="contents__sidebar">
        @include('front.pages.account.components.account-sidebar')
    </div>
@endsection

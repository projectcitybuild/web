@extends('front.layouts.master')

@section('title', 'Donator List')
@section('description', "List of all players who have donated to Project City Build")

@section('contents')

    <div class="donation-list">

        <div class="donation-list__left">
            <div class="card">
                <div class="card__body">
                    <h2>Donations this year</h2>
                    <table class="table table--striped">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Date of Donation</th>
                                <th>Amount Donated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($donationsThisYear as $donation)
                            <tr>
                                <td>
                                    @if (
                                        $donation->account === null ||
                                        $donation->account->minecraftAccount === null ||
                                        $donation->account->minecraftAccount->first()->aliases === null
                                    )
                                        Anonymous
                                    @else
                                        <img src="https://minotar.net/avatar/{{ $donation->account->minecraftAccount->first()->aliases->last()->alias }}/16" width="16" height="16" alt="">
                                        {{ $donation->account->minecraftAccount->first()->aliases->last()->alias }}
                                    @endif
                                </td>
                                <td>{{ $donation->created_at->format('jS \o\f F, Y') }}</td>
                                <td>${{ number_format($donation->amount, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card__body">
                    <h2>Past donations</h2>
                    <table class="table table--striped">
                        <thead>
                        <tr>
                            <th>User</th>
                            <th>Date of Donation</th>
                            <th>Amount Donated</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pastDonations as $donation)
                            <tr>
                                <td>
                                    @if (
                                        $donation->account === null ||
                                        $donation->account->minecraftAccount === null ||
                                        $donation->account->minecraftAccount->first()->aliases === null
                                    )
                                        Anonymous
                                    @else
                                        <img src="https://minotar.net/avatar/{{ $donation->account->minecraftAccount->first()->aliases->last()->alias }}/16" width="16" height="16" alt="">
                                        {{ $donation->account->minecraftAccount->first()->aliases->last()->alias }}
                                    @endif
                                </td>
                                <td>{{ $donation->created_at->format('jS \o\f F, Y') }}</td>
                                <td>${{ number_format($donation->amount, 2) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card__footer">
                    {{ $pastDonations->links('vendor.pagination.default') }}
                </div>
            </div>
        </div>

        <div class="donation-list__right">
            <div class="card">
                <div class="card__body">
                    <h2>Hall of Fame</h2>

                    <table class="table table--striped">
                        <thead>
                        <tr>
                            <th>User</th>
                            <th>Total Donated</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($highestDonators as $donation)
                            <tr>
                                <td>
                                    @if (
                                        $donation->account === null ||
                                        $donation->account->minecraftAccount === null ||
                                        $donation->account->minecraftAccount->first()->aliases === null
                                    )
                                        Anonymous
                                    @else
                                        <img src="https://minotar.net/avatar/{{ $donation->account->minecraftAccount->first()->aliases->last()->alias }}/16" width="16" height="16" alt="">
                                        {{ $donation->account->minecraftAccount->first()->aliases->last()->alias }}
                                    @endif
                                </td>
                                <td>${{ number_format($donation->total_amount, 2) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

@endsection

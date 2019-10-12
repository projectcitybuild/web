@extends('front.layouts.master')

@section('title', 'Donations - Staff Panel')

@section('contents')
    <div class="staff-panel">
        <h1>Donations</h1>

        <table class="table table--divided">
            <thead>
            <tr>
                <th colspan="8">
                    <a href="{{ route('front.panel.donations.index') }}" class="button button--secondary">Donations</a>
                    <a href="{{ route('front.panel.donation-perks.index') }}" class="button button--secondary">Donator Perks</a>

                    <a href="{{ route('front.panel.donations.create') }}" class="button button--primary"><i class="fas fa-plus"></i> Manually Add Donation</a>
                </th>
            </tr>
            <tr>
                <th>
                    ID
                </th>
                <th>
                    Amount Donated
                </th>
                <th>
                    Username
                </th>
                <th>
                    Donation Date
                </th>
                <th>
                    Manage
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach($donations as $donation)
                <tr>
                    <td>{{ $donation->donation_id }}</td>
                    <td>${{ $donation->amount }}</td>
                    <td>
                        @isset($donation->account)
                            <a href="{{ route('front.panel.accounts.show', $donation->account->account_id) }}">
                                {{ $donation->account->username ?: '(Unset)' }}
                            </a>
                        @else
                            Guest
                        @endif
                    </td>
                    <td>{{ $donation->created_at }}</td>
                    <td><a href="{{ route('front.panel.donations.edit', $donation->donation_id) }}">Edit</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $donations->links('vendor.pagination.default') }}
    </div>
@endsection

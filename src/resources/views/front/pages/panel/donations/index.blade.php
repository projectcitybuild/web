@extends('front.layouts.master')

@section('title', 'Donations - Staff Panel')

@section('contents')
    <div class="staff-panel">
        <h1>Donations</h1>

        <table class="table table--divided">
            <thead>
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
                    Lifetime Perks?
                </th>
                <th>
                    Perks Expiry Date
                </th>
                <th>
                    Active
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
                    <td>{{ $donation->is_lifetime_perks ? 'Yes' : 'No' }}</td>
                    <td>{{ $donation->perks_end_at }}</td>
                    <td>{{ $donation->is_active ? 'Yes' : 'No' }}</td>
                    <td><a href="{{ route('front.panel.donations.show', $donation->donation_id) }}">Edit</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $donations->links('vendor.pagination.default') }}
    </div>
@endsection

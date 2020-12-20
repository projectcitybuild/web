@extends('front.layouts.master')

@section('title', 'Donation Perks - Staff Panel')

@section('contents')
    <div class="staff-panel">
        <h1>Donation Perks</h1>

        <table class="table table--divided">
            <thead>
            <tr>
                <th colspan="8">
                    <a href="{{ route('front.panel.donations.index') }}" class="button button--secondary">Donations</a>
                    <a href="{{ route('front.panel.donation-perks.index') }}" class="button button--secondary">Donator Perks</a>

                    <a href="{{ route('front.panel.donation-perks.create') }}" class="button button--primary"><i class="fas fa-plus"></i> Manually Add Donation Perk</a>
                </th>
            </tr>
            <tr>
                <th>
                    ID
                </th>
                <th>
                    Donation
                </th>
                <th>
                    Receiving Username
                </th>
                <th>
                    Start Date
                </th>
                <th>
                    End Date
                </th>
                <th>
                    Lifetime?
                </th>
                <th>
                    Active?
                </th>
                <th>
                    Manage
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach($perks as $perk)
                <tr>
                    <td>{{ $perk->donation_perks_id }}</td>
                    <td>
                        <a href="{{ route('front.panel.donations.edit', $perk->donation->donation_id) }}">
                            {{ $perk->donation->donation_id }}
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('front.panel.accounts.show', $perk->account->account_id) }}">
                            {{ $perk->account->username ?: '(Unset)' }}
                        </a>
                    </td>
                    <td>{{ $perk->created_at }}</td>
                    <td>{{ $perk->expires_at }}</td>
                    <td>{{ $perk->is_lifetime_perks ? 'Yes' : 'No' }}</td>
                    <td>{{ $perk->is_active ? 'Yes' : 'No' }}</td>
                    <td><a href="{{ route('front.panel.donation-perks.edit', $perk->donation_perks_id) }}">Edit</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $perks->links('vendor.pagination.default') }}
    </div>
@endsection

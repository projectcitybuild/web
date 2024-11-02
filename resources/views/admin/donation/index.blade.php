@extends('admin.layouts.admin')

@section('title', 'Donations')

@section('toolbar')
    <div class="btn-group btn-group-sm" role="group">
        <a href="{{ route('manage.donations.create') }}" class="btn btn-outline-primary"><i class="fas fa-plus"></i> Create</a>
    </div>
@endsection

@section('body')
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Amount</th>
                <th>Perks</th>
                <th>Donator</th>
                <th>Date</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($donations as $donation)
                <tr>
                    <td>{{ $donation->donation_id }}</td>
                    <td>${{ number_format($donation->amount, 2) }}</td>
                    <td>{{ $donation->perks->count() }}</td>
                    <td>
                        @isset($donation->account)
                            <a href="{{ route('manage.accounts.show', $donation->account->account_id) }}">
                                {{ $donation->account->username ?? '(Unset)' }}
                            </a>
                        @else
                            Guest
                        @endif
                    </td>
                    <td>{{ $donation->created_at }}</td>
                    <td><a href="{{ route('manage.donations.show', $donation->donation_id) }}">Manage</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{ $donations->links('vendor.pagination.bootstrap-4') }}
@endsection

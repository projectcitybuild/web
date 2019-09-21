@extends('front.layouts.master')

@section('title', 'Accounts - Staff Panel')

@section('contents')
    <div class="staff-panel">
        <h1>Accounts</h1>

        <table class="table table--divided">
            <thead>
            <tr>
                <th>
                    ID
                </th>
                <th>
                    Email
                </th>
                <th>
                    Username
                </th>
                <th>
                    Last Logged in
                </th>
                <th>
                    Manage
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach($accounts as $account)
                <tr>
                    <td>{{ $account->account_id }}</td>
                    <td>{{ $account->email }}</td>
                    <td>{{ $account->username }}</td>
                    <td>{{ $account->last_login_at }}</td>
                    <td><a href="{{ route('front.panel.accounts.show', $account->account_id) }}">Manage</a> | <a
                            href="{{ route('front.panel.accounts.discourse-admin-redirect', $account->account_id) }}"
                            target="_blank">Discourse</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $accounts->links('vendor.pagination.default') }}
    </div>
@endsection

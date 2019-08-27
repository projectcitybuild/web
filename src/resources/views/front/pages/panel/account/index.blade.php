@extends('front.layouts.master')

@section('title', 'Player Ban List')
@section('description', 'Players listed on this page are currently banned on one or more servers on our game network')

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
                    <td><a href="#">Manage</a> | <a href="#">Forums</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $accounts->links('vendor.pagination.default') }}
    </div>
@endsection

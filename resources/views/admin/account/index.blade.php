@extends('admin.layouts.admin')

@section('title', $title ?? 'Accounts')

@section('toolbar')
    @if($showSearch ?? true)
    <div class="d-flex align-items-center">
        @if(!empty($query))
            <a href="{{ route('front.panel.accounts.index') }}" class="d-inline-block me-5 link-danger"><i class="fas fa-times me-2"></i>Clear</a>
        @endif
        <form action="{{ route('front.panel.accounts.index') }}" method="get">
            <div class="input-group input-group-sm">
                <input type="text" class="form-control" name="query" value="{{ $query }}" aria-describedby="button-search">
                <button class="btn btn-outline-secondary" type="submit" id="button-search"><i class="fas fa-search" aria-label="Search"></i></button>
            </div>
        </form>
    </div>
    @endif
@endsection

@section('body')
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th></th>
                <th>ID</th>
                <th>Email</th>
                <th>Username</th>
                <th>Last Logged In</th>
                <th>Manage</th>
            </tr>
            </thead>
            <tbody>
            @foreach($accounts as $account)
                <tr>
                    <td data-bs-toggle="tooltip" data-bs-placement="left" title="{{ $account->is_totp_enabled ? '2FA Enabled' : '2FA Disabled' }}">
                        @if($account->is_totp_enabled)
                            <i class="fas fa-lock fa-fw text-success me-2"></i>
                        @else
                            <i class="fas fa-lock-open fa-fw text-muted me-2"></i>
                        @endif
                    </td>
                    <td>{{ $account->account_id }}</td>
                    <td>{{ $account->email }}</td>
                    <td>{{ $account->username }}</td>
                    <td>{{ $account->last_login_at }}</td>
                    <td class="actions">
                        <a href="{{ route('front.panel.accounts.show', $account->account_id) }}">Manage</a>
                    </td>
                </tr>
            @endforeach
            </tbody>

        </table>

        {{ $accounts->links('vendor.pagination.bootstrap-4') }}
    </div>
@endsection


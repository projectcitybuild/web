@extends('front.templates.master')

@section('title', 'Infractions - Your Account - Project City Build')
@section('description', '')

@section('body')
    <header class="image-header">
        <div class="container">
            <h1>Your Account</h1>
        </div>
    </header>

    <main class="page settings">
        @include('front.pages.account.components.account-sidebar')
        <div class="settings__content">
            <div class="settings__section">
                <h2 class="settings__section-heading">Bans</h2>
                <p class="form__description">If you wish to appeal a ban, use the <a href="{{ route('front.appeal') }}">appeal form</a>.</p>
            </div>

            @if($account->gamePlayerBans->count() == 0)
                <div class="settings__empty-placeholder">
                    <div><i class="fas fa-ban fa-2x"></i></div>
                    <p>You have not been banned.</p>
                </div>
            @else
                <table class="table table--first-col-padded table--striped">
                    <thead>
                    <tr>
                        <th>Status</th>
                        <th>Reason</th>
                        <th>Banned By</th>
                        <th>Ban Date</th>
                        <th>Expires At</th>
                        <th>Unbanned At</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($account->gamePlayerBans as $ban)
                        <tr>
                            <td>{{ $ban->isActive() ? 'Active' : 'Inactive' }}</td>
                            <td>{{ $ban->reason }}</td>
                            <td>{{ $ban->bannerPlayer?->getBanReadableName() ?? 'System' }}</td>
                            <td>{{ $ban->created_at?->toFormattedDateString() ?? 'Unknown' }}</td>
                            <td>{{ $ban->expires_at?->toFormattedDateString() ?? 'Never' }}</td>
                            <td>{{ $ban->unbanned_at?->toFormattedDateString() ?? '-' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif

            <div class="settings__section">
                <h2 class="settings__section-heading">Warnings</h2>
                <p class="settings__description">Any infractions incurred. Too many warnings may result in bans or other consequences.</p>

                @if(session()->has('success'))
                    <div class="alert alert--success">
                        <h2><i class="fas fa-check"></i> Success</h2>
                        {{ session()->get('success') }}
                    </div>
                @endif
            </div>

            @if($account->warnings->count() == 0)
                <div class="settings__empty-placeholder">
                    <div><i class="fas fa-exclamation-triangle fa-2x"></i></div>
                    <p>You have not been issued any warnings.</p>
                </div>
            @else
                <table class="table table--first-col-padded table--striped">
                    <thead>
                    <tr>
                        <th>Reason</th>
                        <th>Warned By</th>
                        <th>Issue Date</th>
                        <th>Is Acknowledged?</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($account->warnings as $warning)
                        <tr class="{{ !$warning->is_acknowledged ? 'warning' : '' }}">
                            <td>{{ $warning->reason }}</td>
                            <td>{{ $warning->warnerPlayer->currentAlias()?->alias ?? '(No Alias)' }}</td>
                            <td>{{ $warning->created_at?->toFormattedDateString() ?? 'Unknown' }}</td>
                            <td>
                                <i class="fas {{ $warning->is_acknowledged ? 'fa-check' : 'fa-multiply' }}"></i>
                            </td>
                            <td>
                                @if (! $warning->is_acknowledged)
                                    <form method="post" action="{{ route('front.account.infractions.acknowledge', $warning) }}">
                                        @csrf
                                        <button type="submit" class="button button--filled button--is-small"><i class="fas fa-eye"></i> Acknowledge</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </main>
@endsection

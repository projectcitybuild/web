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
                <p class="form__description">A record of all your contributions to help keep PCB running.<br>If
                    you've donated, but it hasn't appeared here, please contact PCB staff via <a
                        href="https://forums.projectcitybuild.com/c/support/">our discussion forums</a> or Discord.</p>
            </div>

            @if($account->gameBans->count() == 0)
                <div class="settings__empty-placeholder">
                    <div><i class="fas fa-ban fa-2x"></i></div>
                    <p>You have not been banned.</p>
                </div>
            @else
                <table class="table table--first-col-padded table--striped">
                    <thead>
                    <tr>
                        <th>Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($account->gameBans as $ban)
                        <tr>
                            <td>{{ $ban->created_at?->toFormattedDateString() ?? "Unknown" }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif

            <div class="settings__section">
                <h2 class="settings__section-heading">Warnings</h2>
                <p class="settings__description">Note: Subscriptions will auto-renew their associated perk prior to its expiry date</p>
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
                        <tr class="test">
                            <td>{{ $warning->reason }}</td>
                            <td>{{ $warning->warnerPlayer->currentAlias()?->alias ?? '(No Alias)' }}</td>
                            <td>{{ $warning->created_at?->toFormattedDateString() ?? "Unknown" }}</td>
                            <td>
                                <i class="fas {{ $warning->is_acknowledged ? 'fa-check' : 'fa-multiply' }}"></i>
                            </td>
                            <td>
                                @if (! $warning->is_acknowledged)
                                    <a class="button button--filled button--is-small" href="">
                                        <i class="fas fa-eye"></i> Acknowledge
                                    </a>
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

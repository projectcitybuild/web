@extends('front.layouts.master')

@section('title', 'Accounts - Staff Panel')

@section('contents')
    <div class="staff-panel">
        <h1>Account: {{ $account->username }}</h1>

        @unless($account->activated)
        <div class="card">
            <div class="card__header"><i class="fas fa-exclamation-circle"></i> User hasn't activated account</div>
            <div class="card__body">
                This user is scheduled to be deleted
                on {{ $account->created_at->addDays(config('auth.unactivated_cleanup_days'))->toDayDateTimeString() }}
            </div>
            <div class="card__footer">
                <form class="inline" action="{{ route('front.panel.accounts.resend-activation', $account->account_id) }}" method="post">
                    @csrf
                    <button class="button button--primary" type="submit">Resend activation</button>
                </form>
                <form class="inline" action="{{ route('front.panel.accounts.activate', $account->account_id) }}" method="post">
                    @csrf
                    <button class="button button--secondary" type="submit">Manually activate</button>
                </form>
            </div>
        </div>
        @endunless

    </div>
@endsection

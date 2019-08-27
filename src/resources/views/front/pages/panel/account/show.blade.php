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
                    <form class="inline"
                          action="{{ route('front.panel.accounts.resend-activation', $account->account_id) }}"
                          method="post">
                        @csrf
                        <button class="button button--primary" type="submit">Resend activation</button>
                    </form>
                    <form class="inline" action="{{ route('front.panel.accounts.activate', $account->account_id) }}"
                          method="post">
                        @csrf
                        <button class="button button--secondary" type="submit">Manually activate</button>
                    </form>
                </div>
            </div>
        @endunless

        <div class="card card--no-padding">
            <div class="card__header">
                Details
            </div>
            <div class="card__body">
                <table class="table table--divided">
                    <tr>
                        <td>Email</td>
                        <td>
                            {{ $account->email }}
                        </td>
                    </tr>
                    <tr>
                        <td>Username</td>
                        <td>
                            @isset($account->username)
                                {{ $account->username }}
                            @else
                                Unset
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="card card--no-padding">
            <div class="card__header">
                Account Dates
            </div>
            <div class="card__body">
                <table class="table table--divided">
                    <tr>
                        <td>Last login at</td>
                        <td>
                            {{ $account->last_login_at }} ({{ $account->last_login_ip }} <a
                                href="https://www.ip-tracker.org/locator/ip-lookup.php?ip={{ $account->last_login_ip }}"
                                target="_blank">
                                <i class="fas fa-map-marked"></i> Locate last login </a>)
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>Created at</td>
                        <td>
                            {{ $account->created_at }}
                        </td>
                    </tr>
                    <tr>
                        <td>Updated at</td>
                        <td>
                            {{ $account->updated_at }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="card card--no-padding">
            <div class="card__header">
                Groups
            </div>
            <div class="card__body">
                <ul>
                    @foreach($account->groups as $group)
                        <li>{{ $group->name }}
                            <button class="button button--accent button--bordered"><i class="fas fa-trash-alt"></i>
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="card__footer">
                <form action="#" method="post">
                    <div class="form-row">
                        <label for="group_id">Add new group</label>
                        <select name="group_id" id="group_id">
                            <option value="" disabled selected>Select group</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                        <button class="button button--primary ">Add group</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card card--no-padding">
            <div class="card__header">
                Donations
            </div>
            <div class="card__body">
                <table class="table table--divided">
                    <thead>
                    <tr>
                        <th>Active</th>
                        <th>Amount</th>
                        <th>Perks end at</th>
                        <th>Lifetime?</th>
                        <th>Donation time</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($account->donations as $donation)
                        <tr>
                            <td>{{ $donation->is_active }}</td>
                            <td>${{ $donation->amount/100 }}</td>
                            <td>{{ $donation->perks_end_at }}</td>
                            <td>{{ $donation->is_lifetime_perks }}</td>
                            <td>{{ $donation->donation_time }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card card--no-padding">
            <div class="card__header">
                Minecraft Accounts
            </div>
            <div class="card__body">
                <table class="table table--divided">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Last Alias</th>
                        <th>UUID</th>
                        <th>Playtime</th>
                        <th>Last Seen</th>
                        <th>First Seen</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($account->minecraftAccount as $account)
                        <tr>
                            <td><img src="https://minotar.net/avatar/{{ $account->uuid }}/16" alt=""></td>
                            <td>{{ $account->getBanReadableName() }}</td>
                            <td>{{ $account->uuid }}</td>
                            <td>{{ $account->playtime }}</td>
                            <td>{{ $account->last_seen_at }}</td>
                            <td>{{ $account->created_at }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

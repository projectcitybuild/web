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
                          action="{{ route('front.panel.accounts.resend-activation', $account) }}"
                          method="post">
                        @csrf
                        <button class="button button--primary" type="submit">Resend activation</button>
                    </form>
                    <form class="inline" action="{{ route('front.panel.accounts.activate', $account) }}"
                          method="post">
                        @csrf
                        <button class="button button--secondary" type="submit">Manually activate</button>
                    </form>
                </div>
            </div>
        @endunless

        <div class="card card--no-padding">
            <div class="card__header">
                Details <a href="{{ route('front.panel.accounts.edit', $account) }}" class="spaced-icon-link"><i
                        class="fas fa-pencil-alt"></i>Edit</a>
            </div>
            <div class="card__body">
                <table class="table table--divided">
                    <tr>
                        <td>Email</td>
                        <td>
                            {{ $account->email }}

                            @forelse($account->emailChangeRequests as $request)
                                <div>
                                    <strong>Pending Changes:</strong>
                                    <ul>
                                        <li>
                                            @if($request->is_previous_confirmed)
                                                <i class="fas fa-check"></i>
                                            @else
                                                <i class="fas fa-times"></i>
                                                @endif
                                                {{ $request->email_previous }} &rarr;

                                                @if($request->is_new_confirmed)
                                                    <i class="fas fa-check"></i>
                                                @else
                                                    <i class="fas fa-times"></i>
                                                @endif
                                                {{ $request->email_new }}

                                                <form
                                                    class="inline"
                                                    method="post"
                                                    action="{{ route('front.panel.accounts.email-change.approve', [$account, $request]) }}">
                                                    @csrf
                                                    <button>Approve</button>
                                                </form>
                                        </li>
                                    </ul>
                                </div>
                            @empty
                                <em>No pending change requests</em>
                            @endforelse
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

        <div class="card">
            <div class="card__body">
                <form action="{{ route('front.panel.accounts.force-discourse-sync', $account) }}" method="post">
                    @csrf
                    <button type="submit" class="button button--primary">SSO Sync</button>
                </form>
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
                            {{ $account->last_login_at }}<br>(from {{ $account->last_login_ip }} <a
                                href="https://www.ip-tracker.org/locator/ip-lookup.php?ip={{ $account->last_login_ip }}"
                                class="spaced-icon-link"
                                target="_blank">
                                <i class="fas fa-map-marked"></i>Locate</a>)
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
        <div class="card">
            <div class="card__header">
                Groups
            </div>
            <div class="card__body">
                <form action="{{ route('front.panel.accounts.update-groups', $account) }}" method="post">
                    @csrf
                    <div class="checkbox-list">
                        @foreach($groups as $group)
                            <div>
                                <label><input type="checkbox" name="groups[]" value="{{ $group->group_id }}"
                                              @if($account->inGroup($group)) checked @endif
                                    > {{ $group->name }}</label>
                            </div>
                        @endforeach
                    </div>


                    <button type="submit" class="button button--primary">Save</button>
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
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($account->minecraftAccount as $player)
                        <tr>
                            <td><img src="https://minotar.net/avatar/{{ $player->uuid }}/16" alt=""></td>
                            <td>
                                @if($player->aliases()->count() == 0)
                                    <em>No alias</em>
                                @else
                                    {{ $player->aliases->last()->alias }}
                                @endempty
                            </td>
                            <td>{{ $player->uuid }}</td>
                            <td>{{ $player->playtime }}</td>
                            <td>{{ $player->last_seen_at }}</td>
                            <td>{{ $player->created_at }}</td>
                            <td>
                                <form
                                    action="{{ route('front.panel.accounts.game-account.delete', [$account, $player]) }}"
                                    method="post">
                                    @csrf
                                    @method('DELETE')
                                    <button>Unlink</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card card--no-padding">
            <div class="card__header">
                Bans
            </div>
            <div class="card__body">
                <table class="table table--divided">
                    <thead>
                    <tr>
                        <th>Reason</th>
                        <th>Banned By</th>
                        <th>Active</th>
                        <th>Expires at</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($account->gameBans() as $ban)
                        <tr>
                            <td>{{ $ban->reason }}</td>
                            <td>{{ $ban->getStaffName() }}</td>
                            <td>{{ $ban->is_active }}</td>
                            <td>{{ $ban->expires_at }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

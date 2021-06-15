@extends('admin.layouts.admin')

@section('title')
    {{ $account->username ?? $account->email }}'s Account
@endsection

@section('toolbar')
    <form action="{{ route('front.panel.accounts.force-discourse-sync', $account) }}" method="post" class="d-inline">
        @csrf
        <div class="btn-group btn-group-sm" role="group">
            <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="fas fa-sync"></i> SSO Sync</button>
            <a href="{{ route('front.panel.accounts.discourse-admin-redirect', $account) }}" class="btn btn-outline-secondary"><i class="fas fa-external-link-square-alt"></i> Forum Admin</a>
        </div>
    </form>
@endsection

@section('body')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-default">
                <div class="card-header">
                    Details
                </div>
                <dl class="kv">
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Email
                            @if($account->activated)
                                <span class="text-success" data-bs-toggle="tooltip" data-bs-placement="right" title="Email confirmed"><i class="fas fa-check-circle"></i></span>
                            @else
                                <span class="text-danger" data-bs-toggle="tooltip" data-bs-placement="right" title="Not confirmed"><i class="fas fa-times-circle"></i></span>
                            @endif
                        </dt>
                        <dd class="col-md-9">
                            <div>
                                {{ $account->email }}
                            </div>
                            @unless($account->activated)
                                <div class="mt-3">
                                    <form class="d-inline"
                                          action="{{ route('front.panel.accounts.resend-activation', $account) }}"
                                          method="post">
                                        @csrf
                                        <button class="btn btn-outline-secondary btn-sm" type="submit"><i class="fas fa-envelope"></i> Resend activation</button>
                                    </form>
                                    <form class="d-inline" action="{{ route('front.panel.accounts.activate', $account) }}"
                                          method="post">
                                        @csrf
                                        <button class="btn btn-outline-secondary btn-sm" type="submit"><i class="fas fa-check-double"></i> Manually activate</button>
                                    </form>
                                </div>
                            @endunless
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Username
                        </dt>
                        <dd class="col-md-9">
                            @isset($account->username)
                                {{ $account->username }}
                            @else
                                <span class="badge bg-secondary">Unset</span>
                            @endif
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Created At
                        </dt>
                        <dd class="col-md-9">
                            {{ $account->created_at }}
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Updated At
                        </dt>
                        <dd class="col-md-9">
                            {{ $account->updated_at }}
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Last Sign-In
                        </dt>
                        <dd class="col-md-9">
                            <div>{{ $account->last_login_at }}</div>
                            <div class="text-muted">
                                from {{ $account->last_login_ip }} <a href="https://www.ip-tracker.org/locator/ip-lookup.php?ip={{ $account->last_login_ip }}" class="text-muted" target="_blank"><i class="fas fa-map-marked"></i> Locate</a>
                            </div>
                        </dd>
                    </div>
                </dl>
            </div>
            <div class="card card-default mt-2">
                <div class="card-header">
                    Email Changes
                </div>
                <ul class="list-group list-group-flush">

                    @forelse($account->emailChangeRequests as $request)
                        <li class="list-group-item d-flex justify-content-between">
                            <div>
                                @if($request->is_previous_confirmed)
                                    <span class="text-success" data-bs-toggle="tooltip" title="Confirmed"><i class="fas fa-check" ></i> {{ $request->email_previous }}</span>
                                @else
                                    <span class="text-danger" data-bs-toggle="tooltip" title="Unconfirmed"><i class="fas fa-times"></i> {{ $request->email_previous }}</span>
                                @endif

                                <span class="text-muted mx-2">to</span>

                                @if($request->is_new_confirmed)
                                    <span class="text-success" data-bs-toggle="tooltip" title="Confirmed"><i class="fas fa-check"></i> {{ $request->email_new }}</span>
                                @else
                                    <span class="text-danger" data-bs-toggle="tooltip" title="Unconfirmed"><i class="fas fa-times"></i> {{ $request->email_new }}</span>
                                @endif
                            </div>
                            <div>
                                <form
                                    class="inline"
                                    method="post"
                                    action="{{ route('front.panel.accounts.email-change.approve', [$account, $request]) }}">
                                    @csrf
                                    <button class="btn btn-link btn-sm p-0">Approve</button>
                                </form>
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item">
                            <span class="text-success"><i class="fa fa-check"></i> No pending change requests</span>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <form action="{{ route('front.panel.accounts.update-groups', $account) }}" method="post">
                @csrf
                <div class="card card-default">
                    <div class="card-header d-flex justify-content-between">
                        <span>Groups</span>
                        <button type="submit" class="btn btn-outline-primary btn-sm py-0">
                            <i class="fas fa-save"></i> Update
                        </button>
                    </div>
                    <ul class="list-group list-group-flush">
                        @foreach($groups as $group)
                        <li class="list-group-item"><label><input type="checkbox" class="me-2" name="groups[]" value="{{ $group->group_id }}" @if($account->inGroup($group)) checked @endif> {{ $group->name }}</label></li>
                        @endforeach
                    </ul>
                </div>
            </form>

        </div>
    </div>
    <div class="row row-cols-1 row-cols-md-2 g-4 mt-3">
        <div class="col">
            <div class="card h-100">
                <div class="card-header">Donation Perks</div>
                <table class="table mb-0">
                    <thead>
                    <tr>
                        <th>Active</th>
                        <th>Donation ID</th>
                        <th>Starts at</th>
                        <th>Ends at</th>
                        <th>Lifetime?</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($account->donationPerks as $perk)
                        <tr>
                            <td>{{ $perk->is_active ? 'Yes' : 'No' }}</td>
                            <td><a href="{{ route('front.panel.donations.edit', $perk->donation_id) }}">{{ $perk->donation_id }}</a></td>
                            <td>{{ $perk->created_at }}</td>
                            <td>{{ $perk->expires_at }}</td>
                            <td>{{ $perk->is_lifetime_perks ? 'Yes' : 'No' }}</td>
                            <td><a href="{{ route('front.panel.donation-perks.edit', $perk) }}">Edit</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-muted text-center">
                                No donations yet.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col">
            <div class="card h-100">
                todo: donations, maybe swap this row
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col">
            <div class="card card-default">
                <div class="card-header">Minecraft Accounts</div>
                <table class="table mb-0">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Last Alias</th>
                        <th>UUID</th>
                        <th>Last Synced</th>
                        <th>First Seen</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($account->minecraftAccount as $player)
                        <tr>
                            <td><img src="https://minotar.net/avatar/{{ $player->uuid }}/16" alt=""></td>
                            <td>
                                @if($player->aliases()->count() == 0)
                                    <span class="text-muted">Unknown</span>
                                @else
                                    {{ $player->aliases->last()->alias }}
                                @endempty
                            </td>
                            <td>{{ $player->uuid }}</td>
                            <td>
                                @isset($mcAccount->last_synced_at)
                                    {{ $mcAccount->last_synced_at->toFormattedDateString() }}
                                @else
                                    <span class="text-muted">Never</span>
                                @endisset
                            </td>
                            <td>{{ $player->created_at }}</td>
                            <td>
                                <form
                                    action="{{ route('front.panel.accounts.game-account.delete', [$account, $player]) }}"
                                    method="post">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-link btn-sm text-danger p-0">Unlink</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-muted text-center">No linked accounts</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col">
            <div class="card card-default">
                <div class="card-header">Bans</div>
                <table class="table mb-0">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Reason</th>
                        <th>Banned By</th>
                        <th>Banned At</th>
                        <th>Expires At</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($account->gameBans() as $ban)
                        <tr class="{{ $ban->is_active ? 'table-danger' : '' }}">
                            <td data-bs-toggle="tooltip" data-bs-placement="left" title="{{ $ban->is_active ? 'Active' : 'Expired' }}">
                                <i class="fas fa-{{ $ban->is_active ? 'check' : 'clock' }}"></i>
                            </td>
                            <td>{{ $ban->reason }}</td>
                            <td>{{ $ban->getStaffName() }}</td>
                            <td>{{ $ban->created_at }}</td>
                            <td>{{ $ban->expires_at }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

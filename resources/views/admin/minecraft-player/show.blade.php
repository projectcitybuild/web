@extends('admin.layouts.admin')

@section('title')
    Minecraft Player {{ $minecraftPlayer->alias ?? $minecraftPlayer->uuid }}
@endsection

@section('toolbar')
    <form action="{{ route('front.panel.minecraft-players.reload-alias', $minecraftPlayer) }}" method="post">
        @csrf
        <div class="btn-group btn-group-sm" role="group">
            <a href="https://analytics.pcbmc.co/player/{{ $minecraftPlayer->dashedUuid }}" class="btn btn-outline-secondary"><i class="fas fa-external-link-square-alt"></i> Plan</a>
            <button type="submit" class="btn btn-outline-secondary"><i class="fas fa-sync"></i> Reload Name</button>
        </div>
    </form>
@endsection

@section('body')
    <div class="row">
        <div class="col-md-3 col">
            <img src="https://minotar.net/armor/body/{{ $minecraftPlayer->uuid }}" alt="Player Head" class="img-fluid d-none d-md-block mx-auto">
            <img src="https://minotar.net/helm/{{ $minecraftPlayer->uuid }}" alt="Player Head" class="img-fluid d-block d-md-none mx-auto mb-2">
        </div>
        <div class="col-md-9">
            <div class="card h-100 card-default">
                <div class="card-header d-flex justify-content-between">
                    <span>Details</span>
                    <a href="{{ route('front.panel.minecraft-players.edit', $minecraftPlayer) }}" class="btn btn-outline-primary btn-sm py-0">
                        <i class="fas fa-pencil-alt"></i> Edit
                    </a>
                </div>
                <dl class="kv">
                    <div class="row g-0">
                        <dt class="col-md-3">
                            UUID
                        </dt>
                        <dd class="col-md-9 font-monospace">
                            {{ $minecraftPlayer->uuid }}
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Last Alias
                        </dt>
                        <dd class="col-md-9">
                            {{ $minecraftPlayer->alias ?? '-' }}
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Owner
                        </dt>
                        <dd class="col-md-9 d-md-flex">
                            @if($minecraftPlayer->account)
                                <a href="{{ route('front.panel.accounts.show', $minecraftPlayer->account) }}">
                                    {{ $minecraftPlayer->account->username ?: '(Unset)' }}
                                </a>
                            @else
                                <span class="text-muted">Unassigned</span>
                            @endif
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Last Synced At
                        </dt>
                        <dd class="col-md-9">
                            {{ $minecraftPlayer->last_synced_at }}
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            First Seen
                        </dt>
                        <dd class="col-md-9">
                            {{ $minecraftPlayer->created_at }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col">
            <div class="card">
                <div class="card-header">Bans</div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                        <tr>
                            <th>Status</th>
                            <th>Reason</th>
                            <th>Banned By</th>
                            <th>Expires At</th>
                            <th>Banned At</th>
                            <th>Unbanned At</th>
                            <th>Unban Type</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($minecraftPlayer->gamePlayerBans as $ban)
                            <tr class="{{ $ban->isActive() ? 'table-warning' : '' }}">
                                <td data-bs-toggle="tooltip" data-bs-placement="left" title="{{ $ban->isActive() ? 'Active' : 'Inactive' }}">
                                    {{ $ban->isActive() ? 'Active' : 'Inactive' }}
                                </td>
                                <td>{{ $ban->reason }}</td>
                                <td>
                                    @if($ban->bannerPlayer == null)
                                        <span class="badge bg-secondary">Null</span>
                                    @else
                                    <a href="{{ route('front.panel.minecraft-players.show', $ban->bannerPlayer) }}">
                                        {{ $ban->getBannerName() }}
                                    </a>
                                    @endif
                                </td>
                                <td>{{ $ban->expires_at }}</td>
                                <td>{{ $ban->created_at }}</td>
                                <td>{{ $ban->unbanned_at }}</td>
                                <td>{{ $ban->unban_type?->value }}</td>

                                <td class="actions">
{{--                                    <a href="#" class="text-danger">Unban</a>--}}
{{--                                    <a href="#">Edit</a>--}}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-muted text-center">No bans</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col">
            <div class="card">
                <div class="card-header">Warnings</div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                        <tr>
                            <th>Reason</th>
                            <th>Warned By</th>
                            <th>Created At</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($minecraftPlayer->warnings as $warning)
                            <tr class="table-warning">
                                <td>{{ $warning->reason }}</td>
                                <td>
                                    <a href="{{ route('front.panel.minecraft-players.show', $warning->warnerPlayer) }}">
                                        {{ $warning->warnerPlayer->alias ?? '(No Alias)' }}
                                    </a>
                                </td>
                                <td>{{ $warning->created_at }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-muted text-center">No warnings</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

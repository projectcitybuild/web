@extends('admin.layouts.admin')

@section('title')
    Minecraft Player {{ $minecraftPlayer->getBanReadableName() ?? $minecraftPlayer->uuid }}
@endsection

@section('toolbar')
        <div class="btn-group btn-group-sm" role="group">
            <a href="https://analytics.pcbmc.co/player/{{ $minecraftPlayer->dashedUuid }}" class="btn btn-outline-secondary"><i class="fas fa-external-link-square-alt"></i> Plan</a>
            <a href="#" class="btn btn-outline-secondary"><i class="fas fa-sync"></i> Reload Name</a>
        </div>
@endsection

@section('body')
    <div class="row">
        <div class="col-md-1 col-3">
            <img src="https://minotar.net/avatar/{{ $minecraftPlayer->uuid }}" alt="Player Head" class="img-fluid">
        </div>
        <div class="col-md-5">
            <div class="card card-default">
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
                            {{ $minecraftPlayer->getBanReadableName() ?? '-' }}
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
        <div class="col-md-5">
            <div class="card h-100 card-default">
                <div class="card-header d-flex justify-content-between">
                    <span>Aliases</span>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                        <tr>
                            <th>Alias</th>
                            <th>Created At</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($minecraftPlayer->aliases as $alias)
                            <tr>
                                <td>{{ $alias->alias }}</td>
                                <td>{{ $alias->created_at }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection

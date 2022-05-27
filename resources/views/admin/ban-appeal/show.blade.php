@extends('admin.layouts.admin')

@section('title', 'Ban Appeal: ' . $banAppeal->getBannedPlayerName())

@section('body')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-default">
                <div class="card-header">
                    Appeal Details
                </div>
                <dl class="kv">
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Banned Player
                        </dt>
                        <dd class="col-md-9">
                            <a href="{{ route('front.panel.minecraft-players.show', $banAppeal->gameBan->bannedPlayer) }}">
                                {{ $banAppeal->gameBan->bannedPlayer->getBanReadableName() ?? 'No Alias' }}</a>
                            @if ($banAppeal->gameBan->hasNameChangedSinceBan())
                                (was {{ $banAppeal->gameBan->banned_alias_at_time }})
                            @endif
                            <br>
                            @forelse($banAppeal->gameBan->bannedPlayer?->account->groups as $group)
                                <span class="badge bg-secondary">
                                    {{ $group->alias ?? Str::title($group->name) }}
                                </span>
                            @empty
                                <span class="badge bg-light text-dark">
                                    Guest
                                </span>
                            @endforelse
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Account
                        </dt>
                        <dd class="col-md-9">
                            @isset($banAppeal->gameBan->bannedPlayer->account)
                                <a href="{{ route('front.panel.accounts.show', $banAppeal->gameBan->bannedPlayer->account) }}">
                                    {{ $banAppeal->gameBan->bannedPlayer->account->username }}
                                </a>
                            @else
                                <span class="text-muted">Guest</span>
                            @endisset
                            @if($banAppeal->is_account_verified)
                                <div>
                                    <i class="fas fa-check text-success"></i>
                                    Account used to appeal
                                </div>
                            @else
                                <div class="text-danger">
                                    <i class="fas fa-exclamation-circle"></i>
                                    Appeal not made by this account
                                </div>
                            @endif
                        </dd>
                    </div>
                    @isset($banAppeal->email)
                        <div class="row g-0">
                            <dt class="col-md-3">
                                Email
                            </dt>
                            <dd class="col-md-9">
                                {{ $banAppeal->email }}
                            </dd>
                        </div>
                    @endisset
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Banned By
                        </dt>
                        <dd class="col-md-9">
                            <a href="{{ route('front.panel.minecraft-players.show', $banAppeal->gameBan->staffPlayer) }}">
                                {{ $banAppeal->gameBan->staffPlayer->getBanReadableName() ?? 'No Alias' }}
                            </a>
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Ban Expiry
                        </dt>
                        <dd class="col-md-9">
                            {{ $banAppeal->gameBan->expires_at?->format('j M Y H:i') ?? 'Never' }}
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Appeal Created
                        </dt>
                        <dd class="col-md-9">
                            {{ $banAppeal->gameBan->created_at }}
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Appeal Reason
                        </dt>
                        <dd class="col-md-9">
                            <em>{{ $banAppeal->explanation }}</em>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
        <div class="col-md-6">
            @include('admin.ban-appeal.status._' . $banAppeal->status->slug())
        </div>
    </div>
    <div class="row">
    </div>
@endsection

@extends('admin.layouts.admin')

@section('title', 'Ban Appeal: ' . $banAppeal->getBannedPlayerName())

@section('body')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Appeal Details
                </div>
                <dl class="kv">
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Banned Player
                        </dt>
                        <dd class="col-md-9">
                            <a href="{{ route('front.panel.minecraft-players.show', $banAppeal->gamePlayerBan->bannedPlayer) }}">
                                {{ $banAppeal->gamePlayerBan->bannedPlayer->alias ?? 'No Alias' }}</a>
                            @if ($banAppeal->gamePlayerBan->hasNameChangedSinceBan())
                                (was {{ $banAppeal->gamePlayerBan->banned_alias_at_time }})
                            @endif
                            <br>
                            @forelse($banAppeal->gamePlayerBan->bannedPlayer->account?->groups ?? [] as $group)
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
                            @isset($banAppeal->gamePlayerBan->bannedPlayer->account)
                                <a href="{{ route('front.panel.accounts.show', $banAppeal->gamePlayerBan->bannedPlayer->account) }}">
                                    {{ $banAppeal->gamePlayerBan->bannedPlayer->account->username }}
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
                            @if ($banAppeal->gamePlayerBan->bannerPlayer === null)
                                System
                            @else
                                <a href="{{ route('front.panel.minecraft-players.show', $banAppeal->gamePlayerBan->bannerPlayer) }}">
                                    {{ $banAppeal->gamePlayerBan->bannerPlayer->alias ?? 'No Alias' }}
                                </a>
                            @endif
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Ban Expiry
                        </dt>
                        <dd class="col-md-9">
                            {{ $banAppeal->gamePlayerBan->expires_at?->format('j M Y H:i') ?? 'Never' }}
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Appeal Created
                        </dt>
                        <dd class="col-md-9">
                            {{ $banAppeal->gamePlayerBan->created_at }}
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

            @if($banAppeal->status == \App\Domains\BanAppeals\Entities\BanAppealStatus::PENDING)
                <div class="card mt-2">
                    <div class="card-header">
                        Decide Appeal
                    </div>
                    <div class="card-body border-bottom">
                        <i class="fas fa-exclamation-triangle text-danger"></i> The player <strong>will be notified of
                            this decision immediately</strong>.
                    </div>
                    <div class="card-body">
                        <form action="{{ route('front.panel.ban-appeals.update', $banAppeal) }}" method="post">
                            @csrf
                            @include('admin._errors')
                            @method('PUT')
                            <div class="mb-3">
                                <div class="mb-3">
                                    <label for="decision_note" class="form-label">Decision Message</label>
                                    <textarea
                                            class="form-control"
                                            name="decision_note"
                                            id="decision_note"
                                            rows="5"
                                    >{{ old('deny_reason', $banAppeal->decision_note) }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="mb-1">Appeal Decision</label>
                                    @foreach(\App\Domains\BanAppeals\Entities\BanAppealStatus::decisionCases() as $status)
                                        <div class="form-check ">
                                            <input
                                                    class="form-check-input"
                                                    type="radio" name="status"
                                                    name="status"
                                                    value="{{ $status->value }}"
                                                    id="status{{ $status->value }}"
                                                    @checked(old('status', $banAppeal->status) == $status)>
                                            <label class="form-check-label" for="status{{ $status->value }}">
                                                {{ $status->humanReadableAction() }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <div>
                                    <button class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@extends('front.layouts.root-layout')

@php
    use App\Domains\BanAppeals\Data\BanAppealStatus;
@endphp

@section('title', 'Your Ban Appeal')
@section('heading', 'Ban Appeal')
@section('meta_description', 'Check the status of your ban appeal')

@section('body')
    <x-front::form-page
        background="resources/images/bg_survival.webp"
        eyebrow="BAN APPEALS"
        title="Your Ban Appeal"
        description="Track your appeal here. Updates will also be sent to you by email."
    >
        @if ($banAppeal->status->isDecided())
            @switch($banAppeal->status)
                @case(BanAppealStatus::ACCEPTED_UNBAN)
                    <x-front::form-panel title="Unbanned" variant="success">
                        <p class="mt-2 text-gray-300">
                            Your ban appeal has been accepted. Please read the staff response below for any important information.
                        </p>
                    </x-front::form-panel>
                    @break
                @case(BanAppealStatus::ACCEPTED_TEMPBAN)
                    <x-front::form-panel title="Ban reduced" variant="success">
                        <p class="mt-2 text-gray-300">
                            Your appeal has been considered, and your ban has been reduced to a temporary ban.
                        </p>
                        <p class="mt-2 font-semibold text-emerald-200">You will be unbanned on {{ $banAppeal->gamePlayerBan->expires_at }}</p>
                    </x-front::form-panel>
                    @break
                @case(BanAppealStatus::DENIED)
                    <x-front::form-panel title="Appeal denied" variant="danger">
                        <p class="mt-2 text-gray-300">Your appeal was denied. Please read the staff response below carefully before making another appeal.</p>
                    </x-front::form-panel>
                    @break
            @endswitch
        @else
            <x-front::form-panel title="In review" variant="pending">
                <p class="mt-2 text-gray-300">Staff are currently reviewing your case. This usually happens within 48 hours, but may take longer in some cases.</p>
            </x-front::form-panel>
        @endif

        @if ($banAppeal->status->isDecided())
            <x-front::form-panel title="Staff response">
                <div class="mt-3 whitespace-pre-wrap leading-7 text-gray-200">
                    @if (empty($banAppeal->decision_note))
                        No decision message provided.
                    @else
                        {{ $banAppeal->decision_note }}
                    @endif
                </div>
                <p class="mt-3 text-sm text-gray-400">{{ $banAppeal->decided_at->format('M jS, Y - h:iA') }}</p>
            </x-front::form-panel>
        @endif

        <x-front::form-panel title="Your Appeal">
            <dl class="form-detail-list mt-3">
                <div class="form-detail-list__row">
                    <dt class="form-detail-list__label">Why you should be unbanned</dt>
                    <dd class="form-detail-list__value">{{ $banAppeal->unban_reason }}</dd>
                </div>
            </dl>

            <h2 class="mt-6 border-t border-white/10 pt-6 text-lg font-semibold">Ban Details</h2>
            <dl class="form-detail-list mt-2">
                <div class="form-detail-list__row">
                    <dt class="form-detail-list__label">Date of Ban</dt>
                    <dd class="form-detail-list__value">{{ $banAppeal->date_of_ban }}</dd>
                </div>
                <div class="form-detail-list__row">
                    <dt class="form-detail-list__label">Minecraft UUID</dt>
                    <dd class="form-detail-list__value break-all">{{ $banAppeal->minecraft_uuid }}</dd>
                </div>
                <div class="form-detail-list__row">
                    <dt class="form-detail-list__label">Reason for Ban</dt>
                    <dd class="form-detail-list__value">{{ $banAppeal->ban_reason }}</dd>
                </div>
            </dl>

            @isset ($banAppeal->gamePlayerBan)
                <div class="mt-6 border-t border-white/10 pt-6">
                    <h2 class="text-lg font-semibold">Selected Ban</h2>
                    <div class="form-record mt-4">
                        <img src="https://minotar.net/helm/{{ $banAppeal->gamePlayerBan->bannedPlayer->uuid }}/32" alt="" class="form-record__avatar">
                        <div class="form-record__content">
                            <strong class="form-record__title">{{ $banAppeal->gamePlayerBan->bannedPlayer->alias }}</strong>
                            <span class="form-record__meta">UUID: {{ $banAppeal->gamePlayerBan->bannedPlayer->uuid }}</span>
                            <span class="form-record__meta">{{ $banAppeal->gamePlayerBan->created_at->format('jS \of M, Y - h:i A') }}</span>
                        </div>
                        <a class="form-button form-button--secondary" href="{{ route('front.bans.details', $banAppeal->gamePlayerBan) }}">View ban</a>
                    </div>
                </div>
            @endisset
        </x-front::form-panel>
    </x-front::form-page>
@endsection

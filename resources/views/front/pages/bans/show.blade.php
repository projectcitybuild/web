@extends('front.layouts.root-layout')

@section('title', 'Player Ban Details')
@section('meta_description', 'Review the details of a player ban')

@section('body')
    <x-front::form-page
        eyebrow="COMMUNITY SAFETY"
        title="Ban Details"
        description="Review the available information about this ban."
    >
        <div class="flex flex-wrap items-center justify-between gap-4">
            <a href="{{ route('front.banlist') }}" class="inline-flex items-center gap-2 font-semibold text-form-accent-soft hover:text-white">
                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Back to ban list
            </a>

            @if ($ban->isActive())
                <a class="form-button" href="{{ route('front.appeal.form.prefilled', $ban) }}">
                    Appeal this ban
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                        <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                    </svg>
                </a>
            @endif
        </div>

        @if ($ban->unbanned_at === null)
            <x-front::form-panel title="Ban is active" variant="warning">
                <p class="mt-2 text-gray-300">This ban is currently active on the network.</p>
            </x-front::form-panel>
        @else
            <x-front::form-panel title="Ban is no longer active" variant="success">
                <p class="mt-2 text-gray-300">This ban has been lifted or has expired.</p>
            </x-front::form-panel>
        @endif

        <x-front::form-panel title="Ban information">
            <dl class="form-detail-list mt-3">
                <div class="form-detail-list__row">
                    <dt class="form-detail-list__label">Player Name</dt>
                    <dd class="form-detail-list__value flex items-center gap-3">
                        <img src="https://minotar.net/helm/{{ $ban->bannedPlayer->uuid }}/16" alt="" class="size-6 rounded-md">
                        {{ $ban->banned_alias_at_time }}
                    </dd>
                </div>
                <div class="form-detail-list__row">
                    <dt class="form-detail-list__label">Banned UUID</dt>
                    <dd class="form-detail-list__value break-all">{{ $ban->bannedPlayer->uuid }}</dd>
                </div>
                <div class="form-detail-list__row">
                    <dt class="form-detail-list__label">Reason</dt>
                    <dd class="form-detail-list__value">{{ $ban->reason ?? '-' }}</dd>
                </div>
                <div class="form-detail-list__row">
                    <dt class="form-detail-list__label">Banned At</dt>
                    <dd class="form-detail-list__value">{{ $ban->created_at->format('j M Y H:i') }}</dd>
                </div>
                <div class="form-detail-list__row">
                    <dt class="form-detail-list__label">Expires At</dt>
                    <dd class="form-detail-list__value">{{ $ban->expires_at?->format('j M Y H:i') ?? 'Never (permanent ban)' }}</dd>
                </div>
                <div class="form-detail-list__row">
                    <dt class="form-detail-list__label">Banned By</dt>
                    <dd class="form-detail-list__value">{{ $ban->bannerPlayer?->alias ?? 'No name' }}</dd>
                </div>
            </dl>
        </x-front::form-panel>
    </x-front::form-page>
@endsection

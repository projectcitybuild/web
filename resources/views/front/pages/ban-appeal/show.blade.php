@extends('front.layouts.root-layout')

@php
    use App\Domains\BanAppeals\Data\BanAppealStatus;
@endphp

@section('title', 'Your Ban Appeal')
@section('heading', 'Ban Appeal')
@section('description', 'Check the status of your ban appeal')

@section('body')
    <x-navbar/>

    <div class="max-w-screen-xl m-auto px-3 md:px-9 mb-12">
        <h1 class="mt-12 text-4xl font-bold">Your Ban Appeal</h1>

        <div class="mt-3 text-gray-500">
            Viewing the status of your ban appeal. You will be emailed with any updates.
        </div>

        @if ($banAppeal->status->isDecided())
            @switch($banAppeal->status)
                @case(BanAppealStatus::ACCEPTED_UNBAN)
                    <div class="rounded-lg bg-green-100 p-6 mt-6">
                        <h3 class="text-xl font-bold mb-3">Unbanned</h3>
                        Your ban appeal has been accepted.<br />
                        <strong>Please read the below response from staff, as it may contain important information to prevent you being banned in future.</strong>
                    </div>
                    @break
                @case(BanAppealStatus::ACCEPTED_TEMPBAN)
                    <div class="rounded-lg bg-green-100 p-6 mt-6">
                        <h3 class="text-xl font-bold mb-3">Ban Reduced</h3>
                        Your appeal has been considered, and your ban has been reduced to a temporary ban.<br />
                        <strong>You will be unbanned on {{ $banAppeal->gamePlayerBan->expires_at }}</strong>
                    </div>
                    @break
                @case(BanAppealStatus::DENIED)
                    <div class="rounded-lg bg-red-200 p-6 mt-6">
                        <h3 class="text-xl font-bold mb-3">Appeal Denied</h3>
                        Sorry, your appeal was denied. The response from staff is shown below.<br>
                        Please read and consider it carefully before making another appeal.
                    </div>
            @endswitch
        @else
            <div class="rounded-lg bg-gray-200 p-6 mt-6">
                <div class="flex flex-row gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <h3 class="text-xl font-bold mb-3">In Review</h3>
                </div>
                Staff are currently reviewing your case. <br />
                This usually happens within 48 hours, but may take longer in some cases.
            </div>
        @endif

        @if($banAppeal->status->isDecided())
            <div class="rounded-lg bg-gray-50 p-6 mt-2 border border-red-200">
                <div class="flex flex-row gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                    </svg>
                    <h3 class="text-xl font-bold mb-3">Response</h3>
                </div>

                <div class="message-text">
                    @empty($banAppeal->decision_note)
                        No decision message provided.
                    @else
                        {{ $banAppeal->decision_note }}
                    @endif
                </div>
                <div class="text-gray-500 mt-3 text-sm">
                    {{ $banAppeal->decided_at->format('M jS, Y - h:iA') }}
                </div>
            </div>
        @endif

        <div class="rounded-lg max-w-screen-xl m-auto mt-6 bg-gray-50 p-6">
            <h2 class="text-2xl font-bold mb-6">Your Appeal</h2>

            <div class="mt-6">
                <div class="flex flex-row gap-2 items-center text-gray-500 text-sm mb-1">
                    Why you should be unbanned
                </div>
                <span class="text-lg">{{ $banAppeal->unban_reason }}</span>
            </div>

            <hr class="my-8"/>

            <h2 class="text-2xl font-bold mb-6">Ban Details</h2>

            <div class="mt-6">
                <div class="flex flex-row gap-2 items-center text-gray-500 text-sm mb-1">
                    Date of Ban
                </div>
                <span class="text-lg">{{ $banAppeal->date_of_ban }}</span>
            </div>

            <div class="mt-6">
                <div class="flex flex-row gap-2 items-center text-gray-500 text-sm mb-1">
                    Minecraft UUID
                </div>
                <span class="text-lg">{{ $banAppeal->minecraft_uuid }}</span>
            </div>

            <div class="mt-6">
                <div class="flex flex-row gap-2 items-center text-gray-500 text-sm mb-1">
                    Reason for Ban
                </div>
                <span class="text-lg">{{ $banAppeal->ban_reason }}</span>
            </div>

            @isset ($banAppeal->gamePlayerBan)
                <div class="mt-6">
                    <div class="flex flex-row gap-2 items-center text-gray-500 text-sm mb-1">
                        Selected Ban
                    </div>
                    <div class="rounded-lg border border-gray-200 mt-3 bg-gray-50 py-3 px-4 flex flex-row items-center gap-6 overflow-x-auto mb-12">
                        <img src="https://minotar.net/avatar/{{ $banAppeal->gamePlayerBan->bannedPlayer->uuid }}/32" class="rounded-md w-12 h-12">

                        <div class="mt-1 flex flex-col gap-1 flex-grow">
                            <strong class="text-2xl">{{ $banAppeal->gamePlayerBan->bannedPlayer->alias }}</strong>
                            <span class="text-gray-500 text-xs">UUID: {{ $banAppeal->gamePlayerBan->bannedPlayer->uuid }}</span>
                            <span class="text-sm">{{ $banAppeal->gamePlayerBan->created_at->format('jS \of M, Y - h:i A') }}</span>
                        </div>

                        <x-button href="{{ route('front.bans.details', $banAppeal->gamePlayerBan) }}" variant="outlined">
                            View
                        </x-button>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

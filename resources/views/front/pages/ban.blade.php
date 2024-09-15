@extends('front.layouts.root-layout')

@section('title', 'Player Ban List')
@section('description', 'Players listed on this page are currently banned on one or more servers on our game network')

@section('body')
    <x-navbar />

    <div class="flex flex-col p-9">
        <a class="flex gap-2 text-sm" href="{{ route('front.banlist') }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Back to Ban List
        </a>

        <h1 class="text-4xl font-bold mt-6">Ban Details</h1>
    </div>

    <div class="bg-gray-50 mb-6 mx-2 md:mx-6 rounded-lg p-6">
        @if ($ban->unbanned_at === null)
            <div class="rounded-lg border border-red-200 bg-red-100 p-6">
                <h2 class="flex gap-2 font-bold">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                        <path fill-rule="evenodd" d="M12.963 2.286a.75.75 0 0 0-1.071-.136 9.742 9.742 0 0 0-3.539 6.176 7.547 7.547 0 0 1-1.705-1.715.75.75 0 0 0-1.152-.082A9 9 0 1 0 15.68 4.534a7.46 7.46 0 0 1-2.717-2.248ZM15.75 14.25a3.75 3.75 0 1 1-7.313-1.172c.628.465 1.35.81 2.133 1a5.99 5.99 0 0 1 1.925-3.546 3.75 3.75 0 0 1 3.255 3.718Z" clip-rule="evenodd" />
                    </svg>
                    Ban is Active
                </h2>
            </div>
        @endif

        <table class="mt-6 w-full overflow-x-auto">
            <tr>
                <td class="py-4 px-2 flex gap-2 text-gray-500">
                    Player Name
                </td>
                <td class="py-4 px-2">
                    <img src="https://minotar.net/avatar//{{ $ban->bannedPlayer->uuid }}/16" class="rounded-md h-6">
                    {{ $ban->banned_alias_at_time }}
                </td>
            </tr>
            <tr>
                <td class="py-4 px-2 flex gap-2 text-gray-500">
                    Banned UUID
                </td>
                <td class="py-4 px-2">
                    <em>{{ $ban->bannedPlayer->uuid }}</em>
                </td>
            </tr>
            <tr>
                <td class="py-4 px-2 flex gap-2 text-gray-500">
                    Reason
                </td>
                <td class="py-4 px-2">
                    {{ $ban->reason ?? "-" }}
                </td>
            </tr>
            <tr>
                <td class="py-4 px-2 flex gap-2 text-gray-500">
                    Banned At
                </td>
                <td class="py-4 px-2">
                    {{ $ban->created_at->format('j M Y H:i') }}
                </td>
            </tr>
            <tr>
                <td class="py-4 px-2 flex gap-2 text-gray-500">
                    Expires At
                </td>
                <td class="py-4 px-2">
                    {{ $ban->expires_at?->format('j M Y H:i') ?? "Never (permanent ban)" }}
                </td>
            </tr>
            <tr>
                <td class="py-4 px-2 flex gap-2 text-gray-500">
                    Banned By
                </td>
                <td class="py-4 px-2">
                    {{ $ban->bannerPlayer?->alias?->first() ?? "No name" }}
                </td>
            </tr>
        </table>
    </div>
@endsection

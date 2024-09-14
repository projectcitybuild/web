@extends('front.layouts.root-layout')

@section('title', 'Player Ban List')
@section('description', 'Players listed on this page are currently banned on one or more servers on our game network')

@section('body')
    <x-navbar />

    <div class="flex flex-col p-9">
        <h1 class="text-4xl font-bold">Banned Players</h1>
        <div class="text-gray-500 mt-3">
            List of players banned from our services.
        </div>
        <div class="text-gray-900 mt-3">
            If you have been unfairly or incorrectly banned, please <a href="{{ route('front.appeal') }}" class="text-blue-500">submit an appeal</a>.
        </div>
    </div>

    <div class="bg-gray-50 mx-2 md:mx-6 rounded-lg">
        <div class="p-6 border-b border-gray-200">
            <form action="{{ route('front.banlist') }}" method="get">
                <input
                    type="text"
                    class="rounded-md bg-gray-100 border-gray-200 text-sm"
                    name="query"
                    value="{{ $query }}"
                    placeholder="Search..."
                >
            </form>
        </div>

        <div class="p-6">
            <table class="w-full overflow-x-auto">
                <thead>
                    <tr class="text-left text-sm text-gray-500 bg-gray-100">
                        <th class="py-4 px-2">Player Name</th>
                        <th class="py-4 px-2">Reason</th>
                        <th class="py-4 px-2">Ban Date</th>
                        <th class="py-4 px-2">Expiry Date</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($bans as $ban)
                    <tr>
                        <td class="py-4 px-2 flex gap-2">
                            <img src="https://minotar.net/avatar//{{ $ban->bannedPlayer->uuid }}/16" class="rounded-md h-6">
                            <span class="font-bold">{{ $ban->banned_alias_at_time }}</span>
                        </td>
                        <td class="py-4 px-2">
                            @if ($ban->reason != null)
                                {{ $ban->reason }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="py-4 px-2">
                            {{ $ban->created_at->format('j M Y H:i') }}
                        </td>
                        <td class="py-4 px-2">
                            <x-button size="sm">
                                View Details
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4">
                                    <path fill-rule="evenodd" d="M6.22 4.22a.75.75 0 0 1 1.06 0l3.25 3.25a.75.75 0 0 1 0 1.06l-3.25 3.25a.75.75 0 0 1-1.06-1.06L8.94 8 6.22 5.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                </svg>
                            </x-button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">No bans match your search criteria</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $bans->links('vendor.pagination.default') }}
    </div>
@endsection

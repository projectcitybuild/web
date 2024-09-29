@extends('front.layouts.root-layout')

@section('title', 'Player Ban List')
@section('description', 'Players listed on this page are currently banned on one or more servers on our game network')

@section('body')
    <x-front::navbar />

    <div class="flex flex-col p-9">
        <h1 class="text-4xl font-bold">Banned Players</h1>
        <div class="text-gray-500 mt-3">
            List of players banned from our services.
        </div>
        <div class="text-gray-900 mt-3">
            If you feel you have been unfairly or incorrectly banned, please <a href="{{ route('front.appeal') }}" class="text-blue-500">submit an appeal</a>.
        </div>
    </div>

    <div class="bg-gray-50 mb-6 mx-2 md:mx-6 rounded-lg">
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
            @if (count($bans) === 0)
                <div class="px-6 py-6 flex flex-col items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-12">
                        <path fill-rule="evenodd" d="M12.963 2.286a.75.75 0 0 0-1.071-.136 9.742 9.742 0 0 0-3.539 6.176 7.547 7.547 0 0 1-1.705-1.715.75.75 0 0 0-1.152-.082A9 9 0 1 0 15.68 4.534a7.46 7.46 0 0 1-2.717-2.248ZM15.75 14.25a3.75 3.75 0 1 1-7.313-1.172c.628.465 1.35.81 2.133 1a5.99 5.99 0 0 1 1.925-3.546 3.75 3.75 0 0 1 3.255 3.718Z" clip-rule="evenodd" />
                    </svg>
                    <h2 class="text-xl font-bold">No Results</h2>
                    <span class="text-gray-500">Please try a different search criteria</span>
                </div>
            @else
                <table class="w-full overflow-x-auto">
                    <thead>
                        <tr class="text-left text-sm text-gray-500 bg-gray-100">
                            <th class="py-4 px-2">Player Name</th>
                            <th class="py-4 px-2">Reason</th>
                            <th class="py-4 px-2">Ban Date</th>
                            <th class="py-4 px-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($bans as $ban)
                        <tr>
                            <td class="py-4 px-2 flex gap-2">
                                <img src="https://minotar.net/avatar//{{ $ban->bannedPlayer->uuid }}/16" class="rounded-md h-6">
                                <span class="font-bold">{{ $ban->banned_alias_at_time }}</span>
                            </td>
                            <td class="py-4 px-2">
                                {{ $ban->reason ?? "-" }}
                            </td>
                            <td class="py-4 px-2">
                                {{ $ban->created_at->format('j M Y H:i') }}
                            </td>
                            <td class="py-4 px-2">
                                <x-button href="{{ route('front.bans.details', $ban) }}" size="sm">
                                    View Details
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4">
                                        <path fill-rule="evenodd" d="M6.22 4.22a.75.75 0 0 1 1.06 0l3.25 3.25a.75.75 0 0 1 0 1.06l-3.25 3.25a.75.75 0 0 1-1.06-1.06L8.94 8 6.22 5.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                    </svg>
                                </x-button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>


        <div class="p-6 border-t border-gray-200">
            {{ $bans->links('vendor.pagination.default') }}
        </div>
    </div>
@endsection

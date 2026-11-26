@extends('front.layouts.root-layout')

@section('title', 'Player Ban List')
@section('description', 'Players listed on this page are currently banned on one or more servers on our game network')

@section('body')
    <x-front::form-page
        eyebrow="COMMUNITY SAFETY"
        title="Banned Players"
    >
        <x-slot:intro>
            If you believe you have been banned unfairly, please <a href="{{ route('front.appeal') }}" class="font-semibold text-form-accent-soft underline underline-offset-4 hover:text-white">submit an appeal</a>.
            Players looking for a second chance may also plead their case.
        </x-slot:intro>

        <x-front::form-panel title="Active bans">
            <form action="{{ route('front.banlist') }}" method="get" class="form-search mt-5">
                <x-front::form-field for="query" label="Search by player name" class="form-search__field">
                    <x-front::form-input id="query" name="query" :value="$query" placeholder="Search bans..." />
                </x-front::form-field>
                <button class="form-button form-button--secondary" type="submit">Search</button>
            </form>

            @if (count($bans) === 0)
                <div class="form-empty mt-5 rounded-xl border border-white/10 bg-white/5">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-10 text-form-accent">
                        <path fill-rule="evenodd" d="M12.963 2.286a.75.75 0 0 0-1.071-.136 9.742 9.742 0 0 0-3.539 6.176 7.547 7.547 0 0 1-1.705-1.715.75.75 0 0 0-1.152-.082A9 9 0 1 0 15.68 4.534a7.46 7.46 0 0 1-2.717-2.248ZM15.75 14.25a3.75 3.75 0 1 1-7.313-1.172c.628.465 1.35.81 2.133 1a5.99 5.99 0 0 1 1.925-3.546 3.75 3.75 0 0 1 3.255 3.718Z" clip-rule="evenodd" />
                    </svg>
                    <h2 class="text-xl font-bold text-white">No results</h2>
                    <p>Try a different search or check back later.</p>
                </div>
            @else
                <div class="data-table-wrap mt-5">
                    <table class="data-table">
                        <thead class="data-table__head">
                            <tr>
                                <th class="data-table__cell">Player Name</th>
                                <th class="data-table__cell">Reason</th>
                                <th class="data-table__cell">Ban Date</th>
                                <th class="data-table__cell"><span class="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bans as $ban)
                                <tr class="data-table__row">
                                    <td class="data-table__cell">
                                        <span class="flex items-center gap-3 font-semibold text-white">
                                            <img src="https://minotar.net/helm/{{ $ban->bannedPlayer->uuid }}/16" alt="" class="size-6 rounded-md">
                                            {{ $ban->bannedPlayer?->alias ?? $ban->banned_alias_at_time }}
                                        </span>
                                    </td>
                                    <td class="data-table__cell text-gray-300">{{ $ban->reason ?? '-' }}</td>
                                    <td class="data-table__cell whitespace-nowrap text-gray-300">{{ $ban->created_at->format('j M Y H:i') }}</td>
                                    <td class="data-table__cell">
                                        <div class="data-table__actions">
                                            <a class="form-button form-button--secondary" href="{{ route('front.appeal.form.prefilled', $ban) }}">Appeal</a>
                                            <a class="form-button" href="{{ route('front.bans.details', $ban) }}">
                                                Details
                                                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4">
                                                    <path fill-rule="evenodd" d="M6.22 4.22a.75.75 0 0 1 1.06 0l3.25 3.25a.75.75 0 0 1 0 1.06l-3.25 3.25a.75.75 0 0 1-1.06-1.06L8.94 8 6.22 5.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="mt-5 border-t border-white/10 pt-5">
                {{ $bans->links('vendor.pagination.default') }}
            </div>
        </x-front::form-panel>
    </x-front::form-page>
@endsection

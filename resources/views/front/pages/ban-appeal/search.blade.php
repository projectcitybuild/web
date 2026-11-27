@extends('front.layouts.root-layout')

@section('title', 'Appeal Ban')
@section('heading', 'Appeal Ban')
@section('meta_description', 'Use the below form to submit a ban appeal')

@section('body')
    <x-front::form-page
        background="resources/images/bg_survival.webp"
        eyebrow="BAN APPEALS"
        title="Select a Ban"
        description="Find the ban you wish to appeal. If you cannot locate it, you can fill out the appeal form manually."
    >
        <a
            href="{{ route('front.appeal') }}"
            class="inline-flex items-center gap-2 font-semibold text-form-accent-soft hover:text-white"
        >
            <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Back to appeal options
        </a>

        @if ($errors->any())
            <x-front::form-panel title="Search could not be completed" variant="danger">
                <p class="mt-2 text-gray-300">{{ $errors->first() }}</p>
            </x-front::form-panel>
        @endif

        @auth
            <x-front::form-panel title="Your Active Bans">
                @if ($active_bans->count() > 0)
                    <div class="mt-5 space-y-3">
                        @foreach ($active_bans as $ban)
                            <div class="form-record">
                                <img src="https://minotar.net/helm/{{ $ban->bannedPlayer->uuid }}/32" alt="" class="form-record__avatar">
                                <div class="form-record__content">
                                    <strong class="form-record__title">{{ $ban->bannedPlayer->alias }}</strong>
                                    <span class="form-record__meta">UUID: {{ $ban->bannedPlayer->uuid }}</span>
                                    <span class="form-record__meta">{{ $ban->created_at->format('jS \of M, Y - h:i A') }}</span>
                                </div>
                                <div class="flex flex-wrap gap-2 sm:ml-auto">
                                    <a class="form-button form-button--secondary" href="{{ route('front.bans.details', $ban) }}">View details</a>
                                    <a class="form-button" href="{{ route('front.appeal.form.prefilled', $ban) }}">
                                        Select
                                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                            <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="mt-4 text-gray-300">No active bans were found for your linked Minecraft accounts.</p>
                @endif
            </x-front::form-panel>
        @endauth

        <x-front::form-panel title="All Bans">
            <form action="{{ route('front.appeal.search') }}" method="get" class="form-search mt-5">
                <x-front::form-field for="query" label="Search by player name" class="form-search__field">
                    <x-front::form-input id="query" name="query" :value="$query" placeholder="Search bans..." />
                </x-front::form-field>
                <input type="text" name="name" class="hidden" tabindex="-1" autocomplete="off" />
                <button class="form-button form-button--secondary" type="submit">Search</button>
            </form>

            @if ($bans->count() > 0)
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
                                            {{ $ban->banned_alias_at_time }}
                                        </span>
                                    </td>
                                    <td class="data-table__cell text-gray-300">{{ $ban->reason ?? '-' }}</td>
                                    <td class="data-table__cell whitespace-nowrap text-gray-300">{{ $ban->created_at->format('j M Y H:i') }}</td>
                                    <td class="data-table__cell">
                                        <div class="data-table__actions">
                                            <a class="form-button form-button--secondary" href="{{ route('front.bans.details', $ban) }}">Details</a>
                                            <a class="form-button" href="{{ route('front.appeal.form.prefilled', $ban) }}">Select</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-5 border-t border-white/10 pt-5">
                    {{ $bans->links('vendor.pagination.default') }}
                </div>
            @else
                <div class="form-empty mt-5 rounded-xl border border-white/10 bg-white/5">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-10 text-form-accent">
                        <path fill-rule="evenodd" d="M12.963 2.286a.75.75 0 0 0-1.071-.136 9.742 9.742 0 0 0-3.539 6.176 7.547 7.547 0 0 1-1.705-1.715.75.75 0 0 0-1.152-.082A9 9 0 1 0 15.68 4.534a7.46 7.46 0 0 1-2.717-2.248ZM15.75 14.25a3.75 3.75 0 1 1-7.313-1.172c.628.465 1.35.81 2.133 1a5.99 5.99 0 0 1 1.925-3.546 3.75 3.75 0 0 1 3.255 3.718Z" clip-rule="evenodd" />
                    </svg>
                    <h3 class="text-xl font-bold text-white">No results</h3>
                    <p>Please try a different search.</p>
                </div>
            @endif
        </x-front::form-panel>

        <p class="text-sm text-gray-300">
            Can't find your ban?
            <a href="{{ route('front.appeal.form') }}" class="font-semibold text-form-accent-soft underline underline-offset-4 hover:text-white">Fill out the form manually</a>.
        </p>
    </x-front::form-page>
@endsection

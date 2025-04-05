@extends('front.layouts.root-layout')

@section('title', 'Appeal Ban')
@section('heading', 'Appeal Ban')
@section('description', 'Use the below form to submit a ban appeal')

@section('body')
    <x-front::navbar />

    <main class="bg-white py-20">
        <header class="max-w-screen-2xl mx-auto px-6">
            @if($errors->any())
                <div class="alert alert--error">
                    <h2><i class="fas fa-exclamation-circle"></i> Error</h2>
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="-mx-4 flex flex-wrap">
                <div class="w-full px-4">
                    <div class="mx-auto mb-[60px] max-w-[650px] text-center">
                        <a href="{{ route('front.appeal') }}" class="flex justify-center items-center gap-2 text-sm text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                            </svg>
                            Go Back
                        </a>

                        <h2 class="font-bold text-5xl mt-3">
                            Select the Ban
                        </h2>
                        <p class="text-base text-gray-500 mt-4 leading-relaxed">
                            Please search for the ban you wish to appeal. If you cannot find your ban you can still <a href="{{ route('front.appeal.form') }}" class="text-blue-500">fill out the form</a> manually.
                        </p>
                    </div>
                </div>
            </div>
        </header>

        @auth
        <section class="max-w-screen-lg mx-auto px-6">
            <h2 class="text-lg font-bold text-center">
                Your Active Bans
                <span class="font-normal text-gray-500 text-sm">
                    ({{ $active_bans->count() == 1 ? $active_bans->count() . ' record' : $active_bans->count() . ' records' }})
                </span>
            </h2>

            @if ($active_bans->count() > 0)
                @foreach ($active_bans as $ban)
                    <div class="rounded-lg border border-gray-200 mt-3 bg-gray-50 py-3 px-4 flex flex-row items-center gap-6 overflow-x-auto">
                        <img src="https://minotar.net/avatar/{{ $ban->bannedPlayer->uuid }}/32" class="rounded-md w-12 h-12">

                        <div class="mt-1 flex flex-col gap-1 flex-grow">
                            <strong class="text-2xl">{{ $ban->bannedPlayer->alias }}</strong>
                            <span class="text-gray-500 text-xs">UUID: {{ $ban->bannedPlayer->uuid }}</span>
                            <span class="text-sm">{{ $ban->created_at->format('jS \of M, Y - h:i A') }}</span>
                        </div>

                        <div class="px-4 py-3 flex flex-row gap-3">
                            <x-button size="sm" variant="outlined" href="{{ route('front.bans.details', $ban) }}">
                                View Details
                            </x-button>
                            <x-button size="sm" href="{{ route('front.appeal.form.prefilled', $ban) }}">
                                Select
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                    <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                </svg>
                            </x-button>
                        </div>
                    </div>
                @endforeach
            @else
            <div class="text-center text-gray-500 text-sm mt-2">
                No active bans found for any of your linked Minecraft accounts
            </div>
            @endif
        </section>
        @endauth

        <section class="max-w-screen-lg mx-auto px-6 mt-12">
            <h2 class="text-lg font-bold text-center">All Bans</h2>

            <div class="p-6 border-b border-gray-200">
                <form action="{{ route('front.appeal.search') }}" method="get">
                    <input
                        type="text"
                        class="rounded-md bg-gray-100 border-gray-200 text-sm"
                        name="query"
                        value="{{ $query }}"
                        placeholder="Search..."
                    >
                </form>
            </div>

            @if ($bans->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full overflow-x-auto mt-3">
                        <thead>
                        <tr class="text-left text-sm text-gray-500 bg-gray-100">
                            <th class="py-4 px-2">Player Name</th>
                            <th class="py-4 px-2">Reason</th>
                            <th class="py-4 px-2">Ban Date</th>
                            <th class="py-4 px-2"></th>
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
                                <td class="py-4 px-2 flex flex-row gap-3 justify-end">
                                    <x-button size="sm" variant="outlined" href="{{ route('front.bans.details', $ban) }}">
                                        View Details
                                    </x-button>
                                    <x-button size="sm" href="{{ route('front.appeal.form.prefilled', $ban) }}">
                                        Select
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                            <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                        </svg>
                                    </x-button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-6 border-t border-gray-200">
                    {{ $bans->links('vendor.pagination.default') }}
                </div>
            @else
                <div class="px-6 py-6 flex flex-col items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-12">
                        <path fill-rule="evenodd" d="M12.963 2.286a.75.75 0 0 0-1.071-.136 9.742 9.742 0 0 0-3.539 6.176 7.547 7.547 0 0 1-1.705-1.715.75.75 0 0 0-1.152-.082A9 9 0 1 0 15.68 4.534a7.46 7.46 0 0 1-2.717-2.248ZM15.75 14.25a3.75 3.75 0 1 1-7.313-1.172c.628.465 1.35.81 2.133 1a5.99 5.99 0 0 1 1.925-3.546 3.75 3.75 0 0 1 3.255 3.718Z" clip-rule="evenodd" />
                    </svg>
                    <h2 class="text-xl font-bold">No Results</h2>
                    <span class="text-gray-500">Please try a different search criteria</span>
                </div>
            @endif
        </section>
    </main>
@endsection
